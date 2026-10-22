<?php

namespace App\Services\Payment;

use App\Models\Kmd\KomPaymentSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Payment gateway yang dipakai untuk persembahan kelas tutorial & diklat.
 *
 *  - midtrans : seperti sebelumnya (kunci di .env → config/midtrans.php).
 *  - bizbos   : BB / Bizbos Invoice API (kunci diisi superadmin di menu Payment Gateway,
 *               secret disimpan TERENKRIPSI di tabel kom_payment_settings).
 *
 * Gateway dipilih superadmin. Persembahan yang sudah dibuat tetap diproses dengan gateway
 * yang tercatat di kmd_donations.payment_gateway, walaupun setting diganti di tengah jalan.
 */
class PaymentGatewaySetting
{
    public const MIDTRANS = 'midtrans';
    public const BIZBOS = 'bizbos';

    public const DAFTAR = [
        self::MIDTRANS => 'Midtrans',
        self::BIZBOS => 'BB (Bizbos)',
    ];

    protected const CACHE_KEY = 'kom_payment_settings_v1';

    /** key => rahasia? */
    protected const KEYS = [
        'gateway' => false,
        'bizbos_base_url' => false,
        'bizbos_api_key' => false,
        'bizbos_secret' => true,
        'bizbos_webhook_secret' => true,
    ];

    /** Gateway yang sedang aktif untuk persembahan baru. */
    public function aktif(): string
    {
        $gateway = $this->get('gateway') ?: self::MIDTRANS;

        if ($gateway === self::BIZBOS && ! $this->bizbosLengkap()) {
            Log::warning('[PaymentGateway] Bizbos dipilih tetapi kredensial belum lengkap — memakai Midtrans.');

            return self::MIDTRANS;
        }

        return array_key_exists($gateway, self::DAFTAR) ? $gateway : self::MIDTRANS;
    }

    public function nama(?string $gateway = null): string
    {
        return self::DAFTAR[$gateway ?: $this->aktif()] ?? 'Midtrans';
    }

    public function bizbosBaseUrl(): ?string
    {
        return self::rapikanBaseUrl($this->get('bizbos_base_url'));
    }

    /**
     * Base URL = alamat utama Bizbos, TANPA /api/v1.
     * Bizbos menampilkan "Base URL API" (https://.../api/v1); kalau itu yang disalin,
     * akhiran /api/v1 atau /api dibuang otomatis supaya alamat tidak dobel.
     */
    public static function rapikanBaseUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        $url = rtrim($url, '/');
        $url = preg_replace('#/api(/v1)?$#i', '', $url);

        return rtrim($url, '/');
    }

    public function bizbosApiKey(): ?string
    {
        return $this->get('bizbos_api_key');
    }

    public function bizbosSecret(): ?string
    {
        return $this->get('bizbos_secret');
    }

    public function bizbosWebhookSecret(): ?string
    {
        return $this->get('bizbos_webhook_secret');
    }

    public function bizbosLengkap(): bool
    {
        return $this->bizbosBaseUrl() && $this->bizbosApiKey() && $this->bizbosSecret() && $this->bizbosWebhookSecret();
    }

    public function midtransLengkap(): bool
    {
        return (bool) (config('midtrans.server_key') && config('midtrans.client_key'));
    }

    /** Apakah secret sudah pernah diisi (untuk form: tidak menampilkan isinya). */
    public function adaNilai(string $key): bool
    {
        return (string) $this->get($key) !== '';
    }

    public function get(string $key): ?string
    {
        $semua = $this->semua();

        return $semua[$key] ?? null;
    }

    /**
     * Simpan beberapa setting. Nilai null / '' untuk key rahasia = biarkan nilai lama.
     *
     * @param  array<string, string|null>  $data
     */
    public function simpan(array $data, ?string $userId = null): void
    {
        foreach ($data as $key => $value) {
            if (! array_key_exists($key, self::KEYS)) {
                continue;
            }

            $rahasia = self::KEYS[$key];
            $value = is_string($value) ? trim($value) : $value;
            if ($key === 'bizbos_base_url') {
                $value = self::rapikanBaseUrl($value);
            }

            if ($rahasia && ($value === null || $value === '')) {
                continue; // kosong = tidak diganti
            }

            KomPaymentSetting::updateOrCreate(['key' => $key], [
                'value' => $value === null || $value === '' ? null : ($rahasia ? Crypt::encryptString($value) : $value),
                'is_secret' => $rahasia,
                'updated_by' => $userId,
            ]);
        }

        Cache::forget(self::CACHE_KEY);
    }

    /** @return array<string, string|null> */
    protected function semua(): array
    {
        try {
            if (! Schema::hasTable('kom_payment_settings')) {
                return [];
            }

            $baris = Cache::remember(self::CACHE_KEY, 300, fn () => KomPaymentSetting::query()
                ->get(['key', 'value', 'is_secret'])
                ->map(fn ($s) => ['key' => $s->key, 'value' => $s->value, 'is_secret' => $s->is_secret])
                ->all());
        } catch (\Throwable $e) {
            return [];
        }

        $hasil = [];
        foreach ($baris as $s) {
            $nilai = $s['value'];
            if ($s['is_secret'] && $nilai) {
                try {
                    $nilai = Crypt::decryptString($nilai);
                } catch (\Throwable $e) {
                    Log::error('[PaymentGateway] Secret tidak bisa dibuka (APP_KEY berubah?).', ['key' => $s['key']]);
                    $nilai = null;
                }
            }
            $hasil[$s['key']] = $nilai;
        }

        return $hasil;
    }
}
