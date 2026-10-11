<?php

namespace App\Models;

use App\Models\Concerns\HasMetaDescription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasMetaDescription;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'featured_image',
        'category_id',
        'published_at',
        'is_published',
        'price',
        'story_info',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::deleting(fn (Post $post) => $post->comments()->delete());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The anti-bot question for this story's comment form: "which is the N-th
     * word of the story?". The word is picked from the first lines of the body
     * and depends on the slug, so every story asks for a different word and a
     * bot can't answer without reading the actual text.
     *
     * @return array{position: int, word: string}|null
     */
    public function commentChallenge(): ?array
    {
        // Pad tags with spaces first so "</p><p>" doesn't glue two words together.
        $text = html_entity_decode(strip_tags(str_replace('<', ' <', (string) $this->body)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        preg_match_all('/\p{L}+(?:-\p{L}+)*/u', $text, $matches);

        $words = array_slice($matches[0], 0, 12);

        // Skip the first two words (too easy) and short ones like "в", "и", "от".
        $candidates = array_values(array_filter(
            array_keys($words),
            fn (int $index) => $index >= 2 && mb_strlen($words[$index]) >= 3
        ));

        if (! $candidates) {
            return null;
        }

        $index = $candidates[crc32($this->slug) % count($candidates)];

        return ['position' => $index + 1, 'word' => $words[$index]];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->orderByDesc('published_at');
    }
}
