<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KomUser;

class ClassTutorialNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public KmdClassTutorial $kelas;
    public KomUser $user;

    /**
     * Create a new message instance.
     */
    public function __construct(KmdClassTutorial $kelas, KomUser $user)
    {
        $this->kelas = $kelas;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pengingat Kelas Tutorial: {$this->kelas->topic}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.class_tutorial_notification',
            with: [
                'kelas' => $this->kelas,
                'user' => $this->user,
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
