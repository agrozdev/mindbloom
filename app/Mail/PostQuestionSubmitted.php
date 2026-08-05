<?php

namespace App\Mail;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostQuestionSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Post $post, public array $data)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Въпрос към историята "'.$this->post->title.'"',
            replyTo: [$this->data['email']],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.post-question-submitted',
        );
    }
}
