<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ZoomLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $link;    
    public ?int $rayonsId;          // tambahkan ini
    public ?int $komSchedulesId;

    /**
     * Create a new message instance.
     */
    public function __construct(string $link, ?int $rayonsId = null, ?int $komSchedulesId = null)
    {
        $this->link = $link;
        $this->rayonsId = $rayonsId;
        $this->komSchedulesId = $komSchedulesId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Link Zoom Kelas Tutorial Anda'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.zoom_link',
            with: [
                'link' => $this->link,
                'rayonsId' => $this->rayonsId,
                'komSchedulesId' => $this->komSchedulesId,
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
        return [];
    }
}
