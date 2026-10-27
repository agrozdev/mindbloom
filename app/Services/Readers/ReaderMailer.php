<?php

namespace App\Services\Readers;

use App\Mail\ReaderMessage;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Post;
use App\Models\Reader;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use RuntimeException;
use Throwable;

/**
 * Renders and sends the manual emails from the „Изпрати имейл“ popup.
 */
class ReaderMailer
{
    public const PLACEHOLDERS = [
        '{име}' => 'първото име на читателя',
        '{приказка}' => 'заглавието на приказката',
        '{линк_приказка}' => 'адресът на приказката',
        '{линк_отзив}' => 'адресът на страницата за отзив',
        '{звезди}' => '5 звезди за оценка с един клик',
        '{отписване}' => 'адресът за отписване',
    ];

    public const BUTTON_HINT = 'Връзка, която е сама на свой ред, се показва като бутон.';

    /**
     * Fills the placeholders and tags the site's links with the reader token.
     *
     * Without a feedback token (preview and test emails) the feedback links
     * open a demo form that saves nothing, and links are not tagged, so
     * clicking a test email never signs the admin in as the reader.
     *
     * @return array{subject: string, body: string}
     */
    public function render(Reader $reader, ?Post $post, string $subject, string $body, ?string $feedbackToken): array
    {
        $values = [
            '{име}' => $reader->firstName(),
            '{приказка}' => $post?->title ?? '',
            '{линк_приказка}' => $post ? route('blog.show', [$post->category, $post]) : route('blog.index'),
            '{линк_отзив}' => $this->feedbackUrl($post, $feedbackToken),
            '{отписване}' => route('unsubscribe.show', $reader->token),
        ];

        $htmlValues = array_map(fn (string $value) => e($value), $values);
        $htmlValues['{звезди}'] = $this->stars($post, $feedbackToken);

        // The editor may store "{" as an entity or wrap URLs placed in href; normalise first.
        $body = str_replace(['%7B', '%7D'], ['{', '}'], $body);

        return [
            'subject' => strtr($subject, $values + ['{звезди}' => '']),
            'body' => $this->buttonize($feedbackToken !== null
                ? $this->tagLinks(strtr($body, $htmlValues), $reader)
                : strtr($body, $htmlValues)),
        ];
    }

    /**
     * A link alone in its own paragraph is shown as a button, since the
     * editor can't style links itself.
     */
    private function buttonize(string $html): string
    {
        return preg_replace(
            '/<p>\s*(?:<strong>)?\s*<a ((?:(?!style=)[^>])*)>(.*?)<\/a>\s*(?:<\/strong>)?\s*<\/p>/is',
            '<p style="margin:1.5em 0;"><a $1 style="display:inline-block; background-color:#A31C1C; color:#ffffff; padding:12px 26px; border-radius:6px; text-decoration:none; font-weight:bold;">$2</a></p>',
            $html
        );
    }

    /**
     * Full email HTML as the reader will see it, for the admin preview.
     */
    public function preview(Reader $reader, ?Post $post, string $subject, string $body): string
    {
        $rendered = $this->render($reader, $post, $subject, $body, null);

        return $this->mailable($reader, $post, $rendered['subject'], $rendered['body'])->render();
    }

    public function sendTest(Reader $reader, ?Post $post, string $subject, string $body, string $to): void
    {
        $rendered = $this->render($reader, $post, $subject, $body, null);

        $mailable = $this->mailable($reader, $post, $rendered['subject'], $rendered['body']);
        $mailable->emailSubject = '[ТЕСТ] '.$rendered['subject'];

        Mail::to($to)->send($mailable);
    }

    public function send(Reader $reader, ?Post $post, ?EmailTemplate $template, string $subject, string $body, ?User $sender): EmailLog
    {
        if (! $reader->canBeEmailed()) {
            throw new RuntimeException($reader->emailBlockReason() ?? 'Читателят не може да получава имейли.');
        }

        $feedbackToken = self::newFeedbackToken();
        $rendered = $this->render($reader, $post, $subject, $body, $feedbackToken);

        $log = $reader->emailLogs()->create([
            'post_id' => $post?->id,
            'template_id' => $template?->id,
            'subject' => $rendered['subject'],
            'body' => $rendered['body'],
            'feedback_token' => $feedbackToken,
            'sent_by' => $sender?->id,
            'status' => EmailLog::STATUS_FAILED,
            'sent_at' => now(),
        ]);

        try {
            Mail::to($reader->email, $reader->name)->send($this->mailable($reader, $post, $rendered['subject'], $rendered['body']));
            $log->update(['status' => EmailLog::STATUS_SENT, 'error' => null]);
        } catch (Throwable $e) {
            report($e);
            $log->update(['error' => mb_substr(class_basename($e).': '.$e->getMessage(), 0, 2000)]);
        }

        return $log;
    }

    public static function newFeedbackToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function mailable(Reader $reader, ?Post $post, string $subject, string $body): ReaderMessage
    {
        return new ReaderMessage(
            $subject,
            $body,
            route('unsubscribe.show', $reader->token),
            $post?->title,
            ReaderMessage::bannerFor($post?->id, $subject),
        );
    }

    /**
     * The reader's own review link, or a signed demo link for test emails.
     */
    private function feedbackUrl(?Post $post, ?string $feedbackToken, ?int $rating = null): string
    {
        $query = $rating ? ['rating' => $rating] : [];

        if ($feedbackToken !== null) {
            return route('feedback.show', ['token' => $feedbackToken] + $query);
        }

        return $post
            ? URL::temporarySignedRoute('feedback.demo', now()->addDays(30), ['post' => $post] + $query)
            : route('blog.index');
    }

    private function stars(?Post $post, ?string $feedbackToken): string
    {
        $stars = '';

        for ($rating = 1; $rating <= 5; $rating++) {
            $url = e($this->feedbackUrl($post, $feedbackToken, $rating));
            $stars .= '<a href="'.$url.'" title="'.$rating.' от 5" style="color:#E8A317; font-size:30px; line-height:1; text-decoration:none; padding:0 2px;">&#9733;</a>';
        }

        return '<span style="white-space:nowrap;">'.$stars.'</span>';
    }

    /**
     * Appends ?r={token} to links pointing at this site, so a click identifies
     * the reader. Feedback and unsubscribe links already carry their own token,
     * and links to other sites are left alone so the token never leaves the site.
     */
    private function tagLinks(string $html, Reader $reader): string
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        return preg_replace_callback('/href="([^"]*)"/i', function (array $match) use ($host, $reader) {
            $url = html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5);
            $parts = parse_url($url);

            if (! $parts || ($parts['host'] ?? null) !== $host) {
                return $match[0];
            }

            $path = $parts['path'] ?? '/';

            if (str_starts_with($path, '/otziv/') || str_starts_with($path, '/otpisvane/')) {
                return $match[0];
            }

            parse_str($parts['query'] ?? '', $query);
            $query['r'] = $reader->token;

            $tagged = ($parts['scheme'] ?? 'https').'://'.$parts['host']
                .(isset($parts['port']) ? ':'.$parts['port'] : '')
                .$path.'?'.http_build_query($query)
                .(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');

            return 'href="'.e($tagged).'"';
        }, $html);
    }
}
