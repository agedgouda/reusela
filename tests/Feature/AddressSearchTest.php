<?php

declare(strict_types=1);

use App\Livewire\Search\Index;
use App\Models\Jurisdiction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
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

it('uses the server-side civic api key', function () {
    config(['services.google_civic.api_key' => 'server-key', 'services.google_maps.api_key' => 'browser-key']);
    fakeCivicApi();

    Livewire::test(Index::class)->call('addressSelected', searchAddress());

    Http::assertSent(fn ($request) => $request['key'] === 'server-key');
});

it('never sends the browser maps key to the civic api', function () {
    config(['services.google_civic.api_key' => null, 'services.google_maps.api_key' => 'browser-key']);
    fakeCivicApi();

    Livewire::test(Index::class)
        ->call('addressSelected', searchAddress())
        ->assertHasErrors('address')
        ->assertSet('jurisdiction', null);

    Http::assertNothingSent();
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
