<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ново запитване от контактната форма от '.$this->data['first_name'].' '.$this->data['last_name'],
            replyTo: [$this->data['email']],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form-submitted',
        );
    }
}
