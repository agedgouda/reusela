<?php

declare(strict_types=1);

use App\Models\Jurisdiction;
use App\Models\SharedContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('sanitizes rich text when saved through the model', function () {
    $content = SharedContent::create([
        'key' => 'jurisdiction.show',
        'content' => '<p onclick="steal()">Hello</p><script>alert(1)</script>',
    ]);

    expect($content->fresh()->content)->toBe('<p>Hello</p>');
});

it('sanitizes jurisdiction general information when saved', function () {
    $jurisdiction = Jurisdiction::factory()->create([
        'general_information' => '<p>Info <a href="javascript:alert(1)">bad</a></p>',
    ]);

    expect($jurisdiction->fresh()->general_information)
        ->not->toContain('javascript')
        ->toContain('Info');
});

it('reports unsafe stored content on a dry run without changing it', function () {
    $content = SharedContent::create(['key' => 'jurisdiction.show', 'content' => '<p>placeholder</p>']);
    DB::table('shared_contents')->where('id', $content->id)->update(['content' => '<p>Hi</p><script>x()</script>']);

    $this->artisan('content:sanitize', ['--dry-run' => true])
        ->expectsOutputToContain('1 record(s) would change')
        ->assertSuccessful();

    expect(DB::table('shared_contents')->value('content'))->toContain('<script>');
});

it('sanitizes unsafe stored content and clears the shared content cache', function () {
    $content = SharedContent::create(['key' => 'jurisdiction.show', 'content' => '<p>placeholder</p>']);
    DB::table('shared_contents')->where('id', $content->id)->update(['content' => '<p>Hi</p><script>x()</script>']);
    cache()->forever('shared.jurisdiction.show', '<p>Hi</p><script>x()</script>');

    $this->artisan('content:sanitize')
        ->expectsOutputToContain('Sanitized 1 record(s)')
        ->assertSuccessful();

    expect(DB::table('shared_contents')->value('content'))->toBe('<p>Hi</p>')
        ->and(cache()->has('shared.jurisdiction.show'))->toBeFalse();
});

it('leaves already-clean content untouched', function () {
    SharedContent::create(['key' => 'jurisdiction.show', 'content' => '<p><strong>Fine</strong></p>']);

    $this->artisan('content:sanitize')
        ->expectsOutputToContain('Sanitized 0 record(s)')
        ->assertSuccessful();
});
