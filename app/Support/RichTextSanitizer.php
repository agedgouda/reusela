<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Cleans TinyMCE HTML down to an allowlist of formatting elements before it is stored,
 * so content rendered with {!! !!} cannot run scripts. Links are forced to open in a new tab.
 */
class RichTextSanitizer
{
    /**
     * Elements the editor can produce, mapped to the attributes each may keep.
     *
     * @var array<string, list<string>>
     */
    private const ALLOWED_ELEMENTS = [
        'p' => ['style'],
        'div' => ['style'],
        'span' => ['style'],
        'br' => [],
        'hr' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'u' => [],
        's' => [],
        'sub' => [],
        'sup' => [],
        'h1' => ['style'],
        'h2' => ['style'],
        'h3' => ['style'],
        'h4' => ['style'],
        'h5' => ['style'],
        'h6' => ['style'],
        'pre' => [],
        'code' => [],
        'blockquote' => [],
        'ul' => ['style'],
        'ol' => ['style', 'start'],
        'li' => ['style'],
        'a' => ['href', 'title'],
        'table' => ['style', 'border'],
        'caption' => [],
        'colgroup' => [],
        'col' => ['style', 'span'],
        'thead' => [],
        'tbody' => [],
        'tfoot' => [],
        'tr' => ['style'],
        'th' => ['style', 'colspan', 'rowspan', 'scope'],
        'td' => ['style', 'colspan', 'rowspan'],
    ];

    /**
     * Harmless wrapper elements (often from content pasted out of other sites) whose
     * tag is removed but whose text is kept. Anything not listed here or above, such as
     * script, style, iframe or svg, is removed together with its contents.
     *
     * @var list<string>
     */
    private const UNWRAPPED_ELEMENTS = [
        'section', 'article', 'header', 'footer', 'main', 'aside', 'nav',
        'figure', 'figcaption', 'font', 'center', 'small', 'big', 'mark',
        'abbr', 'cite', 'q', 'dfn', 'time', 'address', 'label', 'button',
        'dl', 'dt', 'dd', 'details', 'summary', 'ins', 'del', 'strike', 'tt', 'kbd', 'samp', 'var',
    ];

    private static ?HtmlSanitizer $sanitizer = null;

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        return self::sanitizer()->sanitize($html);
    }

    private static function sanitizer(): HtmlSanitizer
    {
        if (self::$sanitizer) {
            return self::$sanitizer;
        }

        $config = (new HtmlSanitizerConfig)
            ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
            ->allowRelativeLinks()
            ->forceAttribute('a', 'target', '_blank')
            ->forceAttribute('a', 'rel', 'noopener noreferrer')
            ->withAttributeSanitizer(new StyleAttributeSanitizer)
            ->withMaxInputLength(-1);

        foreach (self::ALLOWED_ELEMENTS as $element => $attributes) {
            $config = $config->allowElement($element, $attributes);
        }

        foreach (self::UNWRAPPED_ELEMENTS as $element) {
            $config = $config->blockElement($element);
        }

        return self::$sanitizer = new HtmlSanitizer($config);
    }
}
