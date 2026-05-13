<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\UserEvent;

class RegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $userEvent;

    /**
     * Create a new message instance.
     */
    public function __construct(UserEvent $userEvent)
    {
        // Gunakan huruf kecil sesuai nama properti
        $this->userEvent = $userEvent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Confirmation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_confirmation', // Ganti dengan path view yang sesuai
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
