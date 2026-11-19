<?php

use App\Support\RichTextSanitizer;

it('removes script and dangerous elements', function (string $input, string $forbidden) {
    expect(RichTextSanitizer::clean($input))->not->toContain($forbidden);
})->with([
    'script tag' => ['<p>Hi</p><script>alert(1)</script>', '<script'],
    'iframe' => ['<iframe src="https://evil.test"></iframe>', '<iframe'],
    'object' => ['<object data="x.swf"></object>', '<object'],
    'style element' => ['<style>body{display:none}</style><p>x</p>', '<style'],
    'svg with script' => ['<svg><script>alert(1)</script></svg>', 'alert'],
    'img onerror' => ['<img src="x" onerror="alert(1)">', 'onerror'],
    'form' => ['<form action="https://evil.test"><input name="password"></form>', '<form'],
    'meta refresh' => ['<meta http-equiv="refresh" content="0;url=https://evil.test">', '<meta'],
]);

it('removes event handler attributes', function () {
    $output = RichTextSanitizer::clean('<p onclick="alert(1)" onmouseover="alert(2)">Text</p>');

    expect($output)->toBe('<p>Text</p>');
});

it('removes javascript and data links', function (string $href) {
    $output = RichTextSanitizer::clean('<a href="'.$href.'">Click</a>');

    expect($output)->not->toContain('href=')
        ->and($output)->toContain('Click');
})->with([
    'javascript' => 'javascript:alert(1)',
    'mixed case javascript' => 'JaVaScRiPt:alert(1)',
    'entity encoded javascript' => '&#106;avascript:alert(1)',
    'data uri' => 'data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==',
    'vbscript' => 'vbscript:msgbox(1)',
]);

it('keeps safe links and forces them to open in a new tab', function (string $href) {
    $output = html_entity_decode(RichTextSanitizer::clean('<a href="'.$href.'" target="_self" rel="opener">Link</a>'));

    expect($output)
        ->toContain('href="'.$href.'"')
        ->toContain('target="_blank"')
        ->toContain('rel="noopener noreferrer"')
        ->not->toContain('_self')
        ->not->toContain('rel="opener"');
})->with([
    'https' => 'https://example.com/page',
    'http' => 'http://example.com',
    'mailto' => 'mailto:info@example.com',
    'tel' => 'tel:+15555550100',
    'relative' => '/jurisdictions',
]);

it('keeps normal TinyMCE formatting', function (string $html) {
    expect(RichTextSanitizer::clean($html))->toBe($html);
})->with([
    'paragraph' => '<p>Plain text</p>',
    'headings' => '<h2>Title</h2><h3>Subtitle</h3>',
    'inline formatting' => '<p><strong>bold</strong> <em>italic</em> <u>under</u> <s>strike</s></p>',
    'lists' => '<ul><li>One</li><li>Two</li></ul><ol><li>First</li></ol>',
    'colors' => '<p><span style="color: #e03e2d;">red</span> <span style="background-color: rgb(251, 238, 184);">hl</span></p>',
    'underline span' => '<p><span style="text-decoration: underline;">underlined</span></p>',
    'alignment' => '<p style="text-align: center;">Centered</p>',
    'indent' => '<p style="padding-left: 40px;">Indented</p>',
    'table' => '<table style="border-collapse: collapse; width: 100%;" border="1"><tbody><tr><td style="width: 50%;">A</td><td colspan="2">B</td></tr></tbody></table>',
    'preformatted' => '<pre>code here</pre>',
    'line break' => '<p>Line one<br />Line two</p>',
    'nested list without outer bullet' => '<ul><li style="list-style-type: none;"><ul><li>Nested</li></ul></li></ul>',
    'bold list item' => '<ul><li style="font-weight: bold;"><p>Item</p></li></ul>',
]);

it('keeps the text of unknown wrapper elements while removing the tags', function () {
    $output = RichTextSanitizer::clean(
        '<div class="flex"><section class="w-full"><article><p>Keep me</p></article></section><font color="red">font text</font></div>'
    );

    expect($output)->toBe('<div><p>Keep me</p>font text</div>');
});

it('removes dangerous elements together with their contents', function () {
    $output = RichTextSanitizer::clean('<p>Safe</p><script>steal()</script><style>.x{}</style><iframe>frame</iframe><svg><text>svg</text></svg>');

    expect($output)->toBe('<p>Safe</p>');
});

it('removes attributes left by pasted content', function () {
    $output = RichTextSanitizer::clean('<ul data-start="1"><li dir="ltr" aria-level="1"><p dir="ltr" role="presentation"><strong id="docs-internal-guid-1" data-end="2">Item</strong></p></li></ul>');

    expect($output)->toBe('<ul><li><p><strong>Item</strong></p></li></ul>');
});

it('keeps multibyte characters intact', function () {
    $html = '<p>“Curly quotes” — café, naïve, 日本語</p>';

    expect(RichTextSanitizer::clean($html))->toBe($html);
});

it('strips unsafe style properties and values', function (string $style) {
    $output = RichTextSanitizer::clean('<p style="'.$style.'">Text</p>');

    expect($output)->toBe('<p>Text</p>');
})->with([
    'fixed overlay' => 'position: fixed; top: 0; left: 0; z-index: 9999',
    'background image' => 'background-image: url(https://evil.test/track.png)',
    'url in allowed property' => 'background-color: url(https://evil.test/x)',
    'expression' => 'width: expression(alert(1))',
    'css variable' => 'color: var(--x)',
    'escape sequences' => 'color: \\72 ed',
    'display none' => 'display: none',
]);

it('keeps safe style properties while dropping unsafe ones', function () {
    $output = RichTextSanitizer::clean('<p style="color: red; position: absolute; text-align: right;">Text</p>');

    expect($output)->toBe('<p style="color: red; text-align: right;">Text</p>');
});

it('removes class and id attributes', function () {
    $output = RichTextSanitizer::clean('<p class="fixed inset-0" id="login">Text</p>');

    expect($output)->toBe('<p>Text</p>');
});

it('does not truncate long content', function () {
    $html = str_repeat('<p>'.str_repeat('a', 990).'</p>', 50);

    expect(RichTextSanitizer::clean($html))->toBe($html);
});

it('passes through null and empty strings', function (?string $input) {
    expect(RichTextSanitizer::clean($input))->toBe($input);
})->with([
    'null' => null,
    'empty' => '',
    'whitespace' => '   ',
]);

it('is idempotent', function () {
    $once = RichTextSanitizer::clean('<p onclick="x">Hi <a href="https://example.com">link</a><script>bad()</script></p>');

    expect(RichTextSanitizer::clean($once))->toBe($once);
});
