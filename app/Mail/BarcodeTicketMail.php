<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BarcodeTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    protected $barcodePath;

    public function __construct($ticket, $barcodePath)
    {
        $this->ticket = $ticket;
        $this->barcodePath = $barcodePath;
    }

    // Kita gunakan build() yang lebih fleksibel dan klasik
    public function build()
    {
        // Menyematkan barcode sebagai attachment
        $this->attach($this->barcodePath, [
            'as' => 'barcode.png',
            'mime' => 'image/png',
        ]);

        // Menyusun email dengan subjek, view, dan data yang dikirim
        return $this->subject('Ticket Payment Confirmation')
                    ->view('emails.ticket_barcode')
                    ->with([
                        'ticket' => $this->ticket,
                    ]);
    }


    public function attachments(): array
    {
        return [];
    }
}
