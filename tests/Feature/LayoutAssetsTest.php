<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('loads SortableJS from the CDN only with a pinned integrity hash', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('section-title.index'))
        ->assertSuccessful()
        ->assertSee('src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js" integrity="sha512-Eezs+g9Lq4TCCq0wae01s9PuNWzHYoCMkE97e2qdkYthpI0pzC3UGB03lgEHn2XM85hDOUF6qgqqszs+iXU4UA==" crossorigin="anonymous"', false);
});

it('does not ship Volt or livewire-sortable', function () {
    $composer = json_decode((string) file_get_contents(base_path('composer.json')), true);
    $package = json_decode((string) file_get_contents(base_path('package.json')), true);

    expect($composer['require'])->not->toHaveKey('livewire/volt')
        ->and($package['devDependencies'] ?? [])->not->toHaveKey('livewire-sortable')
        ->and($package['dependencies'])->not->toHaveKey('livewire-sortable');
});
