<?php

namespace App\Services\Payment;

use App\Http\Controllers\Kmd\Dashboard\KelasTutorialController;
use App\Models\Kmd\KmdDonasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * kom_v02 — Proses persembahan lewat BB (Bizbos).
 *
 * Status LUNAS yang sah HANYA dari webhook invoice.paid (terverifikasi) atau dari
 * GET /api/v1/invoices/{id} — bukan dari callback JavaScript popup.
 * Semua jalur (webhook, tombol cek, jadwal pembersihan) memakai sinkron() supaya hasilnya sama
 * dan idempoten (aman dipanggil berkali-kali).
 */
class BizbosPembayaranService
{
    public const PAID = 'paid';
    public const UNPAID = 'unpaid';
    public const EXPIRED = 'expired';
    public const CANCELLED = 'cancelled';

    public function __construct(protected BizbosClient $client)
    {
    }

    /** Buat invoice Bizbos untuk persembahan ini, simpan id & link bayarnya. */
    public function buatCheckout(KmdDonasi $donasi, $user, int $total, int $menitBerlaku, string $deskripsi, ?string $returnUrl = null): array
    {
        $invoice = $this->client->buatInvoice(array_filter([
            'external_id' => $donasi->order_id,
            'amount' => $total,
            'description' => mb_substr($deskripsi, 0, 255),
            'customer' => array_filter([
                'name' => $user->name ?: 'Peserta',
                'email' => $user->email ?: null,
                'phone' => $user->phone ?? $user->no_hp ?? null,
            ]),
            'expiry_minutes' => max(5, min(43200, $menitBerlaku)), // Bizbos: minimal 5 menit
            'return_url' => $returnUrl,
        ], fn ($v) => $v !== null));

        $donasi->update([
            'gateway_invoice_id' => $invoice['id'] ?? null,
            'checkout_url' => $invoice['checkout_url'] ?? null,
            'payment_token' => $invoice['id'] ?? null,
        ]);

        return $invoice;
    }

    /**
     * Cocokkan status persembahan dengan Bizbos. Kalau lunas → pendaftaran diproses lunas
     * (Zoom + email lewat queue, sama seperti Midtrans).
     *
     * @param  array|null  $invoice  objek invoice dari webhook; null = ambil dari API
     * @return string paid | unpaid | expired | cancelled
     */
    public function sinkron(KmdDonasi $donasi, ?array $invoice = null): string
    {
        $invoice ??= $this->client->invoice($donasi->gateway_invoice_id ?: $donasi->order_id);
        $status = (string) ($invoice['status'] ?? self::UNPAID);

        if ($status !== self::PAID) {
            if (in_array($status, [self::EXPIRED, self::CANCELLED], true) && $donasi->status !== 'paid') {
                $donasi->update(['transaction_status' => $status]);
            }

            return $status;
        }

        // Pastikan invoice ini memang milik persembahan ini & nominalnya cocok
        $harus = (int) round((float) $donasi->amount + (float) $donasi->fee_payment_gateway);
        $dibayar = (int) ($invoice['total'] ?? $invoice['amount'] ?? 0);
        if (($invoice['external_id'] ?? null) !== $donasi->order_id || $dibayar < $harus) {
            Log::critical('[Bizbos] Invoice lunas tetapi data tidak cocok — cek manual.', [
                'donasi_id' => $donasi->id, 'order_id' => $donasi->order_id,
                'external_id' => $invoice['external_id'] ?? null, 'dibayar' => $dibayar, 'harus' => $harus,
            ]);

            return self::UNPAID;
        }

        $donasiId = DB::connection('mysql')->transaction(function () use ($donasi, $invoice) {
            $kunci = KmdDonasi::lockForUpdate()->find($donasi->id);
            if (! $kunci) {
                return null;
            }

            if ($kunci->status !== 'paid') {
                $kunci->update([
                    'status' => 'paid',
                    'transaction_status' => 'settlement',
                    'payment_type' => $invoice['payment_method'] ?? 'bizbos',
                    'transaction_time' => now(),
                ]);
            }

            return $kunci->id;
        });

        if ($donasiId && $donasi->userclasstutorial_id) {
            if (\App\Models\Kmd\KmdUserClassTutorial::whereKey($donasi->userclasstutorial_id)->exists()) {
                // idempoten: kalau sudah lunas sebelumnya tidak diproses / email tidak dikirim ulang
                app(KelasTutorialController::class)->handlePaidStatus($donasi->userclasstutorial_id, $donasiId);
            } else {
                Log::critical('[Bizbos] Persembahan LUNAS tetapi pendaftaran kelas tutorial sudah tidak ada — proses manual.', [
                    'donasi_id' => $donasiId, 'order_id' => $donasi->order_id,
                ]);
            }
        }

        return self::PAID;
    }
}
