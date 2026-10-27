<?php

namespace Tests\Feature\Readers;

use App\Filament\Resources\Readers\Pages\ListReaders;
use App\Filament\Resources\Readers\Pages\ViewReader;
use App\Mail\ReaderMessage;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\StoryRead;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

class SendEmailActionTest extends ReaderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->actingAs(User::factory()->create(['email' => 'admin@mindbloombg.com']));
    }

    public function test_popup_defaults_to_the_most_read_story_not_yet_emailed(): void
    {
        $reader = $this->makeReader();
        $short = $this->makeStory(['title' => 'Кратка']);
        $long = $this->makeStory(['title' => 'Дълга']);
        $reader->storyReads()->create(['post_id' => $short->id, 'status' => StoryRead::STATUS_OPENED, 'max_seconds' => 30]);
        $reader->storyReads()->create(['post_id' => $long->id, 'status' => StoryRead::STATUS_READ, 'max_seconds' => 300]);
        $reader->emailLogs()->create(['post_id' => $long->id, 'subject' => 'x', 'body' => 'x', 'status' => EmailLog::STATUS_SENT, 'sent_at' => now()->subMonth()]);

        Livewire::test(ListReaders::class)
            ->mountAction(TestAction::make('sendEmail')->table($reader))
            ->assertSchemaStateSet([
                'post_id' => $short->id,
                'subject' => 'Как ви се стори „{приказка}“?',
                'test_email' => 'admin@mindbloombg.com',
            ]);
    }

    public function test_send_test_keeps_modal_open_and_logs_nothing(): void
    {
        $reader = $this->makeReader();
        $post = $this->makeStory();
        $reader->storyReads()->create(['post_id' => $post->id, 'status' => StoryRead::STATUS_READ, 'max_seconds' => 120]);

        Livewire::test(ListReaders::class)
            ->mountAction(TestAction::make('sendEmail')->table($reader))
            ->callMountedAction(arguments: ['test' => true])
            ->assertHasNoFormErrors()
            ->assertActionMounted(TestAction::make('sendEmail')->table($reader));

        Mail::assertSent(ReaderMessage::class, fn (ReaderMessage $mail) => $mail->hasTo('admin@mindbloombg.com')
            && $mail->emailSubject === '[ТЕСТ] Как ви се стори „'.$post->title.'“?');
        $this->assertSame(0, EmailLog::query()->count());
    }

    public function test_send_logs_and_can_save_a_new_template(): void
    {
        $reader = $this->makeReader();
        $post = $this->makeStory();
        $reader->storyReads()->create(['post_id' => $post->id, 'status' => StoryRead::STATUS_READ, 'max_seconds' => 120]);

        Livewire::test(ViewReader::class, ['record' => $reader->getRouteKey()])
            ->callAction('sendEmail', arguments: ['send' => true], data: [
                'subject' => 'Здравейте, {име}',
                'body' => '<p>Как сте, {име}?</p>',
                'save_as_template' => true,
                'template_name' => 'Кратък поздрав',
            ])
            ->assertHasNoFormErrors()
            ->assertNotified('Имейлът е изпратен до '.$reader->name.'.');

        $log = EmailLog::query()->sole();
        $this->assertSame('Здравейте, Мария', $log->subject);
        $this->assertSame($post->id, $log->post_id);
        $this->assertSame(auth()->id(), $log->sent_by);
        $this->assertSame('Кратък поздрав', $log->template->name);
        $this->assertSame(4, EmailTemplate::query()->count());
        Mail::assertSent(ReaderMessage::class, fn (ReaderMessage $mail) => $mail->hasTo($reader->email));
    }

    public function test_plain_form_submit_sends_nothing(): void
    {
        $reader = $this->makeReader();

        Livewire::test(ListReaders::class)
            ->mountAction(TestAction::make('sendEmail')->table($reader))
            ->callMountedAction()
            ->assertActionMounted(TestAction::make('sendEmail')->table($reader));

        Mail::assertNothingSent();
        $this->assertSame(0, EmailLog::query()->count());
    }

    public function test_choosing_a_template_fills_subject_and_body(): void
    {
        $reader = $this->makeReader();
        $template = EmailTemplate::query()->where('name', 'Нова приказка')->sole();

        Livewire::test(ListReaders::class)
            ->mountAction(TestAction::make('sendEmail')->table($reader))
            ->fillForm(['template_id' => $template->id])
            ->assertSchemaStateSet(['subject' => 'Нова приказка за вас, {име}']);
    }

    public function test_action_is_disabled_for_unsubscribed_readers(): void
    {
        $reader = $this->makeReader(['unsubscribed_at' => now()]);

        Livewire::test(ListReaders::class)
            ->assertActionDisabled(TestAction::make('sendEmail')->table($reader));
    }
}
