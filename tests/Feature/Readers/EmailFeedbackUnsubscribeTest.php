<?php

namespace Tests\Feature\Readers;

use App\Mail\ReaderMessage;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Reader;
use App\Models\Review;
use App\Models\StoryRead;
use App\Models\User;
use App\Services\Readers\ReaderMailer;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class EmailFeedbackUnsubscribeTest extends ReaderTestCase
{
    public function test_templates_are_seeded(): void
    {
        $this->assertSame(
            ['Как ви се стори приказката', 'Нова приказка', 'Покана за консултация'],
            EmailTemplate::query()->orderBy('id')->pluck('name')->all()
        );
    }

    public function test_send_renders_placeholders_tags_links_and_logs(): void
    {
        Mail::fake();
        $post = $this->makeStory();
        $reader = $this->makeReader(['name' => 'Мария Иванова']);
        $template = EmailTemplate::query()->orderBy('id')->first();

        $log = app(ReaderMailer::class)->send($reader, $post, $template, $template->subject, $template->body, null);

        $this->assertSame(EmailLog::STATUS_SENT, $log->status);
        $this->assertSame('Как ви се стори „Слънчевият лъч“?', $log->subject);
        $this->assertSame(64, strlen($log->feedback_token));
        $this->assertStringContainsString('Здравейте, Мария,', $log->body);
        $this->assertStringContainsString(route('feedback.show', ['token' => $log->feedback_token, 'rating' => 5]), html_entity_decode($log->body));
        // The feedback link carries its own token, not ?r=.
        $this->assertStringNotContainsString('/otziv/'.$log->feedback_token.'?r=', html_entity_decode($log->body));
        // The button paragraph became a styled button.
        $this->assertStringContainsString('background-color:#A31C1C', $log->body);

        Mail::assertSent(ReaderMessage::class, function (ReaderMessage $mail) use ($reader) {
            $mail->assertSeeInText('Отписване');
            $html = $mail->render();

            return $mail->hasTo($reader->email)
                && str_contains($html, 'Получавате този имейл, защото сте се регистрирали на mindbloombg.com.')
                && str_contains($html, route('unsubscribe.show', $reader->token));
        });
    }

    public function test_site_links_get_reader_token_but_external_links_do_not(): void
    {
        $post = $this->makeStory();
        $reader = $this->makeReader();

        $body = '<p><a href="{линк_приказка}">тук</a> и <a href="https://example.org/x">там</a></p>';
        $rendered = app(ReaderMailer::class)->render($reader, $post, 'Т', $body, ReaderMailer::newFeedbackToken());
        $html = html_entity_decode($rendered['body']);

        $this->assertStringContainsString($this->storyUrl($post).'?r='.$reader->token, $html);
        $this->assertStringContainsString('href="https://example.org/x"', $html);
    }

    public function test_list_unsubscribe_headers(): void
    {
        $mail = new ReaderMessage('Тема', '<p>Здравейте</p>', 'https://mindbloombg.com/otpisvane/abc');
        $headers = $mail->headers()->text;

        $this->assertSame('<https://mindbloombg.com/otpisvane/abc>', $headers['List-Unsubscribe']);
        $this->assertSame('List-Unsubscribe=One-Click', $headers['List-Unsubscribe-Post']);
    }

    public function test_unsubscribed_reader_cannot_be_emailed(): void
    {
        Mail::fake();
        $reader = $this->makeReader(['unsubscribed_at' => now()]);

        $this->expectException(RuntimeException::class);

        try {
            app(ReaderMailer::class)->send($reader, null, null, 'Т', '<p>Х</p>', null);
        } finally {
            Mail::assertNothingSent();
            $this->assertSame(0, EmailLog::query()->count());
        }
    }

    public function test_test_email_is_not_logged(): void
    {
        Mail::fake();
        $reader = $this->makeReader();

        app(ReaderMailer::class)->sendTest($reader, null, 'Тема', '<p>Х</p>', 'admin@example.com');

        Mail::assertSent(ReaderMessage::class, fn (ReaderMessage $mail) => $mail->hasTo('admin@example.com') && $mail->emailSubject === '[ТЕСТ] Тема');
        $this->assertSame(0, EmailLog::query()->count());
    }

    private function sentLog(): EmailLog
    {
        Mail::fake();
        $post = $this->makeStory();
        $reader = $this->makeReader();

        return app(ReaderMailer::class)->send($reader, $post, null, 'Т', '<p>{звезди}</p>', null);
    }

    public function test_feedback_page_preselects_rating_and_saves_pending_review(): void
    {
        $log = $this->sentLog();

        $this->get(route('feedback.show', ['token' => $log->feedback_token, 'rating' => 4]))
            ->assertOk()
            ->assertSee('id="rating_4" name="rating" value="4" checked', false)
            ->assertSee('Изпрати мнението си');

        $this->post(route('feedback.store', $log->feedback_token), [
            'rating' => 4,
            'answer_feeling' => 'Топлина',
            'answer_lesson' => 'Да бъдеш себе си',
            'can_publish' => '1',
        ])->assertRedirect(route('feedback.show', $log->feedback_token));

        $review = Review::query()->sole();
        $this->assertSame(Review::STATUS_PENDING, $review->status);
        $this->assertSame($log->id, $review->email_log_id);
        $this->assertTrue($review->can_publish);

        $this->get(route('feedback.show', $log->feedback_token))->assertSee('Благодарим ви! Вашето мнение е много ценно за нас.');
    }

    public function test_feedback_with_unknown_token_is_404(): void
    {
        $this->get(route('feedback.show', str_repeat('b', 64)))->assertNotFound();
    }

    public function test_only_approved_and_permitted_reviews_show_with_aggregate_rating(): void
    {
        $log = $this->sentLog();
        $post = $log->post()->with('category')->first();
        $base = ['reader_id' => $log->reader_id, 'post_id' => $post->id, 'rating' => 5, 'answer_feeling' => 'Скрит отзив'];

        Review::query()->create($base + ['status' => Review::STATUS_APPROVED, 'can_publish' => false]);
        Review::query()->create(['answer_feeling' => 'Чакащ отзив'] + $base + ['status' => Review::STATUS_PENDING, 'can_publish' => true]);

        $this->get($this->storyUrl($post))->assertDontSee('Отзиви от читатели')->assertDontSee('aggregateRating');

        Review::query()->create(['answer_feeling' => 'Видим отзив', 'rating' => 4] + $base + ['status' => Review::STATUS_APPROVED, 'can_publish' => true]);

        $this->get($this->storyUrl($post))
            ->assertSee('Отзиви от читатели')
            ->assertSee('Видим отзив')
            ->assertDontSee('Скрит отзив')
            ->assertDontSee('Чакащ отзив')
            ->assertSee('"aggregateRating":{"@type":"AggregateRating","ratingValue":4', false)
            ->assertDontSee('Иванова');
    }

    public function test_unsubscribe_get_only_shows_the_button(): void
    {
        $reader = $this->makeReader();

        $this->get(route('unsubscribe.show', $reader->token))->assertOk()->assertSee('Отпиши ме');

        $this->assertNull($reader->fresh()->unsubscribed_at);
    }

    public function test_unsubscribe_post_without_csrf_works_for_one_click(): void
    {
        $reader = $this->makeReader();
        $reader->storyReads()->create(['post_id' => $this->makeStory()->id, 'status' => StoryRead::STATUS_READ]);

        $this->post(route('unsubscribe.store', $reader->token), ['List-Unsubscribe' => 'One-Click'])
            ->assertOk()
            ->assertSee('Успешно се отписахте. Няма да получавате повече имейли от нас.');

        $reader->refresh();
        $this->assertNotNull($reader->unsubscribed_at);
        $this->assertFalse($reader->canBeEmailed());
        $this->assertSame(1, $reader->storyReads()->count(), 'reading data is kept');
    }

    public function test_deleting_a_reader_removes_all_related_data(): void
    {
        $log = $this->sentLog();
        $reader = $log->reader;
        $reader->storyReads()->create(['post_id' => $log->post_id, 'status' => StoryRead::STATUS_READ]);
        Review::query()->create(['reader_id' => $reader->id, 'post_id' => $log->post_id, 'email_log_id' => $log->id, 'rating' => 5]);

        $reader->delete();

        $this->assertSame(0, Reader::query()->count());
        $this->assertSame(0, StoryRead::query()->count());
        $this->assertSame(0, EmailLog::query()->count());
        $this->assertSame(0, Review::query()->count());
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::factory()->create();
        $reader = $this->makeReader();
        $this->sentLog();

        $this->actingAs($admin);
        $this->get(route('filament.admin.resources.readers.index'))->assertOk()->assertSee($reader->email);
        $this->get(route('filament.admin.resources.readers.view', $reader))->assertOk()->assertSee('Прочетени приказки');
        $this->get(route('filament.admin.resources.reviews.index'))->assertOk();
        $this->get(route('filament.admin.resources.email-templates.index'))->assertOk()->assertSee('Покана за консултация');
        $this->get(route('filament.admin.pages.reader-settings'))->assertOk()->assertSee('Cloudflare Turnstile');
    }
}
