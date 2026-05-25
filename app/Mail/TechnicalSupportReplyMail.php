<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TechnicalSupportReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $mail_data, public $type)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Technical Support Reply',
        );
    }

    public function content(): Content
    {
        if($this->type == 'admin') {
            return new Content(
                view: 'mail.admin-technical-support-reply'
            );
        }
        return new Content(
            view: 'mail.technical-support-reply'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
