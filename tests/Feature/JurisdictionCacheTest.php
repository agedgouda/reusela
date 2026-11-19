<?php

declare(strict_types=1);

use App\Models\Jurisdiction;
use App\Models\Section;
use App\Models\SectionTitle;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

/**
 * Laravel 13 no longer unserializes objects from the cache by default, so cached values
 * must be built only from scalars, nulls and arrays.
 */
function expectNoObjects(mixed $value): void
{
    if (is_array($value)) {
        foreach ($value as $item) {
            expectNoObjects($item);
        }

        return;
    }

    expect(is_object($value))->toBeFalse();
}

beforeEach(function () {
    Cache::flush();

    $this->master = Jurisdiction::factory()->create([
        'name' => 'Master',
        'is_system_default' => true,
        'general_information' => '<p>Default info</p>',
    ]);
});

it('caches the master record as plain attributes and rebuilds the model from them', function () {
    $record = (new Jurisdiction)->getDefaultRecord();

    expect($record)->toBeInstanceOf(Jurisdiction::class)
        ->and($record->id)->toBe($this->master->id)
        ->and($record->exists)->toBeTrue()
        ->and($record->general_information)->toBe('<p>Default info</p>');

    $cached = Cache::tags(['jurisdictions'])->get('jurisdiction_master_record_attributes');
    expect($cached)->toBeArray();
    expectNoObjects($cached);
});

it('serves the master record from cache on later calls', function () {
    (new Jurisdiction)->getDefaultRecord();

    Jurisdiction::withoutGlobalScopes()->whereKey($this->master->id)->toBase()->update(['name' => 'Renamed directly']);

    expect((new Jurisdiction)->getDefaultRecord()->name)->toBe('Master');
});

it('returns null when there is no master record', function () {
    $this->master->delete();

    expect((new Jurisdiction)->getDefaultRecord())->toBeNull();
});

it('falls back to cached master sections, sorted by title and with titles loaded', function () {
    $later = SectionTitle::factory()->create(['title' => 'Later', 'sort_order' => 2]);
    $first = SectionTitle::factory()->create(['title' => 'First', 'sort_order' => 1]);
    Section::factory()->for($this->master)->for($later)->create(['text' => '<p>Second</p>']);
    Section::factory()->for($this->master)->for($first)->create(['text' => '<p>First</p>']);

    $jurisdiction = Jurisdiction::factory()->create();

    $sections = $jurisdiction->display_sections;

    expect($sections)->toBeInstanceOf(EloquentCollection::class)
        ->and($sections->map(fn (Section $section) => $section->sectionTitle->title)->all())->toBe(['First', 'Later'])
        ->and($sections->every(fn (Section $section) => $section->exists && $section->relationLoaded('sectionTitle')))->toBeTrue();

    $cached = Cache::tags(['jurisdictions'])->get('master_section_rows');
    expect($cached)->toBeArray()->toHaveCount(2);
    expectNoObjects($cached);
});

it('uses its own sections instead of the master sections', function () {
    Section::factory()->for($this->master)->create();
    $jurisdiction = Jurisdiction::factory()->create();
    $own = Section::factory()->for($jurisdiction)->create();

    expect($jurisdiction->display_sections->pluck('id')->all())->toBe([$own->id]);
});

it('clears the cached values when a jurisdiction is saved', function () {
    (new Jurisdiction)->getDefaultRecord();

    $this->master->update(['name' => 'Renamed']);

    expect(Cache::tags(['jurisdictions'])->get('jurisdiction_master_record_attributes'))->toBeNull()
        ->and((new Jurisdiction)->getDefaultRecord()->name)->toBe('Renamed');
});
