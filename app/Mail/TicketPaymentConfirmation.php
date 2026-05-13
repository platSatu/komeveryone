<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TicketPaymentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $tickets;

    /**
     * Create a new message instance.
     */
    public function __construct($tickets)
    {
        // Pastikan tiket yang diterima berupa array atau koleksi objek Ticket
        if (is_null($tickets) || !is_array($tickets) && !is_object($tickets)) {
            \Log::error('Data tiket tidak valid', ['tickets' => $tickets]);
            throw new \Exception('Tiket tidak valid atau data kosong.');
        }

        $this->tickets = $tickets;
    }



    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[CONFIRMATION] Pendaftaran Planetshakers Abundance Tour 2025 with Ps. Russell Evans',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Inisialisasi array untuk menyimpan QR code URLs
        $qrCodeUrls = [];
        $qrCodeInlines = [];

        // Pastikan tiket yang diterima adalah koleksi atau array yang valid
        if (empty($this->tickets) || !is_iterable($this->tickets)) {
            Log::error('Tiket tidak valid atau kosong ditemukan: ', ['tickets' => $this->tickets]);
            throw new \Exception('Tiket tidak valid atau kosong.');
        }

        // Loop melalui setiap tiket dalam koleksi
        foreach ($this->tickets as $ticket) {
            // Cek jika tiket tidak valid atau null
            if (!$ticket || !$ticket instanceof Ticket) {
                Log::error('Tiket tidak valid ditemukan: ', ['ticket' => $ticket]);
                throw new \Exception('Tiket tidak valid.');
            }

            // Tentukan nama file QR code (berdasarkan ID tiket)
            $fileName = 'ticket_' . $ticket->id . '.png';

            // Tentukan path untuk menyimpan QR code di folder public/barcodes
            $path = public_path('barcodes/' . $fileName);

            // Generate QR code dan simpan di folder public/barcodes
            QrCode::format('png')->size(300)
                ->margin(10)
                ->generate($ticket->barcode, $path);

            // Menambahkan URL QR code untuk tiket ini
            $qrCodeUrls[] = asset('barcodes/' . $fileName);

            // Generate QR code inline (base64) untuk tiket ini
            $qrCodeInlines[] = base64_encode(QrCode::format('png')->size(250)
                ->generate(route('ticket.show', ['ticket' => $ticket->id])));
        }

        return new Content(
            view: 'registerevents.ticket_payment_confirmation',
            with: [
                'tickets' => $this->tickets,  // Kirim koleksi tiket
                'qrCodeUrls' => $qrCodeUrls,  // QR code URL untuk semua tiket
                'qrCodeInlines' => $qrCodeInlines,  // QR code inline (base64) untuk semua tiket
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
