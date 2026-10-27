<?php

namespace Tests\Feature\Readers;

use App\Models\Reader;
use App\Models\StoryRead;
use App\Services\Readers\ReaderIdentity;

class StoryGateTest extends ReaderTestCase
{
    public function test_anonymous_visitor_gets_the_full_text_with_gate_and_paywall_schema(): void
    {
        $post = $this->makeStory();

        $response = $this->get($this->storyUrl($post));

        $response->assertOk()
            ->assertSee('Параграф 1 от', false)
            ->assertSee('Параграф 10 от', false)
            ->assertSee('class="story-locked"', false)
            ->assertSee('Искате ли да прочетете приказката докрай?')
            ->assertSee('"isAccessibleForFree":false', false)
            ->assertSee('"cssSelector":".story-locked"', false);

        // Paywall fields are merged into the one BlogPosting, not a second Article.
        $this->assertSame(1, substr_count($response->getContent(), '"@type":"BlogPosting"'));
    }

    public function test_paid_stories_keep_the_existing_paywall_without_the_gate(): void
    {
        $post = $this->makeStory(['price' => 5]);

        $this->get($this->storyUrl($post))
            ->assertOk()
            ->assertDontSee('Искате ли да прочетете приказката докрай?')
            ->assertDontSee('isAccessibleForFree');
    }

    public function test_signup_creates_reader_sets_cookie_and_counts_the_visit(): void
    {
        $post = $this->makeStory();

        $response = $this->postJson(route('readers.signup'), [
            'post_id' => $post->id,
            'name' => 'Мария Иванова',
            'email' => '  Maria@Example.COM ',
            'consent' => '1',
        ]);

        $response->assertOk()->assertJson(['ok' => true])->assertCookie(ReaderIdentity::COOKIE);

        $reader = Reader::query()->sole();
        $this->assertSame('maria@example.com', $reader->email);
        $this->assertSame(64, strlen($reader->token));
        $this->assertSame($post->id, $reader->signup_story_id);
        $this->assertNotNull($reader->consent_at);
        $this->assertSame(1, StoryRead::query()->sole()->visits);

        $cookie = collect($response->headers->getCookies())->firstWhere(fn ($c) => $c->getName() === ReaderIdentity::COOKIE);
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('lax', $cookie->getSameSite());
    }

    public function test_signup_reuses_existing_reader_by_email_and_resubscribes(): void
    {
        $post = $this->makeStory();
        $reader = $this->makeReader(['email' => 'maria@example.com', 'name' => 'Мария', 'unsubscribed_at' => now()]);

        $this->postJson(route('readers.signup'), [
            'post_id' => $post->id, 'name' => 'Друго Име', 'email' => 'MARIA@example.com', 'consent' => '1',
        ])->assertOk();

        $this->assertSame(1, Reader::query()->count());
        $reader->refresh();
        $this->assertSame('Мария', $reader->name);
        $this->assertNull($reader->unsubscribed_at);
    }

    public function test_signup_validates_in_bulgarian(): void
    {
        $post = $this->makeStory();

        $this->postJson(route('readers.signup'), ['post_id' => $post->id, 'name' => 'А', 'email' => 'not-an-email'])
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'Моля, въведете валиден имейл.')
            ->assertJsonPath('errors.consent.0', 'Моля, потвърдете съгласието си.');

        $this->assertSame(0, Reader::query()->count());
    }

    public function test_honeypot_stores_nothing(): void
    {
        $post = $this->makeStory();

        $this->postJson(route('readers.signup'), [
            'post_id' => $post->id, 'name' => 'Бот', 'email' => 'bot@example.com', 'consent' => '1', 'website' => 'http://spam',
        ])->assertOk()->assertCookieMissing(ReaderIdentity::COOKIE);

        $this->assertSame(0, Reader::query()->count());
    }

    public function test_signup_is_rate_limited_to_five_per_hour(): void
    {
        $post = $this->makeStory();

        foreach (range(1, 5) as $i) {
            $this->postJson(route('readers.signup'), ['post_id' => $post->id])->assertStatus(422);
        }

        $this->postJson(route('readers.signup'), ['post_id' => $post->id])->assertStatus(429);
    }

    public function test_identified_reader_sees_no_gate_and_gets_tracking(): void
    {
        $post = $this->makeStory();
        $reader = $this->makeReader();

        $this->withCookie(ReaderIdentity::COOKIE, $reader->token)
            ->get($this->storyUrl($post))
            ->assertOk()
            ->assertDontSee('Искате ли да прочетете приказката докрай?')
            ->assertSee('data-track-url', false);

        $this->assertSame(1, $reader->storyReads()->sole()->visits);
    }

    public function test_email_link_token_sets_cookie_and_redirects_to_clean_url(): void
    {
        $post = $this->makeStory();
        $reader = $this->makeReader();

        $this->get($this->storyUrl($post).'?utm_source=email&r='.$reader->token)
            ->assertRedirect($this->storyUrl($post).'?utm_source=email')
            ->assertCookie(ReaderIdentity::COOKIE, $reader->token);
    }

    public function test_invalid_email_link_token_is_stripped_without_cookie(): void
    {
        $post = $this->makeStory();

        $this->get($this->storyUrl($post).'?r=nope')
            ->assertRedirect($this->storyUrl($post))
            ->assertCookieMissing(ReaderIdentity::COOKIE);
    }
}
