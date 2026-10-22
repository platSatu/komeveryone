<?php

namespace App\Services\Kmd;

use App\Http\Controllers\Kmd\Dashboard\KelasTutorialController;
use App\Models\Kmd\KmdDonasi;
use App\Models\Kmd\KmdPertanyaanUser;
use App\Models\Kmd\KmdUserClassTutorial;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * kom_v02 — batas waktu "checkout" kelas tutorial.
 *
 * Alur: pilih jadwal → 2 pertanyaan → persembahan → link Zoom.
 * Masalah lama: peserta yang menutup pop-up Midtrans / tidak jadi membayar TERKUNCI
 * di halaman persembahan (middleware userprogress), dan admin harus menghapus manual.
 *
 * Sekarang:
 *  - langkah persembahan punya batas waktu (default 10 menit, config kom.checkout);
 *    link pembayaran Midtrans juga kedaluwarsa pada waktu yang sama;
 *  - lewat batas waktu → pendaftaran, pertanyaan, dan persembahan BELUM LUNAS dihapus,
 *    peserta kembali ke daftar jadwal kelas tutorial;
 *  - SEBELUM menghapus, status di Midtrans dicek: kalau ternyata sudah dibayar → diproses
 *    lunas (bukan dihapus); kalau masih diproses bank → ditunggu.
 *
 * Kuota tidak perlu dikembalikan: kuota baru berkurang saat pembayaran lunas.
 */
class CheckoutTutorialService
{
    public const DIHAPUS = 'dihapus';
    public const LUNAS = 'lunas';
    public const TUNGGU = 'tunggu';
    public const ABAIKAN = 'abaikan';

    /** Batas waktu langkah yang sedang dijalani peserta. */
    public function batasWaktu(KmdUserClassTutorial $booking): Carbon
    {
        if ($booking->step === 'donasi') {
            return $booking->checkout_expires_at
                ? $booking->checkout_expires_at->copy()
                : Carbon::parse($booking->updated_at ?? $booking->created_at)->addMinutes($this->menitPersembahan());
        }

        // langkah pilih / pertanyaan
        return Carbon::parse($booking->updated_at ?? $booking->created_at)
            ->addMinutes((int) config('kom.checkout.batas_pertanyaan_menit', 30));
    }

    public function menitPersembahan(): int
    {
        return max(1, (int) config('kom.checkout.batas_persembahan_menit', 10));
    }

    public function sudahKedaluwarsa(KmdUserClassTutorial $booking, bool $denganTenggang = false): bool
    {
        if ($booking->status_progress === 'paid') {
            return false;
        }

        $batas = $this->batasWaktu($booking);
        if ($denganTenggang) {
            $batas->addMinutes((int) config('kom.checkout.tenggang_menit', 5));
        }

        return now()->greaterThan($batas);
    }

    /**
     * Hapus pendaftaran yang belum lunas (beserta pertanyaan & persembahan yang belum lunas).
     *
     * @param  bool  $paksa  true = dibatalkan peserta sendiri (tidak menunggu batas waktu)
     * @return string  self::DIHAPUS | LUNAS | TUNGGU | ABAIKAN
     */
    public function bersihkan(KmdUserClassTutorial $booking, bool $paksa = false): string
    {
        if ($booking->status_progress === 'paid') {
            return self::ABAIKAN;
        }

        // 1. Cek dulu ke payment gateway: jangan sampai menghapus pembayaran yang sebenarnya masuk
        $donasiList = KmdDonasi::where('userclasstutorial_id', $booking->id)->get();
        foreach ($donasiList as $donasi) {
            // kom_v02: cek ke gateway tempat persembahan ini dibuat (Midtrans / BB Bizbos)
            $status = $donasi->payment_gateway === \App\Services\Payment\PaymentGatewaySetting::BIZBOS
                ? $this->statusBizbos($donasi)
                : $this->statusMidtrans($donasi);

            if (in_array($status, ['settlement', 'capture'], true)) {
                $donasi->update(['transaction_status' => $status, 'status' => 'paid']);
                app(KelasTutorialController::class)->handlePaidStatus($booking->id, $donasi->id);
                Log::warning('[Checkout] Pembayaran ternyata sudah masuk — diproses lunas, tidak dihapus.', [
                    'booking_id' => $booking->id, 'order_id' => $donasi->order_id,
                ]);

                return self::LUNAS;
            }

            if ($status === 'pending' && ! $paksa) {
                // VA / transfer sedang diproses bank → tunggu sampai lunas atau kedaluwarsa
                return self::TUNGGU;
            }

            if ($status === 'pending' && $paksa) {
                return self::TUNGGU; // peserta tidak bisa membatalkan pembayaran yang sedang diproses
            }
        }

        // 2. Hapus semuanya dalam satu transaksi
        DB::connection('mysql')->transaction(function () use ($booking) {
            $kunci = KmdUserClassTutorial::lockForUpdate()->find($booking->id);
            if (! $kunci || $kunci->status_progress === 'paid') {
                return;
            }

            // Pertanyaan dihapus, KECUALI peserta punya pendaftaran lain yang sudah lunas
            // di jadwal yang sama (pertanyaan itu milik pendaftaran yang lunas).
            $adaYangLunas = KmdUserClassTutorial::where('user_id', $kunci->user_id)
                ->where('class_tutorial_id', $kunci->class_tutorial_id)
                ->where('id', '!=', $kunci->id)
                ->where('status_progress', 'paid')
                ->exists();
            if (! $adaYangLunas) {
                KmdPertanyaanUser::where('user_id', $kunci->user_id)
                    ->where('kelas_tutorial_id', $kunci->class_tutorial_id)
                    ->delete();
            }

            KmdDonasi::where('userclasstutorial_id', $kunci->id)
                ->where('status', '!=', 'paid')
                ->delete();

            $kunci->delete();
        });

        Log::info('[Checkout] Pendaftaran kelas tutorial yang belum dibayar dihapus.', [
            'booking_id' => $booking->id, 'user_id' => $booking->user_id,
            'step' => $booking->step, 'dibatalkan_peserta' => $paksa,
        ]);

        return self::DIHAPUS;
    }

    /**
     * Dipanggil jadwal tiap menit (php artisan kom:bersihkan-checkout).
     *
     * @return array<string,int>
     */
    public function bersihkanSemua(): array
    {
        $hasil = [self::DIHAPUS => 0, self::LUNAS => 0, self::TUNGGU => 0, self::ABAIKAN => 0];

        // batas paling awal yang mungkin kedaluwarsa (hemat query)
        $minimal = min($this->menitPersembahan(), (int) config('kom.checkout.batas_pertanyaan_menit', 30));

        KmdUserClassTutorial::where('status_progress', 'pending')
            ->whereIn('step', ['pilih', 'pertanyaan', 'donasi'])
            ->where('updated_at', '<', now()->subMinutes($minimal))
            ->orderBy('updated_at')
            ->limit(200)
            ->get()
            ->each(function (KmdUserClassTutorial $booking) use (&$hasil) {
                if ($this->sudahKedaluwarsa($booking, true)) {
                    $hasil[$this->bersihkan($booking)]++;
                }
            });

        return $hasil;
    }

    /** Waktu kedaluwarsa link pembayaran Midtrans, sama dengan batas checkout. */
    public function expiryMidtrans(KmdUserClassTutorial $booking): array
    {
        $sisaMenit = (int) ceil(max(60, now()->diffInSeconds($this->batasWaktu($booking), false)) / 60);

        return [
            'start_time' => now()->format('Y-m-d H:i:s O'),
            'unit' => 'minute',
            'duration' => max(1, $sisaMenit),
        ];
    }

    /**
     * kom_v02 — Status persembahan BB (Bizbos), disamakan dengan istilah Midtrans:
     *  - 'settlement' : sudah lunas (langsung diproses lunas)
     *  - null         : belum dibayar & invoice berhasil dibatalkan / sudah kedaluwarsa → aman dihapus
     *  - 'pending'    : Bizbos tidak bisa dihubungi → tunda, coba lagi di jadwal berikutnya
     */
    protected function statusBizbos(KmdDonasi $donasi): ?string
    {
        if ($donasi->status === 'paid') {
            return 'settlement';
        }

        if (! $donasi->gateway_invoice_id) {
            return null; // invoice belum pernah dibuat
        }

        $bizbos = app(\App\Services\Payment\BizbosPembayaranService::class);

        try {
            $status = $bizbos->sinkron($donasi);

            if ($status === \App\Services\Payment\BizbosPembayaranService::PAID) {
                return 'settlement';
            }

            if ($status !== \App\Services\Payment\BizbosPembayaranService::UNPAID) {
                return null; // expired / cancelled
            }

            // Masih menunggu pembayaran → batalkan invoice dulu supaya tidak bisa dibayar setelah dihapus
            if (app(\App\Services\Payment\BizbosClient::class)->batalkan($donasi->gateway_invoice_id)) {
                return null;
            }

            // 409 = ternyata baru saja lunas
            return $bizbos->sinkron($donasi) === \App\Services\Payment\BizbosPembayaranService::PAID ? 'settlement' : 'pending';
        } catch (\Throwable $e) {
            Log::warning('[Checkout] Gagal cek status Bizbos, penghapusan ditunda.', [
                'order_id' => $donasi->order_id, 'pesan' => $e->getMessage(),
            ]);

            return 'pending';
        }
    }

    /**
     * Status transaksi di Midtrans: settlement | capture | pending | expire | cancel | deny | null (belum pernah dibayar).
     */
    protected function statusMidtrans(KmdDonasi $donasi): ?string
    {
        if ($donasi->status === 'paid') {
            return 'settlement';
        }

        // Pop-up dibuka tapi tidak pernah memilih metode pembayaran → tidak ada transaksi di Midtrans
        if (! $donasi->payment_token) {
            return null;
        }

        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production', true);

            $status = \Midtrans\Transaction::status($donasi->order_id);
            $status = is_object($status) ? ($status->transaction_status ?? null) : ($status['transaction_status'] ?? null);

            return $status;
        } catch (\Throwable $e) {
            // 404 = order belum pernah dibayar / tidak ada di Midtrans → aman dihapus
            if (str_contains($e->getMessage(), '404')) {
                return null;
            }

            // Midtrans tidak bisa dihubungi → jangan hapus dulu, coba lagi di jadwal berikutnya
            Log::warning('[Checkout] Gagal cek status Midtrans, penghapusan ditunda.', [
                'order_id' => $donasi->order_id, 'pesan' => $e->getMessage(),
            ]);

            return 'pending';
        }
    }
}
