<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\RegisterEvent;

class RegistrationSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $barcodePath;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\RegisterEvent  $event
     * @param  string  $barcodePath
     * @return void
     */
    public function __construct(RegisterEvent $event, $barcodePath)
    {
        $this->event = $event;
        $this->barcodePath = $barcodePath;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registrasi Berhasil!'
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_success',
            with: [
                'event' => $this->event,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromStoragePath($this->barcodePath)
                ->as('barcode-' . $this->event->code_registrasi . '.png')
                ->withMime('image/png')
        ];
    }
}
