<?php

declare(strict_types=1);

use App\Livewire\SectionTitle\Create;
use App\Livewire\SectionTitle\Edit;
use App\Models\SectionTitle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create();
});

it('stores an uploaded icon under a generated name, not the client filename', function () {
    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->set('title', 'Recycling')
        ->set('icon', UploadedFile::fake()->image('my icon.png'))
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('section-title.index'));

    $icon = SectionTitle::sole()->icon;

    expect($icon)->not->toBe('my icon.png')
        ->and($icon)->toEndWith('.png');
    Storage::disk('public')->assertExists('icons/'.$icon);
});

it('never keeps an executable extension from the client filename', function () {
    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->set('title', 'Polyglot')
        ->set('icon', UploadedFile::fake()->image('shell.php.png'))
        ->call('save')
        ->assertHasNoErrors();

    $icon = SectionTitle::sole()->icon;

    expect($icon)->not->toContain('php')
        ->and($icon)->not->toContain('shell');
});

it('rejects files that are not allowed image types', function (UploadedFile $file) {
    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->set('title', 'Bad upload')
        ->set('icon', $file)
        ->call('save')
        ->assertHasErrors('icon');

    expect(SectionTitle::count())->toBe(0);
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
})->with([
    'php script' => fn () => UploadedFile::fake()->create('shell.php', 1, 'application/x-php'),
    'svg' => fn () => UploadedFile::fake()->create('icon.svg', 1, 'image/svg+xml'),
    'html' => fn () => UploadedFile::fake()->create('page.html', 1, 'text/html'),
    'too large' => fn () => UploadedFile::fake()->image('big.png')->size(3000),
]);

it('requires an icon when creating', function () {
    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->set('title', 'No icon')
        ->call('save')
        ->assertHasErrors(['icon' => 'required']);
});

it('does not overwrite an existing icon when two uploads share a filename', function () {
    foreach (['First', 'Second'] as $title) {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('title', $title)
            ->set('icon', UploadedFile::fake()->image('same.png'))
            ->call('save')
            ->assertHasNoErrors();
    }

    $icons = SectionTitle::pluck('icon');

    expect($icons->unique())->toHaveCount(2);
    $icons->each(fn (string $icon) => Storage::disk('public')->assertExists('icons/'.$icon));
});

it('replaces the icon on edit and deletes the old file', function () {
    Storage::disk('public')->put('icons/old.png', 'old');
    $sectionTitle = SectionTitle::factory()->create(['icon' => 'old.png']);

    Livewire::actingAs($this->user)
        ->test(Edit::class, ['sectionTitle' => $sectionTitle])
        ->set('icon', UploadedFile::fake()->image('new.png'))
        ->call('save')
        ->assertHasNoErrors();

    $newIcon = $sectionTitle->fresh()->icon;

    expect($newIcon)->not->toBe('old.png');
    Storage::disk('public')->assertMissing('icons/old.png');
    Storage::disk('public')->assertExists('icons/'.$newIcon);
});

it('keeps the existing icon when editing without a new upload', function () {
    Storage::disk('public')->put('icons/old.png', 'old');
    $sectionTitle = SectionTitle::factory()->create(['icon' => 'old.png']);

    Livewire::actingAs($this->user)
        ->test(Edit::class, ['sectionTitle' => $sectionTitle])
        ->set('title', 'Renamed')
        ->call('save')
        ->assertHasNoErrors();

    expect($sectionTitle->fresh())
        ->title->toBe('Renamed')
        ->icon->toBe('old.png');
    Storage::disk('public')->assertExists('icons/old.png');
});

it('cannot delete files outside the icons directory via a stored icon name', function () {
    Storage::disk('public')->put('secret.txt', 'keep me');
    $sectionTitle = SectionTitle::factory()->create(['icon' => '../secret.txt']);

    Livewire::actingAs($this->user)
        ->test(Edit::class, ['sectionTitle' => $sectionTitle])
        ->set('icon', UploadedFile::fake()->image('new.png'))
        ->call('save')
        ->assertHasNoErrors();

    Storage::disk('public')->assertExists('secret.txt');
});

it('shows the upload progress bar only while uploading', function () {
    $sectionTitle = SectionTitle::factory()->create();

    $this->actingAs($this->user)
        ->get(route('section-title.edit', $sectionTitle))
        ->assertSuccessful()
        ->assertSee('x-on:livewire-upload-progress="progress = $event.detail.progress"', false)
        ->assertSee('x-show="uploading" x-cloak', false)
        ->assertDontSee('style="width: 100%"', false);
});
