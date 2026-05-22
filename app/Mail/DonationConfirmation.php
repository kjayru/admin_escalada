<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $donorName,
        public readonly string $amount,
        public readonly string $currency,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu donación fue exitosa! — Escalada Libre A.C.',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.donation-confirmation',
        );
    }
}
