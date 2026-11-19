<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Server-side client for the Google Places API (New), so the API key never reaches the browser.
 */
class GooglePlaces
{
    /**
     * Rectangle that address suggestions are restricted to (Los Angeles County).
     *
     * @var array{low: array{latitude: float, longitude: float}, high: array{latitude: float, longitude: float}}
     */
    public const SEARCH_AREA = [
        'low' => ['latitude' => 33.7037, 'longitude' => -118.9448],
        'high' => ['latitude' => 34.8233, 'longitude' => -117.6458],
    ];

    public const PLACE_ID_PATTERN = '/^[A-Za-z0-9_-]{1,512}$/';

    /**
     * How long identical search terms are answered from cache instead of Google.
     */
    public const SUGGESTION_CACHE_SECONDS = 600;

    private const SESSION_TOKEN_KEY = 'google_places_session_token';

    public function __construct(private string $apiKey) {}

    /**
     * The single server-only Google key, or null (and a reported error) when it is missing.
     */
    public static function configuredApiKey(): ?string
    {
        $apiKey = config('services.google.api_key');

        if (! is_string($apiKey) || $apiKey === '') {
            report(new RuntimeException('GOOGLE_MAPS_API_KEY is not configured.'));

            return null;
        }

        return $apiKey;
    }

    /**
     * The visitor's current Places session token, which groups their autocomplete
     * requests and the final place lookup into one billed session.
     */
    public static function sessionToken(): string
    {
        $token = session(self::SESSION_TOKEN_KEY);

        if (! is_string($token) || ! Str::isUuid($token)) {
            $token = (string) Str::uuid();
            session([self::SESSION_TOKEN_KEY => $token]);
        }

        return $token;
    }

    /**
     * Start a new Places session after a place has been looked up.
     */
    public static function rotateSessionToken(): void
    {
        session()->forget(self::SESSION_TOKEN_KEY);
    }

    /**
     * @return list<array{id: string, label: string}>
     */
    public function suggestAddresses(string $input, string $sessionToken): array
    {
        $cacheKey = 'places-suggestions:'.sha1(Str::lower(Str::squish($input)));

        /** @var list<array{id: string, label: string}> */
        return Cache::remember($cacheKey, self::SUGGESTION_CACHE_SECONDS, fn (): array => $this->fetchSuggestions($input, $sessionToken));
    }

    /**
     * @return list<array{id: string, label: string}>
     */
    private function fetchSuggestions(string $input, string $sessionToken): array
    {
        $response = Http::timeout(5)
            ->withHeaders(['X-Goog-Api-Key' => $this->apiKey])
            ->post('https://places.googleapis.com/v1/places:autocomplete', [
                'input' => $input,
                'sessionToken' => $sessionToken,
                'includedRegionCodes' => ['us'],
                'locationRestriction' => ['rectangle' => self::SEARCH_AREA],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Places autocomplete failed with status '.$response->status());
        }

        $suggestions = [];

        foreach ((array) $response->json('suggestions', []) as $suggestion) {
            $placeId = data_get($suggestion, 'placePrediction.placeId');
            $label = data_get($suggestion, 'placePrediction.text.text');

            if (is_string($placeId) && is_string($label) && preg_match(self::PLACE_ID_PATTERN, $placeId)) {
                $suggestions[] = ['id' => $placeId, 'label' => $label];
            }
        }

        return $suggestions;
    }

    /**
     * @return array{location: string, locality: string, state: string, postal_code: string}
     */
    public function addressFor(string $placeId, string $sessionToken): array
    {
        if (! preg_match(self::PLACE_ID_PATTERN, $placeId)) {
            throw new RuntimeException('Invalid place id.');
        }

        $response = Http::timeout(5)
            ->withHeaders([
                'X-Goog-Api-Key' => $this->apiKey,
                'X-Goog-FieldMask' => 'addressComponents',
            ])
            ->get("https://places.googleapis.com/v1/places/{$placeId}", [
                'sessionToken' => $sessionToken,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Places details failed with status '.$response->status());
        }

        $components = collect((array) $response->json('addressComponents', []));

        $component = function (string $type, bool $short = false) use ($components): string {
            $match = $components->first(fn (mixed $c): bool => in_array($type, (array) data_get($c, 'types', []), true));
            $text = data_get($match, $short ? 'shortText' : 'longText');

            return is_string($text) ? $text : '';
        };

        return [
            'location' => trim($component('street_number').' '.$component('route')),
            'locality' => $component('locality'),
            'state' => $component('administrative_area_level_1', true),
            'postal_code' => $component('postal_code'),
        ];
    }
}
