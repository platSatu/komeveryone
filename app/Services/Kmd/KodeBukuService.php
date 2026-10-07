<?php

namespace App\Services\Kmd;

use App\Models\Kmd\KmdBooks;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * kom_v02 — Pengecekan kode buku ke API hmministry.
 *
 * Logika dipindahkan apa adanya dari ValidasiKodeBukuController::validateKodeBuku()
 * supaya bisa dipakai ulang oleh halaman kode buku per kelas.
 * Catatan: API belum bisa membedakan buku Indonesia / English, jadi untuk
 * sementara hanya dicek: ada, valid, dan belum dipakai.
 */
class KodeBukuService
{
    /**
     * @return array{ok: bool, message: string}
     */
    public function verify(string $kode): array
    {
        $kode = trim($kode);

        try {
            $resp1 = Http::withOptions(['verify' => false])->timeout(15)
                ->get('https://hmministry.net/api/nobuku_api.php', ['no' => $kode, 'mod' => 'KOM']);

            Log::info('KodeBuku API 1', ['no_books' => $kode, 'body' => $resp1->body()]);

            if ($resp1->failed()) {
                return $this->fail('Layanan pengecekan kode buku sedang tidak bisa dihubungi. Silakan coba beberapa saat lagi.');
            }

            $data1 = $resp1->json();
            if (empty($data1['status']) || ! $data1['status'] || empty($data1['data'])) {
                return $this->fail('Kode buku tidak ditemukan. Periksa kembali kode yang tertera di buku Anda.');
            }

            $dataKode1 = $data1['data'][0]['nomer'] ?? null;
            if (! $dataKode1 || strtolower($dataKode1) !== strtolower($kode)) {
                return $this->fail('Kode buku tidak valid. Periksa kembali kode yang tertera di buku Anda.');
            }

            $resp2 = Http::withOptions(['verify' => false])->timeout(15)
                ->get('https://hmministry.net/api/nobuku_offline_api.php', ['nobukukom' => $kode]);

            Log::info('KodeBuku API 2', ['no_books' => $kode, 'body' => $resp2->body()]);

            if ($resp2->failed()) {
                return $this->fail('Layanan pengecekan kode buku sedang tidak bisa dihubungi. Silakan coba beberapa saat lagi.');
            }

            $data2 = $resp2->json();
            if (! empty($data2['status']) && $data2['status'] === true && ! empty($data2['data'])) {
                return $this->fail('Kode buku ini sudah pernah digunakan.');
            }

            $book = KmdBooks::where('no_books', $kode)->first();
            if ($book && $book->status === 'active') {
                return $this->fail('Kode buku ini sudah aktif dan dipakai oleh akun lain.');
            }

            return ['ok' => true, 'message' => 'Kode buku valid.'];
        } catch (\Throwable $e) {
            Log::error('KodeBukuService::verify', ['no_books' => $kode, 'error' => $e->getMessage()]);

            return $this->fail('Terjadi kendala saat memeriksa kode buku. Silakan coba lagi.');
        }
    }

    protected function fail(string $message): array
    {
        return ['ok' => false, 'message' => $message];
    }
}
