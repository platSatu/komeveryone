<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ThankYouDonasiMail extends Mailable
{
    use Queueable, SerializesModels;

    public $donasi;
    public $joinUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($donasi,$joinUrl)
    {
        $this->donasi = $donasi;
        $this->joinUrl = $joinUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Terima Kasih atas Persembahan Anda'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.thankyou',
            with: [
                'donasi' => $this->donasi,
                'userName' => optional($this->donasi->user)->name ?? 'Sahabat KOM Digital',
                'joinUrl' => $this->joinUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
