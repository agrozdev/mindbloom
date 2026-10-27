<?php

namespace App\Services\Readers;

use App\Models\Post;
use App\Models\Reader;
use App\Models\StoryRead;

class ReadingTracker
{
    /** Longer than any story takes to read; anything above is a tab left open. */
    public const MAX_SECONDS = 3 * 60 * 60;

    private const STATUS_RANK = [
        StoryRead::STATUS_VISITED => 0,
        StoryRead::STATUS_OPENED => 1,
        StoryRead::STATUS_READ => 2,
    ];

    public static function recordVisit(Reader $reader, Post $post): StoryRead
    {
        $read = StoryRead::query()->firstOrNew(['reader_id' => $reader->id, 'post_id' => $post->id]);
        $read->first_seen ??= now();
        $read->status ??= StoryRead::STATUS_VISITED;
        $read->visits = ($read->visits ?? 0) + 1;
        $read->last_seen = now();
        $read->save();

        return $read;
    }

    /**
     * Keeps the highest active time and scroll seen for this story and
     * upgrades the status when a threshold is crossed (never downgrades).
     */
    public static function recordProgress(Reader $reader, Post $post, int $seconds, int $scroll): StoryRead
    {
        $read = StoryRead::query()->firstOrNew(['reader_id' => $reader->id, 'post_id' => $post->id]);

        if (! $read->exists) {
            $read->fill(['first_seen' => now(), 'visits' => 1, 'status' => StoryRead::STATUS_VISITED]);
        }

        $read->max_seconds = max((int) $read->max_seconds, min($seconds, self::MAX_SECONDS));
        $read->max_scroll = max((int) $read->max_scroll, max(0, min($scroll, 100)));

        $status = StoryRead::statusFor($read->max_seconds, $read->max_scroll);
        if (self::STATUS_RANK[$status] > (self::STATUS_RANK[$read->status] ?? 0)) {
            $read->status = $status;
        }

        $read->last_seen = now();
        $read->save();

        return $read;
    }
}
