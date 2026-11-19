<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface;

/**
 * Reduces inline style attributes to the formatting properties TinyMCE produces,
 * so stored content cannot position elements over the page or load remote resources.
 */
class StyleAttributeSanitizer implements AttributeSanitizerInterface
{
    /**
     * @var list<string>
     */
    public const ALLOWED_PROPERTIES = [
        'color',
        'background-color',
        'text-decoration',
        'text-align',
        'font-weight',
        'font-style',
        'padding-left',
        'list-style-type',
        'width',
        'height',
        'border',
        'border-width',
        'border-style',
        'border-color',
        'border-collapse',
    ];

    public function getSupportedElements(): ?array
    {
        return null;
    }

    public function getSupportedAttributes(): ?array
    {
        return ['style'];
    }

    public function sanitizeAttribute(string $element, string $attribute, string $value, HtmlSanitizerConfig $config): ?string
    {
        $declarations = [];

        foreach (explode(';', $value) as $declaration) {
            if (! str_contains($declaration, ':')) {
                continue;
            }

            [$property, $propertyValue] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);

            if (in_array($property, self::ALLOWED_PROPERTIES, true) && $this->isSafeValue($propertyValue)) {
                $declarations[] = "{$property}: {$propertyValue}";
            }
        }

        return $declarations === [] ? null : implode('; ', $declarations).';';
    }

    private function isSafeValue(string $value): bool
    {
        if ($value === '' || preg_match('/url\s*\(|expression\s*\(|var\s*\(|image-set|@import/i', $value)) {
            return false;
        }

        return (bool) preg_match('/^[a-zA-Z0-9#%.,()\s-]+$/', $value);
    }
}
