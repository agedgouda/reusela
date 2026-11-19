<?php

namespace App\Livewire\Search;

use App\Models\Jurisdiction;
use App\Support\GooglePlaces;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    /**
     * Address lookups allowed per IP address per minute.
     */
    public const LOOKUPS_PER_MINUTE = 10;

    #[Locked]
    public ?string $address = null;

    #[Locked]
    public ?string $city = null;

    #[Locked]
    public ?Jurisdiction $jurisdiction = null;

    #[Locked]
    public bool $isProcessing = false;

    /**
     * Look up the chosen suggestion's address server-side, then find its jurisdiction.
     */
    public function selectPlace(string $placeId): void
    {
        $this->resetErrorBag();

        if (! preg_match(GooglePlaces::PLACE_ID_PATTERN, $placeId)) {
            $this->failLookup('Please choose an address from the suggestions.');

            return;
        }

        $apiKey = GooglePlaces::configuredApiKey();

        if ($apiKey === null) {
            $this->failLookup('Address lookup is temporarily unavailable. Please try again later.');

            return;
        }

        try {
            $address = (new GooglePlaces($apiKey))->addressFor($placeId, GooglePlaces::sessionToken());
        } catch (\Exception $e) {
            report($e);
            $this->failLookup('Address lookup is temporarily unavailable. Please try again later.');

            return;
        } finally {
            GooglePlaces::rotateSessionToken();
        }

        $this->addressSelected($address);
    }

    /**
     * @param  array{location?: mixed, locality?: mixed, state?: mixed, postal_code?: mixed}|null  $address
     */
    #[On('address-updated')]
    public function addressSelected(?array $address): void
    {
        $this->resetErrorBag();

        if (is_null($address)) {
            $this->reset(['address', 'city', 'jurisdiction', 'isProcessing']);

            return;
        }

        $validator = Validator::make($address, [
            'location' => ['required', 'string', 'max:200'],
            'locality' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'in:CA'],
            'postal_code' => ['nullable', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
        ]);

        if ($validator->fails()) {
            $this->failLookup('Please choose a California street address from the suggestions.');

            return;
        }

        $civicApiKey = GooglePlaces::configuredApiKey();

        if ($civicApiKey === null) {
            $this->failLookup('Address lookup is temporarily unavailable. Please try again later.');

            return;
        }

        $rateLimitKey = 'address-lookup:'.request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, self::LOOKUPS_PER_MINUTE)) {
            $this->failLookup('Too many searches. Please wait a minute and try again.');

            return;
        }

        RateLimiter::hit($rateLimitKey, 60);

        $this->isProcessing = true;
        $address = $validator->validated();

        try {
            $formattedAddress = sprintf('%s, %s, %s %s',
                $address['location'], $address['locality'], $address['state'], $address['postal_code'] ?? ''
            );

            $cacheKey = 'civic_lookup_'.Str::slug($formattedAddress);

            /**
             * We use Cache::tags(['jurisdictions']) so that this entire result
             * can be cleared whenever a Jurisdiction model is updated.
             */
            $data = Cache::tags(['jurisdictions'])->remember($cacheKey, now()->addDays(7), function () use ($formattedAddress, $address, $civicApiKey) {
                $response = Http::timeout(5)->get('https://www.googleapis.com/civicinfo/v2/divisionsByAddress', [
                    'address' => $formattedAddress,
                    'key' => $civicApiKey,
                ]);

                if ($response->successful()) {
                    $divisions = $response->json('divisions');
                    $hasPlace = is_array($divisions) && collect(array_keys($divisions))
                        ->contains(fn (int|string $division) => str_contains((string) $division, 'place'));

                    $city = $hasPlace ? $address['locality'] : 'Unincorporated';

                    // We fetch the model inside the cache closure so the
                    // entire object is stored in Redis.
                    $model = Jurisdiction::where('name', $city)->first();

                    return [
                        'city' => $city,
                        'address_label' => $hasPlace ? "City of {$city}" : 'Unincorporated LA County',
                        'jurisdiction' => $model,
                    ];
                }

                return null;
            });

            if ($data) {
                $this->city = is_string($data['city']) ? $data['city'] : null;
                $this->address = $data['address_label'];
                $this->jurisdiction = $data['jurisdiction'];
            }

        } catch (\Exception $e) {
            // Log the error for debugging on Forge
            logger()->error('Search Error: '.$e->getMessage());
            $this->dispatch('error-processing');
        }

        $this->isProcessing = false;
    }

    private function failLookup(string $message): void
    {
        $this->reset(['address', 'city', 'jurisdiction', 'isProcessing']);
        $this->addError('address', $message);
        $this->dispatch('error-processing');
    }

    public function render(): View
    {
        return view('livewire.search.index');
    }
}
