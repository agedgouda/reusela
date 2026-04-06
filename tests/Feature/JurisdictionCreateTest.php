<?php

declare(strict_types=1);

use App\Livewire\Jurisdiction\Index;
use App\Livewire\Jurisdiction\Show;
use App\Models\County;
use App\Models\Jurisdiction;
use App\Models\User;
use Livewire\Livewire;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('requires a name before creating a jurisdiction', function () {
    $user = User::factory()->create();
    County::factory()->create(['name' => 'Los Angeles County']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('newJurisdictionName', '')
        ->call('createJurisdiction')
        ->assertHasErrors(['newJurisdictionName' => 'required']);

    expect(Jurisdiction::count())->toBe(0);
});

it('creates a jurisdiction with the given name and redirects', function () {
    $user = User::factory()->create();
    County::factory()->create(['name' => 'Los Angeles County']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('newJurisdictionName', 'Beverly Hills')
        ->call('createJurisdiction')
        ->assertHasNoErrors()
        ->assertRedirect();

    $jurisdiction = Jurisdiction::where('name', 'Beverly Hills')->first();
    expect($jurisdiction)->not->toBeNull();
    expect($jurisdiction->county->name)->toBe('Los Angeles County');
});

it('saves the jurisdiction name in title case', function () {
    $user = User::factory()->create();
    County::factory()->create(['name' => 'Los Angeles County']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('newJurisdictionName', 'beverly hills')
        ->call('createJurisdiction')
        ->assertHasNoErrors();

    expect(Jurisdiction::where('name', 'Beverly Hills')->exists())->toBeTrue();
});

it('cancel resets the name field and clears errors', function () {
    $user = User::factory()->create();
    County::factory()->create(['name' => 'Los Angeles County']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('newJurisdictionName', 'partial input')
        ->call('cancelCreate')
        ->assertSet('newJurisdictionName', '');

    expect(Jurisdiction::count())->toBe(0);
});

it('rejects a duplicate jurisdiction name on create', function () {
    $user = User::factory()->create();
    County::factory()->create(['name' => 'Los Angeles County']);
    Jurisdiction::factory()->create(['name' => 'Beverly Hills']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('newJurisdictionName', 'Beverly Hills')
        ->call('createJurisdiction')
        ->assertHasErrors(['newJurisdictionName' => 'unique']);

    expect(Jurisdiction::where('name', 'Beverly Hills')->count())->toBe(1);
});

it('does not show name input when jurisdiction already has a name', function () {
    $user = User::factory()->create();
    $jurisdiction = Jurisdiction::factory()->create(['name' => 'Pasadena']);

    Livewire::actingAs($user)
        ->test(Show::class, ['jurisdiction' => $jurisdiction])
        ->assertSet('editingName', false);
});
