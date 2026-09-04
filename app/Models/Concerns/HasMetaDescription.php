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

        return Str::limit($source, $length);
    }

    protected function collapseWhitespace(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags($value)));
    }
}
