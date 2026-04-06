<?php

declare(strict_types=1);

use App\Support\HtmlLinks;

it('adds target and rel to a plain anchor tag', function () {
    $input = '<p><a href="https://example.com">Example</a></p>';
    $output = HtmlLinks::ensureNewTab($input);

    expect($output)
        ->toContain('target="_blank"')
        ->toContain('rel="noopener noreferrer"');
});

it('overwrites an existing target attribute', function () {
    $input = '<a href="https://example.com" target="_self">Example</a>';
    $output = HtmlLinks::ensureNewTab($input);

    expect($output)
        ->toContain('target="_blank"')
        ->not->toContain('target="_self"');
});

it('overwrites an existing rel attribute', function () {
    $input = '<a href="https://example.com" rel="nofollow">Example</a>';
    $output = HtmlLinks::ensureNewTab($input);

    expect($output)->toContain('rel="noopener noreferrer"');
});

it('handles multiple anchor tags', function () {
    $input = '<a href="https://one.com">One</a> and <a href="https://two.com">Two</a>';
    $output = HtmlLinks::ensureNewTab($input);

    expect(substr_count($output, 'target="_blank"'))->toBe(2);
    expect(substr_count($output, 'rel="noopener noreferrer"'))->toBe(2);
});

it('returns html with no anchors unchanged in structure', function () {
    $input = '<p>No links here.</p>';
    $output = HtmlLinks::ensureNewTab($input);

    expect($output)
        ->toContain('No links here.')
        ->not->toContain('target=');
});

it('returns an empty string unchanged', function () {
    expect(HtmlLinks::ensureNewTab(''))->toBe('');
    expect(HtmlLinks::ensureNewTab('   '))->toBe('   ');
});

it('preserves utf-8 multibyte characters like curly quotes', function () {
    $input = "<p>It\u{2019}s a <a href=\"https://example.com\">link</a>.</p>";
    $output = HtmlLinks::ensureNewTab($input);

    expect($output)
        ->toContain("\u{2019}")
        ->not->toContain('â€™');
});
