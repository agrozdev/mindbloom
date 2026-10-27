<?php

namespace App\Services\Readers;

use DOMDocument;
use DOMNode;

/**
 * Splits a story body into the free part and the locked part, at a
 * top-level block boundary close to the given share of the text.
 */
class StoryGate
{
    /**
     * @return array{visible: string, locked: string}|null null when the story is too short to split
     */
    public static function split(?string $html, int $visiblePercent): ?array
    {
        $html = trim((string) $html);

        if ($html === '') {
            return null;
        }

        $dom = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><div>'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = null;
        foreach ($dom->childNodes as $node) {
            if ($node->nodeName === 'div') {
                $root = $node;
            }
        }

        if (! $root) {
            return null;
        }

        $blocks = array_values(array_filter(
            iterator_to_array($root->childNodes),
            fn (DOMNode $node) => $node->nodeType !== XML_TEXT_NODE || trim($node->textContent) !== ''
        ));

        if (count($blocks) < 2) {
            return null;
        }

        $lengths = array_map(fn (DOMNode $node) => mb_strlen(trim($node->textContent)), $blocks);
        $target = array_sum($lengths) * max(5, min(95, $visiblePercent)) / 100;

        $visible = '';
        $locked = '';
        $count = 0;
        $splitAt = null;

        foreach ($blocks as $index => $node) {
            if ($splitAt === null) {
                $count += $lengths[$index];
                $visible .= $dom->saveHTML($node);

                // Always keep at least one block on each side.
                if ($count >= $target || $index === count($blocks) - 2) {
                    $splitAt = $index;
                }
            } else {
                $locked .= $dom->saveHTML($node);
            }
        }

        return ['visible' => $visible, 'locked' => $locked];
    }
}
