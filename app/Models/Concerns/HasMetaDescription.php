<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Builds a clean, single-line meta description for a model.
 *
 * Prefers a real `excerpt`, but falls back to the body text when the excerpt
 * is missing, too short, or just a copy of the title (which is how a lot of
 * the imported blog posts ended up — meta description = bare post title).
 *
 * Set `$metaBodyField` on the model if the long text lives somewhere other
 * than `body` (Service / Event use `description`).
 */
trait HasMetaDescription
{
    public function metaDescription(int $length = 155): string
    {
        $title = trim((string) ($this->title ?? ''));
        $excerpt = $this->collapseWhitespace((string) ($this->excerpt ?? ''));

        $excerptIsUsable = $excerpt !== ''
            && mb_strlen($excerpt) >= 40
            && Str::lower($excerpt) !== Str::lower($title);

        $bodyField = property_exists($this, 'metaBodyField') ? $this->metaBodyField : 'body';

        $source = $excerptIsUsable
            ? $excerpt
            : $this->collapseWhitespace((string) ($this->{$bodyField} ?? ''));

        return $this->limitAtSentenceOrWord($source, $length);
    }

    /**
     * Str::limit() truncates at a raw character count, which can land
     * mid-word or mid-thought ("...своя малка..."). This prefers cutting at
     * the last full sentence that fits, falling back to the last full word
     * when no sentence boundary exists within the limit.
     */
    protected function limitAtSentenceOrWord(string $value, int $limit): string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        $truncated = mb_substr($value, 0, $limit);

        if (preg_match('/^(.*[.!?])\s/us', $truncated . ' ', $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/^(.*)\s\S*$/us', $truncated, $matches)) {
            return trim($matches[1]) . '...';
        }

        return trim($truncated) . '...';
    }

    protected function collapseWhitespace(string $value): string
    {
        // strip_tags() alone glues adjacent blocks together
        // ("...какво е.</p><p>Затова..." -> "какво е.Затова"), so turn
        // <br> and block-level closing tags into spaces first.
        $value = preg_replace('~<br\s*/?>|</(p|div|li|h[1-6]|blockquote)>~i', ' ', $value);

        return trim(preg_replace('/\s+/', ' ', strip_tags($value)));
    }
}
