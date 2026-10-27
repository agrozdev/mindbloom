<?php

namespace Tests\Feature\Readers;

use App\Models\Setting;
use App\Models\StoryRead;
use App\Services\Readers\ReaderIdentity;

class ReadingTrackingTest extends ReaderTestCase
{
    private function track(?string $token, array $data)
    {
        $request = $token ? $this->withCookie(ReaderIdentity::COOKIE, $token) : $this;

        return $request->post(route('readers.track'), $data);
    }

    public function test_requests_without_a_valid_reader_are_ignored(): void
    {
        $post = $this->makeStory();

        $this->track(null, ['post_id' => $post->id, 'seconds' => 100, 'scroll' => 100])->assertNoContent();
        $this->track(str_repeat('a', 64), ['post_id' => $post->id, 'seconds' => 100, 'scroll' => 100])->assertNoContent();

        $this->assertSame(0, StoryRead::query()->count());
    }

    public function test_status_thresholds_and_max_values(): void
    {
        $post = $this->makeStory();
        $reader = $this->makeReader();

        $this->track($reader->token, ['post_id' => $post->id, 'seconds' => 10, 'scroll' => 20]);
        $this->assertSame(StoryRead::STATUS_VISITED, StoryRead::query()->sole()->status);

        $this->track($reader->token, ['post_id' => $post->id, 'seconds' => 20, 'scroll' => 30]);
        $this->assertSame(StoryRead::STATUS_OPENED, StoryRead::query()->sole()->status);

        // Enough time but not enough scroll is still only "opened".
        $this->track($reader->token, ['post_id' => $post->id, 'seconds' => 90, 'scroll' => 50]);
        $this->assertSame(StoryRead::STATUS_OPENED, StoryRead::query()->sole()->status);

        $this->track($reader->token, ['post_id' => $post->id, 'seconds' => 61, 'scroll' => 75]);
        $read = StoryRead::query()->sole();
        $this->assertSame(StoryRead::STATUS_READ, $read->status);
        $this->assertSame(90, $read->max_seconds);
        $this->assertSame(75, $read->max_scroll);

        // A later, shorter visit never downgrades.
        $this->track($reader->token, ['post_id' => $post->id, 'seconds' => 3, 'scroll' => 5]);
        $this->assertSame(StoryRead::STATUS_READ, StoryRead::query()->sole()->status);
    }

    public function test_thresholds_come_from_settings(): void
    {
        Setting::putMany(['read_opened_seconds' => 5]);
        $post = $this->makeStory();
        $reader = $this->makeReader();

        $this->track($reader->token, ['post_id' => $post->id, 'seconds' => 6, 'scroll' => 0]);

        $this->assertSame(StoryRead::STATUS_OPENED, StoryRead::query()->sole()->status);
    }

    public function test_seconds_are_capped_and_scroll_validated(): void
    {
        $post = $this->makeStory();
        $reader = $this->makeReader();

        $this->track($reader->token, ['post_id' => $post->id, 'seconds' => 999999, 'scroll' => 100]);
        $this->assertSame(3 * 60 * 60, StoryRead::query()->sole()->max_seconds);

        $this->track($reader->token, ['post_id' => $post->id, 'seconds' => 1, 'scroll' => 150])->assertStatus(302);
    }

    public function test_unknown_story_is_ignored(): void
    {
        $reader = $this->makeReader();

        $this->track($reader->token, ['post_id' => 999999, 'seconds' => 100, 'scroll' => 100])->assertNoContent();

        $this->assertSame(0, StoryRead::query()->count());
    }
}
