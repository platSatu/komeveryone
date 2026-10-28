<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * kom_v02 — satu email untuk semua notifikasi kelas tutorial (isi disusun NotifikasiKelasService).
 * Hanya berisi teks (array), jadi ringan dimasukkan ke antrean.
 *
 * $isi: subjek, judul, sapaan, paragraf[], detail[label => nilai], tombol[label,url], catatan, aksen, ikon
 */
class KomNotifikasiMail extends Mailable
{
    use Queueable;

    public function __construct(public array $isi)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->isi['subjek']);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.kom.notifikasi', with: ['isi' => $this->isi]);
    }
}
