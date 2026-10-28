<?php

namespace App\Jobs\Kmd;

use App\Models\Kmd\KmdDonasi;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Services\AdminCabang\KelasTutorService;
use App\Services\Kmd\NotifikasiKelasService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * kom_v02 (performa) — pekerjaan SETELAH pendaftaran kelas tutorial lunas, dijalankan di queue:
 *   1. daftarkan peserta ke meeting Zoom (ambil link join pribadi)
 *   2. kirim email terima kasih (+ link Zoom bila sudah ada), hanya jika ada persembahan
 *
 * Sebelumnya dua hal ini dikerjakan langsung di dalam callback Midtrans, sehingga kalau
 * Zoom / server email lambat, callback ikut lambat dan menumpuk saat banyak yang membayar.
 *
 * Status lunas & kuota TETAP diproses langsung (KelasTutorialController::handlePaidStatus),
 * jadi peserta langsung tercatat terdaftar walaupun job ini baru berjalan beberapa detik kemudian.
 *
 * Aman diulang: pendaftaran Zoom dilewati jika peserta sudah punya link Zoom.
 */
class ProsesTutorialLunasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Dicoba ulang jika Zoom / server email sedang gangguan */
    public int $tries = 5;

    /** Jeda sebelum mencoba ulang (detik): 1 menit, 5 menit, 15 menit, 30 menit */
    public array $backoff = [60, 300, 900, 1800];

    public int $timeout = 120;

    public function __construct(
        public string $userClassTutorialId,
        public ?string $donasiId = null,
    ) {
    }

    public function handle(KelasTutorService $zoom): void
    {
        $booking = KmdUserClassTutorial::with(['user', 'classTutorial'])->find($this->userClassTutorialId);
        if (! $booking) {
            return;
        }

        $user = $booking->user;

        // Email & teks mengikuti bahasa belajar peserta
        if ($user && $user->language_code) {
            app()->setLocale($user->language_code);
        }

        // 1. Pendaftaran Zoom (lewati jika sudah pernah)
        $gagalZoom = null;
        $meetingId = optional($booking->classTutorial)->zoom_meeting_id;
        if (! $booking->zoom_join_url && ! $booking->zoom_registrant_id && $meetingId && $user) {
            try {
                $registrant = $zoom->registerZoomRegistrant($meetingId, $user->email, $user->name ?? 'Peserta');

                $booking->zoom_join_url = $registrant['join_url'] ?? null;
                $booking->zoom_registrant_id = $registrant['registrant_id'] ?? null;
                $booking->save();
            } catch (\Throwable $e) {
                $gagalZoom = $e; // dicoba ulang setelah email terima kasih tetap terkirim
            }
        }

        // 2. Email terima kasih (hanya untuk pendaftaran dengan persembahan).
        //    kom_v02: tetap terkirim walaupun Zoom gagal (link Zoom ada di menu "Kelas Tutorial Saya"),
        //    dan hanya sekali walaupun job dicoba ulang (dicatat di kom_notifikasi_terkirim).
        $donasi = $this->donasiId ? KmdDonasi::find($this->donasiId) : null;

        if ($donasi && optional($user)->email) {
            app(NotifikasiKelasService::class)->terimaKasihPeserta($booking, $donasi);
        } elseif ($this->donasiId) {
            Log::warning('[ProsesTutorialLunasJob] Email tidak dikirim karena data tidak lengkap.', [
                'booking_id' => $booking->id, 'donasi_id' => $this->donasiId, 'ada_email' => (bool) optional($user)->email,
            ]);
        }

        if ($gagalZoom) {
            throw $gagalZoom; // job otomatis dicoba ulang untuk pendaftaran Zoom
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('[ProsesTutorialLunasJob] Gagal setelah beberapa kali dicoba.', [
            'booking_id' => $this->userClassTutorialId,
            'donasi_id' => $this->donasiId,
            'pesan' => $e->getMessage(),
        ]);
    }
}
