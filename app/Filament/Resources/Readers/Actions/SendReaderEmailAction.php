<?php

namespace App\Filament\Resources\Readers\Actions;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Post;
use App\Models\Reader;
use App\Models\StoryRead;
use App\Services\Readers\ReaderMailer;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Throwable;

/**
 * The „Изпрати имейл“ popup. Emails only ever go out from here, by hand.
 */
class SendReaderEmailAction
{
    public static function make(): Action
    {
        return Action::make('sendEmail')
            ->label('Изпрати имейл')
            ->icon(Heroicon::OutlinedEnvelope)
            ->disabled(fn (Reader $record) => ! $record->canBeEmailed())
            ->tooltip(fn (Reader $record) => $record->emailBlockReason())
            ->modalHeading(fn (Reader $record) => 'Имейл до '.$record->name)
            ->modalWidth(Width::FiveExtraLarge)
            ->fillForm(fn (Reader $record) => self::defaults($record))
            ->schema(fn (Reader $record) => self::schema($record))
            // The real send is a footer button with a confirm dialog; the default
            // submit is hidden so pressing Enter in a field never sends anything.
            ->modalSubmitAction(false)
            ->extraModalFooterActions(fn (Action $action) => [
                $action->makeModalSubmitAction('send', arguments: ['send' => true])
                    ->label('Изпрати')
                    ->color('primary')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->extraAttributes(['wire:confirm' => 'Да изпратя ли имейла сега?']),
                $action->makeModalSubmitAction('sendTest', arguments: ['test' => true])
                    ->label('Изпрати тест')
                    ->color('gray')
                    ->icon(Heroicon::OutlinedBeaker),
            ])
            ->action(function (array $data, array $arguments, Reader $record, Action $action) {
                match (true) {
                    ! empty($arguments['send']) => self::send($data, $record, $action),
                    ! empty($arguments['test']) => self::sendTest($data, $record, $action),
                    default => $action->halt(),
                };
            });
    }

    /**
     * @return array<string, mixed>
     */
    private static function defaults(Reader $record): array
    {
        $template = EmailTemplate::query()->orderBy('id')->first();

        return [
            'template_id' => $template?->id,
            'post_id' => self::defaultStoryId($record),
            'subject' => $template?->subject,
            'body' => $template?->body,
            'test_email' => auth()->user()?->email,
            'save_as_template' => false,
        ];
    }

    /**
     * The story the reader spent the most time on that we haven't emailed about yet.
     */
    private static function defaultStoryId(Reader $record): ?int
    {
        $emailed = $record->emailLogs()->where('status', EmailLog::STATUS_SENT)->whereNotNull('post_id')->pluck('post_id');
        $reads = $record->storyReads()->orderByDesc('max_seconds')->get();

        return ($reads->first(fn (StoryRead $read) => ! $emailed->contains($read->post_id)) ?? $reads->first())?->post_id;
    }

    /**
     * @return array<string, string>
     */
    private static function storyOptions(Reader $record): array
    {
        return $record->storyReads()
            ->with('post')
            ->orderByDesc('max_seconds')
            ->get()
            ->filter(fn (StoryRead $read) => $read->post)
            ->mapWithKeys(fn (StoryRead $read) => [
                $read->post_id => $read->post->title.' — '.$read->statusLabel().', '.self::duration($read->max_seconds),
            ])
            ->all();
    }

    public static function duration(int $seconds): string
    {
        return $seconds >= 60 ? intdiv($seconds, 60).' мин '.($seconds % 60).' сек' : $seconds.' сек';
    }

    private static function schema(Reader $record): array
    {
        $placeholders = collect(ReaderMailer::PLACEHOLDERS)
            ->map(fn (string $description, string $tag) => '<li><code>'.e($tag).'</code> – '.e($description).'</li>')
            ->implode('');

        return [
            Callout::make('Този читател е получил имейл през последните 7 дни.')
                ->description('Помислете дали не е твърде скоро за нов имейл.')
                ->warning()
                ->visible(fn () => $record->wasEmailedRecently()),
            Grid::make(2)->schema([
                Select::make('template_id')
                    ->label('Шаблон')
                    ->options(fn () => EmailTemplate::query()->orderBy('name')->pluck('name', 'id'))
                    ->placeholder('— без шаблон —')
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        if ($template = EmailTemplate::find($state)) {
                            $set('subject', $template->subject);
                            $set('body', $template->body);
                        }
                    }),
                Select::make('post_id')
                    ->label('Приказка')
                    ->options(fn () => self::storyOptions($record))
                    ->placeholder('— без приказка —')
                    ->live(),
            ]),
            Tabs::make('email')->tabs([
                Tab::make('Редакция')->schema([
                    TextInput::make('subject')
                        ->label('Тема')
                        ->required()
                        ->maxLength(200)
                        ->live(onBlur: true),
                    RichEditor::make('body')
                        ->label('Съдържание')
                        ->required()
                        ->live(onBlur: true)
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'link'],
                            ['h2', 'h3'],
                            ['bulletList', 'orderedList', 'blockquote'],
                            ['undo', 'redo'],
                        ]),
                    Html::make(new HtmlString(
                        '<div style="font-size:.875rem; line-height:1.6; opacity:.85;"><strong>Можете да използвате:</strong><ul style="list-style:disc; padding-left:1.25rem; margin:.25rem 0;">'
                        .$placeholders.'</ul>'.e(ReaderMailer::BUTTON_HINT).'</div>'
                    )),
                ]),
                Tab::make('Преглед')->schema([
                    Html::make(fn (Get $get) => self::preview($record, $get)),
                ]),
            ]),
            Grid::make(2)->schema([
                TextInput::make('test_email')
                    ->label('Тестов имейл')
                    ->email()
                    ->helperText('Бутонът „Изпрати тест“ праща имейла с данните на читателя на този адрес, без да го записва.'),
                Grid::make(1)->schema([
                    Checkbox::make('save_as_template')
                        ->label('Запази като нов шаблон')
                        ->live(),
                    TextInput::make('template_name')
                        ->label('Име на шаблона')
                        ->maxLength(255)
                        ->visible(fn (Get $get) => (bool) $get('save_as_template'))
                        ->required(fn (Get $get) => (bool) $get('save_as_template')),
                ]),
            ]),
        ];
    }

    private static function preview(Reader $record, Get $get): HtmlString
    {
        $body = self::bodyHtml($get('body'));

        if (blank(strip_tags($body))) {
            return new HtmlString('<p style="opacity:.7;">Напишете съдържание, за да видите прегледа.</p>');
        }

        $post = $get('post_id') ? Post::with('category')->find($get('post_id')) : null;
        $html = app(ReaderMailer::class)->preview($record, $post, (string) $get('subject'), $body);
        $subject = app(ReaderMailer::class)->render($record, $post, (string) $get('subject'), '', null)['subject'];

        return new HtmlString(view('filament.readers.email-preview', ['html' => $html, 'subject' => $subject, 'reader' => $record])->render());
    }

    /**
     * While editing, the rich editor's raw state is a TipTap document, not HTML.
     */
    private static function bodyHtml(mixed $state): string
    {
        if (is_array($state)) {
            return RichContentRenderer::make($state)->toUnsafeHtml();
        }

        return (string) $state;
    }

    private static function sendTest(array $data, Reader $record, Action $action): void
    {
        $to = trim((string) ($data['test_email'] ?? ''));

        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Notification::make()->title('Моля, въведете валиден тестов имейл.')->danger()->send();
            $action->halt();
        }

        try {
            app(ReaderMailer::class)->sendTest($record, self::post($data), $data['subject'], self::bodyHtml($data['body']), $to);
            Notification::make()->title('Тестовият имейл е изпратен до '.$to.'.')->success()->send();
        } catch (Throwable $e) {
            report($e);
            Notification::make()->title('Тестовият имейл не беше изпратен.')->body($e->getMessage())->danger()->send();
        }

        // Keep the popup open for more edits or the real send.
        $action->halt();
    }

    private static function send(array $data, Reader $record, Action $action): void
    {
        $record->refresh();

        if (! $record->canBeEmailed()) {
            Notification::make()->title('Имейлът не е изпратен.')->body($record->emailBlockReason())->danger()->send();
            $action->halt();
        }

        $body = self::bodyHtml($data['body']);
        $template = filled($data['template_id'] ?? null) ? EmailTemplate::find($data['template_id']) : null;

        if (! empty($data['save_as_template'])) {
            $template = EmailTemplate::create([
                'name' => $data['template_name'],
                'subject' => $data['subject'],
                'body' => $body,
            ]);
        }

        $log = app(ReaderMailer::class)->send($record, self::post($data), $template, $data['subject'], $body, auth()->user());

        if ($log->status !== EmailLog::STATUS_SENT) {
            Notification::make()->title('Имейлът не беше изпратен.')->body($log->error)->danger()->persistent()->send();
            $action->halt();
        }

        Notification::make()->title('Имейлът е изпратен до '.$record->name.'.')->success()->send();
    }

    private static function post(array $data): ?Post
    {
        return filled($data['post_id'] ?? null) ? Post::with('category')->find($data['post_id']) : null;
    }
}
