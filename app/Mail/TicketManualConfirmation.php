<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\TicketManual;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TicketManualConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $manualTickets;

    public function __construct($manualTickets)
    {
        if (is_null($manualTickets) || (!is_array($manualTickets) && !is_iterable($manualTickets))) {
            Log::error('Data tiket manual tidak valid', ['tickets' => $manualTickets]);
            throw new \Exception('Tiket manual tidak valid atau data kosong.');
        }

        $this->manualTickets = $manualTickets;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[CONFIRMATION] Pendaftaran Planetshakers Abundance Tour 2025 with Ps. Russell Evans',
        );
    }

    public function content(): Content
    {
        $qrCodeUrls = [];
        $qrCodeInlines = [];

        foreach ($this->manualTickets as $ticket) {
            if (!$ticket || !$ticket instanceof TicketManual) {
                Log::error('Tiket manual tidak valid ditemukan: ', ['ticket' => $ticket]);
                throw new \Exception('Tiket manual tidak valid.');
            }

            $fileName = 'ticket_' . $ticket->id . '.png';
            $path = public_path('barcodes/' . $fileName);

            QrCode::format('png')->size(300)
                ->margin(10)
                ->generate($ticket->barcode, $path);

            $qrCodeUrls[] = asset('barcodes/' . $fileName);
            $qrCodeInlines[] = base64_encode(QrCode::format('png')->size(250)
                ->generate(route('ticket.show', ['ticket' => $ticket->id])));
        }

        return new Content(
            view: 'registerevents.ticket_payment_confirmation',
            with: [
                'tickets' => $this->manualTickets,
                'qrCodeUrls' => $qrCodeUrls,
                'qrCodeInlines' => $qrCodeInlines,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
