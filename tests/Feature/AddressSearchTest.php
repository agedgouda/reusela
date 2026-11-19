<?php

declare(strict_types=1);

use App\Livewire\Search\Index;
use App\Models\Jurisdiction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/**
 * @return array{location: string, locality: string, state: string, postal_code: string}
 */
function searchAddress(array $overrides = []): array
{
    return array_merge([
        'location' => '100 Main St',
        'locality' => 'Burbank',
        'state' => 'CA',
        'postal_code' => '91502',
    ], $overrides);
}

function fakeCivicApi(bool $inCity = true): void
{
    Http::fake([
        'www.googleapis.com/civicinfo/*' => Http::response([
            'divisions' => $inCity
                ? ['ocd-division/country:us/state:ca/place:burbank' => []]
                : ['ocd-division/country:us/state:ca/county:los_angeles' => []],
        ]),
    ]);
}

beforeEach(function () {
    RateLimiter::clear('address-lookup:127.0.0.1');
});

it('is publicly accessible', function () {
    $this->get(route('search.index'))->assertSuccessful();
});

it('finds the jurisdiction for an address in a city', function () {
    fakeCivicApi();
    $jurisdiction = Jurisdiction::factory()->create(['name' => 'Burbank']);

    Livewire::test(Index::class)
        ->call('addressSelected', searchAddress())
        ->assertHasNoErrors()
        ->assertSet('city', 'Burbank')
        ->assertSet('address', 'City of Burbank')
        ->assertSet('jurisdiction.id', $jurisdiction->id);
});

it('falls back to unincorporated LA County when the address is not in a city', function () {
    fakeCivicApi(inCity: false);

    Livewire::test(Index::class)
        ->call('addressSelected', searchAddress(['locality' => 'Altadena']))
        ->assertSet('city', 'Unincorporated')
        ->assertSet('address', 'Unincorporated LA County');
});

it('rejects invalid or out-of-state addresses without calling Google', function (array $address) {
    Http::fake();

    Livewire::test(Index::class)
        ->call('addressSelected', $address)
        ->assertHasErrors('address')
        ->assertDispatched('error-processing')
        ->assertSet('jurisdiction', null);

    Http::assertNothingSent();
})->with([
    'out of state' => [searchAddress(['state' => 'NV'])],
    'missing location' => [searchAddress(['location' => ' '])],
    'missing locality' => [searchAddress(['locality' => ''])],
    'non-string location' => [searchAddress(['location' => ['nested' => 'array']])],
    'overlong location' => [searchAddress(['location' => str_repeat('a', 201)])],
    'bad postal code' => [searchAddress(['postal_code' => '<script>'])],
    'missing keys' => [[]],
]);

it('accepts a missing postal code', function () {
    fakeCivicApi();

    Livewire::test(Index::class)
        ->call('addressSelected', searchAddress(['postal_code' => '']))
        ->assertHasNoErrors();

    Http::assertSentCount(1);
});

it('rate limits lookups per IP address', function () {
    fakeCivicApi();
    $component = Livewire::test(Index::class);

    foreach (range(1, Index::LOOKUPS_PER_MINUTE) as $attempt) {
        $component->call('addressSelected', searchAddress(['location' => "{$attempt} Main St"]))
            ->assertHasNoErrors();
    }

    $component->call('addressSelected', searchAddress(['location' => '999 Main St']))
        ->assertHasErrors('address')
        ->assertSee('Too many searches');

    Http::assertSentCount(Index::LOOKUPS_PER_MINUTE);
});

it('serves repeated lookups of the same address from cache', function () {
    fakeCivicApi();
    $component = Livewire::test(Index::class);

    $component->call('addressSelected', searchAddress());
    $component->call('addressSelected', searchAddress());

    Http::assertSentCount(1);
});

it('sends the google api key to the civic api', function () {
    config(['services.google.api_key' => 'server-key']);
    fakeCivicApi();

    Livewire::test(Index::class)->call('addressSelected', searchAddress());

    Http::assertSent(fn ($request) => $request['key'] === 'server-key');
});

it('fails the lookup without calling Google when no api key is configured', function () {
    config(['services.google.api_key' => null]);
    fakeCivicApi();

    Livewire::test(Index::class)
        ->call('addressSelected', searchAddress())
        ->assertHasErrors('address')
        ->assertSet('jurisdiction', null);

    Http::assertNothingSent();
});

it('waits 200ms after typing before asking for suggestions', function () {
    $this->get(route('search.index'))
        ->assertSuccessful()
        ->assertSee('@input.debounce.200ms', false);
});

it('never puts the google api key in the page', function () {
    config(['services.google.api_key' => 'secret-server-key']);

    $this->get(route('search.index'))
        ->assertSuccessful()
        ->assertDontSee('secret-server-key')
        ->assertDontSee('maps.googleapis.com');
});

describe('server-side address autocomplete endpoint', function () {
    beforeEach(function () {
        RateLimiter::clear(md5('address-suggestions127.0.0.1'));
        Cache::flush();
        config(['services.google.api_key' => 'server-key']);
    });

    it('returns suggestions from the places api using the key in a header', function () {
        Http::fake([
            'places.googleapis.com/v1/places:autocomplete' => Http::response([
                'suggestions' => [
                    ['placePrediction' => ['placeId' => 'ChIJabc_123-x', 'text' => ['text' => '100 Main St, Burbank, CA']]],
                    ['queryPrediction' => ['text' => ['text' => 'not a place']]],
                ],
            ]),
        ]);

        $this->getJson(route('search.suggestions', ['query' => '100 Main']))
            ->assertSuccessful()
            ->assertExactJson([['id' => 'ChIJabc_123-x', 'label' => '100 Main St, Burbank, CA']]);

        Http::assertSent(fn ($request) => $request->hasHeader('X-Goog-Api-Key', 'server-key')
            && $request['input'] === '100 Main'
            && Str::isUuid($request['sessionToken'])
            && ! str_contains($request->url(), 'server-key'));
    });

    it('is public and does not need a login', function () {
        Http::fake(['places.googleapis.com/*' => Http::response(['suggestions' => []])]);

        $this->assertGuest()
            ->getJson(route('search.suggestions', ['query' => '100 Main']))
            ->assertSuccessful();
    });

    it('answers repeated search terms from cache', function () {
        Http::fake(['places.googleapis.com/*' => Http::response([
            'suggestions' => [['placePrediction' => ['placeId' => 'abc', 'text' => ['text' => '1 A St']]]],
        ])]);

        foreach (['1 A St', '1 a st', '  1  A   St '] as $query) {
            $this->getJson(route('search.suggestions', ['query' => $query]))
                ->assertExactJson([['id' => 'abc', 'label' => '1 A St']]);
        }

        Http::assertSentCount(1);
    });

    it('does not cache failures', function () {
        Http::fakeSequence('places.googleapis.com/*')
            ->push([], 500)
            ->push(['suggestions' => [['placePrediction' => ['placeId' => 'abc', 'text' => ['text' => '1 A St']]]]]);

        $this->getJson(route('search.suggestions', ['query' => '1 A St']))->assertExactJson([]);
        $this->getJson(route('search.suggestions', ['query' => '1 A St']))->assertExactJson([['id' => 'abc', 'label' => '1 A St']]);
    });

    it('rejects queries that are missing, too short or too long', function (array $parameters) {
        Http::fake();

        $this->getJson(route('search.suggestions', $parameters))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('query');

        Http::assertNothingSent();
    })->with([
        'missing' => [[]],
        'too short' => [['query' => 'ab']],
        'too long' => [['query' => str_repeat('a', 201)]],
        'array' => [['query' => ['100 Main']]],
    ]);

    it('rate limits suggestions per IP address', function () {
        Http::fake(['places.googleapis.com/*' => Http::response(['suggestions' => []])]);

        foreach (range(1, 60) as $attempt) {
            $this->getJson(route('search.suggestions', ['query' => "Main {$attempt}"]))->assertSuccessful();
        }

        $this->getJson(route('search.suggestions', ['query' => 'Main 61']))->assertTooManyRequests();
    });

    it('returns no suggestions without calling Google when no api key is configured', function () {
        config(['services.google.api_key' => null]);
        Http::fake();

        $this->getJson(route('search.suggestions', ['query' => '100 Main']))->assertExactJson([]);

        Http::assertNothingSent();
    });
});

describe('selecting a suggested place', function () {
    beforeEach(function () {
        config(['services.google.api_key' => 'server-key']);
    });

    it('looks up the address server-side and finds the jurisdiction', function () {
        Http::fake([
            'places.googleapis.com/v1/places/*' => Http::response([
                'addressComponents' => [
                    ['longText' => '100', 'shortText' => '100', 'types' => ['street_number']],
                    ['longText' => 'Main Street', 'shortText' => 'Main St', 'types' => ['route']],
                    ['longText' => 'Burbank', 'shortText' => 'Burbank', 'types' => ['locality', 'political']],
                    ['longText' => 'California', 'shortText' => 'CA', 'types' => ['administrative_area_level_1', 'political']],
                    ['longText' => '91502', 'shortText' => '91502', 'types' => ['postal_code']],
                ],
            ]),
            'www.googleapis.com/civicinfo/*' => Http::response([
                'divisions' => ['ocd-division/country:us/state:ca/place:burbank' => []],
            ]),
        ]);
        $jurisdiction = Jurisdiction::factory()->create(['name' => 'Burbank']);

        session(['google_places_session_token' => $firstSessionToken = (string) Str::uuid()]);

        Livewire::test(Index::class)
            ->call('selectPlace', 'ChIJabc123')
            ->assertHasNoErrors()
            ->assertSet('jurisdiction.id', $jurisdiction->id);

        Http::assertSent(fn ($request) => str_starts_with($request->url(), 'https://places.googleapis.com/v1/places/ChIJabc123')
            && $request->hasHeader('X-Goog-Api-Key', 'server-key')
            && $request->hasHeader('X-Goog-FieldMask', 'addressComponents')
            && $request['sessionToken'] === $firstSessionToken);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'civicinfo') && $request['address'] === '100 Main Street, Burbank, CA 91502');
        expect(session('google_places_session_token'))->toBeNull();
    });

    it('rejects malformed place ids without calling Google', function (string $placeId) {
        Http::fake();

        Livewire::test(Index::class)
            ->call('selectPlace', $placeId)
            ->assertHasErrors('address');

        Http::assertNothingSent();
    })->with([
        'path traversal' => '../places:autocomplete',
        'query string' => 'abc?key=x',
        'empty' => '',
    ]);

    it('shows an error when the places lookup fails', function () {
        Http::fake(['places.googleapis.com/*' => Http::response([], 500)]);

        Livewire::test(Index::class)
            ->call('selectPlace', 'ChIJabc123')
            ->assertHasErrors('address')
            ->assertSet('jurisdiction', null);
    });
});

it('caches the jurisdiction id rather than the model', function () {
    fakeCivicApi();
    $jurisdiction = Jurisdiction::factory()->create(['name' => 'Burbank']);

    Livewire::test(Index::class)->call('addressSelected', searchAddress());

    $cached = Cache::tags(['jurisdictions'])->get('civic_lookup_v2_'.Str::slug('100 Main St, Burbank, CA 91502'));

    expect($cached)->toBe([
        'city' => 'Burbank',
        'address_label' => 'City of Burbank',
        'jurisdiction_id' => $jurisdiction->id,
    ]);
});

it('shows no jurisdiction when the cached city has none', function () {
    fakeCivicApi();

    Livewire::test(Index::class)
        ->call('addressSelected', searchAddress())
        ->assertSet('city', 'Burbank')
        ->assertSet('jurisdiction', null);
});

it('resets the result when the address is cleared', function () {
    fakeCivicApi();
    Jurisdiction::factory()->create(['name' => 'Burbank']);

    Livewire::test(Index::class)
        ->call('addressSelected', searchAddress())
        ->call('addressSelected', null)
        ->assertSet('address', null)
        ->assertSet('city', null)
        ->assertSet('jurisdiction', null);
});

it('reports an error when Google fails', function () {
    Http::fake(fn () => throw new ConnectionException('timeout'));

    Livewire::test(Index::class)
        ->call('addressSelected', searchAddress())
        ->assertDispatched('error-processing')
        ->assertSet('jurisdiction', null);
});

it('does not let the browser overwrite result properties', function (string $property) {
    Livewire::test(Index::class)->set($property, 'tampered');
})->with(['address', 'city', 'jurisdiction', 'isProcessing'])
    ->throws(CannotUpdateLockedPropertyException::class);
