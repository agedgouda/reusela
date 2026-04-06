<?php

namespace App\Support;

use DOMDocument;

class HtmlLinks
{
    /**
     * Ensure all anchor tags in the given HTML open in a new tab.
     * Adds target="_blank" and rel="noopener noreferrer" to every <a> tag,
     * overwriting any existing target or rel attributes.
     */
    public static function ensureNewTab(string $html): string
    {
        if (trim($html) === '') {
            return $html;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        // The meta charset tag forces DOMDocument to treat content as UTF-8,
        // preventing it from mangling multibyte characters like curly quotes.
        $dom->loadHTML(
            '<meta charset="UTF-8"><div>'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();

        foreach ($dom->getElementsByTagName('a') as $link) {
            $link->setAttribute('target', '_blank');
            $link->setAttribute('rel', 'noopener noreferrer');
        }

        // Extract innerHTML of the wrapper div
        $wrapper = $dom->getElementsByTagName('div')->item(0);
        $result = '';

        foreach ($wrapper->childNodes as $child) {
            $result .= $dom->saveHTML($child);
        }

        return $result;
    }
}
