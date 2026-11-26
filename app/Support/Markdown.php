<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Markdown for pages and news (docs/DECISIONS.md): the framework's converter,
 * with raw HTML shown as text and unsafe links (javascript: and the like) removed.
 */
final class Markdown
{
    public static function toHtml(?string $markdown): HtmlString
    {
        $html = Str::markdown((string) $markdown, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 20,
        ]);

        // No images from other websites (no trackers, docs/DECISIONS.md): they become their description.
        $html = preg_replace_callback('/<img\s[^>]*>/i', function (array $match): string {
            if (preg_match('/\ssrc="\/(?!\/)/i', $match[0])) {
                return $match[0];
            }

            return preg_match('/\salt="([^"]*)"/i', $match[0], $alt) ? $alt[1] : '';
        }, $html);

        return new HtmlString($html);
    }

    /**
     * Like toHtml(), and every h2 gets the id "sec-<heading>", for jump links.
     *
     * @return array{html: HtmlString, sections: list<array{string, string}>}
     */
    public static function withSections(?string $markdown): array
    {
        $sections = [];
        $html = preg_replace_callback('/<h2>(.*?)<\/h2>/s', function (array $match) use (&$sections): string {
            $label = html_entity_decode(strip_tags($match[1]), ENT_QUOTES | ENT_HTML5);
            $id = Str::slug($label) ?: 'section';
            $base = $id;
            for ($n = 2; in_array($id, array_column($sections, 0), true); $n++) {
                $id = $base.'-'.$n;
            }
            $sections[] = [$id, $label];

            return '<h2 id="sec-'.$id.'">'.$match[1].'</h2>';
        }, (string) self::toHtml($markdown));

        return ['html' => new HtmlString($html), 'sections' => $sections];
    }

    /** True if the text embeds an image from another website. */
    public static function hasExternalImage(?string $markdown): bool
    {
        return (bool) preg_match('/!\[[^\]]*\]\(\s*<?(?!\/(?!\/))/', (string) $markdown);
    }
}
