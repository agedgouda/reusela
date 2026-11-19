<?php

declare(strict_types=1);

use App\Models\SectionTitle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses built-in wire:sort instead of loading SortableJS from a CDN', function () {
    $title = SectionTitle::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('section-title.index'))
        ->assertSuccessful()
        ->assertSee('wire:sort="moveTitle"', false)
        ->assertSee('wire:sort:item="'.$title->id.'"', false)
        ->assertSee('wire:sort:handle', false)
        ->assertDontSee('Sortable.min.js', false)
        ->assertDontSee('cdnjs.cloudflare.com', false);
});

it('reloads the page on an expired session using the Livewire 4 request interceptor', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('section-title.index'))
        ->assertSee('Livewire.interceptRequest', false)
        ->assertSee('response.status === 419', false)
        ->assertDontSee("Livewire.hook('request'", false);
});

it('does not ship Volt or livewire-sortable', function () {
    $composer = json_decode((string) file_get_contents(base_path('composer.json')), true);
    $package = json_decode((string) file_get_contents(base_path('package.json')), true);

    expect($composer['require'])->not->toHaveKey('livewire/volt')
        ->and($package['devDependencies'] ?? [])->not->toHaveKey('livewire-sortable')
        ->and($package['dependencies'])->not->toHaveKey('livewire-sortable');
});
