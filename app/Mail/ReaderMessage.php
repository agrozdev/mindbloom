<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

/**
 * A manual email to a reader. The body is already rendered by ReaderMailer.
 */
class ReaderMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public string $htmlBody,
        public string $unsubscribeUrl,
        public ?string $storyTitle = null,
        public ?string $bannerUrl = null,
    ) {}

    /**
     * One of the site's banners, picked per story so the same story always
     * gets the same picture (and the preview matches the sent email).
     */
    public static function bannerFor(?int $postId, string $subject): string
    {
        $count = 5;
        $index = $postId !== null ? $postId % $count : crc32($subject) % $count;

        return asset('images/email/banner-'.($index + 1).'.jpg');
    }

    public function envelope(): Envelope
    {
        $fromAddress = Setting::get('mail_from_address') ?: config('mail.from.address');
        $fromName = Setting::get('mail_from_name') ?: config('mail.from.name');
        $replyTo = Setting::get('mail_reply_to');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            replyTo: $replyTo ? [new Address($replyTo)] : [],
            subject: $this->emailSubject,
        );
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'List-Unsubscribe' => '<'.$this->unsubscribeUrl.'>',
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ]);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reader-message',
            text: 'emails.reader-message-text',
            with: ['plainBody' => self::toPlainText($this->htmlBody)],
        );
    }

    public static function toPlainText(string $html): string
    {
        $text = preg_replace_callback(
            '/<a\s[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/is',
            function (array $m) {
                $label = trim(html_entity_decode(strip_tags($m[2]), ENT_QUOTES | ENT_HTML5));
                $url = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);

                return $label === '★' ? $url."\n" : ($label === '' ? $url : $label.' ('.$url.')');
            },
            $html
        );

        $text = preg_replace(['/<br\s*\/?>/i', '/<\/(p|div|h[1-6]|ul|ol)>/i', '/<li[^>]*>/i'], ["\n", "\n\n", '– '], $text);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5);

        return trim(preg_replace("/\n{3,}/", "\n\n", preg_replace('/[ \t]+\n/', "\n", $text)));
    }
}
