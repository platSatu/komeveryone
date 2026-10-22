<?php

namespace App\Jobs;

use App\Models\Kmd\KmdClassTutorial;
use App\Services\AdminCabang\KelasTutorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * kom_v02 — notifikasi email ke peserta beberapa menit sebelum kelas tutorial dimulai.
 *
 * Class ini sebelumnya DIPANGGIL oleh KelasTutorService::create() (jika "Aktifkan Notifikasi"
 * dicentang) tetapi file-nya tidak ada, sehingga menyimpan jadwal dengan notifikasi bisa error.
 */
class SendClassTutorialNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(public KmdClassTutorial $kelas)
    {
    }

    public function handle(KelasTutorService $service): void
    {
        $kelas = $this->kelas->fresh();

        // Jadwal sudah dihapus / dinonaktifkan → tidak perlu notifikasi
        if (! $kelas || $kelas->status !== 'active') {
            return;
        }

        $service->kirimNotifikasiPeserta($kelas);

        $kelas->forceFill(['notif_sent_at' => now()])->save();
    }
}
