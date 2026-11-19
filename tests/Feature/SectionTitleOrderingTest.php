<?php

declare(strict_types=1);

use App\Livewire\SectionTitle\Index;
use App\Models\SectionTitle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->titles = collect(['A', 'B', 'C', 'D'])->map(
        fn (string $title, int $index) => SectionTitle::factory()->create(['title' => $title, 'sort_order' => $index + 1])
    );
});

/**
 * @return list<string>
 */
function titleOrder(): array
{
    return SectionTitle::query()->orderBy('sort_order')->pluck('title')->all();
}

it('moves a title down to a new position', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(Index::class)
        ->call('moveTitle', $this->titles[0]->id, 2)
        ->assertSet('titles.2.title', 'A');

    expect(titleOrder())->toBe(['B', 'C', 'A', 'D'])
        ->and(SectionTitle::query()->orderBy('sort_order')->pluck('sort_order')->all())->toBe([1, 2, 3, 4]);
});

it('moves a title up to the first position', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(Index::class)
        ->call('moveTitle', (string) $this->titles[3]->id, 0);

    expect(titleOrder())->toBe(['D', 'A', 'B', 'C']);
});

it('clamps positions outside the list', function (int $position, array $expected) {
    Livewire::actingAs(User::factory()->create())
        ->test(Index::class)
        ->call('moveTitle', $this->titles[1]->id, $position);

    expect(titleOrder())->toBe($expected);
})->with([
    'past the end' => [99, ['A', 'C', 'D', 'B']],
    'negative' => [-5, ['B', 'A', 'C', 'D']],
]);

it('ignores unknown titles', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(Index::class)
        ->call('moveTitle', 999999, 0);

    expect(titleOrder())->toBe(['A', 'B', 'C', 'D']);
});

it('no longer exposes the old unvalidated updateSortOrder action', function () {
    expect(method_exists(Index::class, 'updateSortOrder'))->toBeFalse();
});

it('requires login to view the ordering page', function () {
    $this->get(route('section-title.index'))->assertRedirect(route('login'));
});
