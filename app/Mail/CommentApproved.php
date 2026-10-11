<?php

namespace App\Mail;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommentApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Comment $comment)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Твоят коментар вече е част от историята',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.comment-approved',
        );
    }
}
