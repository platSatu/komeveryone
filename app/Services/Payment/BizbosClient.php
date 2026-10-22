<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * kom_v02 — Klien API Invoice BB (Bizbos).
 *
 * Mengikuti dokumentasi Bizbos (docs/invoice-api.md di aplikasi Bizbos):
 *  - Header: X-Bizbos-Key, X-Bizbos-Timestamp, X-Bizbos-Signature
 *  - Signature = hex HMAC-SHA256(secret, timestamp \n METHOD \n path \n body)
 *    path mulai dari /api/ (tanpa domain / folder instalasi), body persis yang dikirim.
 *  - Webhook: signature = hex HMAC-SHA256(webhook_secret, timestamp . "." . rawBody)
 */
class BizbosClient
{
    public function __construct(protected PaymentGatewaySetting $setting)
    {
    }

    /**
     * Buat invoice. external_id yang sama → invoice yang sama (aman diulang).
     *
     * @return array objek invoice (data)
     */
    public function buatInvoice(array $payload): array
    {
        return $this->kirim('POST', '/api/v1/invoices', $payload)['data'] ?? [];
    }

    /** @param string $id id Bizbos ATAU external_id */
    public function invoice(string $id): array
    {
        return $this->kirim('GET', '/api/v1/invoices/' . rawurlencode($id))['data'] ?? [];
    }

    /**
     * Batalkan invoice yang belum dibayar.
     *
     * @return bool true = berhasil dibatalkan / sudah batal; false = sudah LUNAS (409)
     */
    public function batalkan(string $id, string $alasan = 'Waktu persembahan habis'): bool
    {
        try {
            $this->kirim('POST', '/api/v1/invoices/' . rawurlencode($id) . '/cancel', ['reason' => $alasan]);

            return true;
        } catch (BizbosException $e) {
            if ($e->status === 409) {
                return false;
            }
            throw $e;
        }
    }

    public function saldo(): array
    {
        return $this->kirim('GET', '/api/v1/balance')['data'] ?? [];
    }

    /** Disbursement: daftar bank tujuan [{bank_code, bank_name}]. */
    public function banks(): array
    {
        return $this->kirim('GET', '/api/v1/banks')['data'] ?? [];
    }

    /** Disbursement: daftarkan & verifikasi rekening penerima. external_id sama = rekening yang sama. */
    public function daftarRekening(array $payload): array
    {
        return $this->kirim('POST', '/api/v1/payout-accounts', $payload)['data'] ?? [];
    }

    /** Disbursement: kirim dana. external_id sama → payout yang sama (aman diulang). */
    public function buatPayout(array $payload): array
    {
        return $this->kirim('POST', '/api/v1/payouts', $payload)['data'] ?? [];
    }

    /** @param string $id id Bizbos ATAU external_id */
    public function payout(string $id): array
    {
        return $this->kirim('GET', '/api/v1/payouts/' . rawurlencode($id))['data'] ?? [];
    }

    /** Periksa tanda tangan webhook (raw body sebelum di-parse). */
    public function webhookSah(string $rawBody, ?string $timestamp, ?string $signature): bool
    {
        $secret = (string) $this->setting->bizbosWebhookSecret();
        if ($secret === '' || ! $timestamp || ! $signature || ! ctype_digit($timestamp)) {
            return false;
        }

        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret), strtolower($signature));
    }

    protected function kirim(string $method, string $path, ?array $payload = null): array
    {
        $base = $this->setting->bizbosBaseUrl();
        $key = $this->setting->bizbosApiKey();
        $secret = $this->setting->bizbosSecret();

        if (! $base || ! $key || ! $secret) {
            throw new RuntimeException('Kredensial Bizbos belum lengkap. Isi di menu Payment Gateway.');
        }

        $method = strtoupper($method);
        $body = $payload === null ? '' : json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $timestamp = (string) time();
        $signature = hash_hmac('sha256', $timestamp . "\n" . $method . "\n" . $path . "\n" . $body, $secret);

        $request = Http::timeout(20)
            ->acceptJson()
            ->withHeaders([
                'X-Bizbos-Key' => $key,
                'X-Bizbos-Timestamp' => $timestamp,
                'X-Bizbos-Signature' => $signature,
            ]);

        if ($body !== '') {
            $request = $request->withBody($body, 'application/json'); // body SAMA dengan yang ditandatangani
        }

        $response = $request->send($method, $base . $path);
        $json = $response->json() ?? [];

        if (! $response->successful() || ($json['success'] ?? false) !== true) {
            throw new BizbosException(
                $json['message'] ?? ('Bizbos membalas HTTP ' . $response->status()),
                $response->status(),
                $json['errors'] ?? []
            );
        }

        return $json;
    }
}
