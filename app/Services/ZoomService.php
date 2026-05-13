<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ZoomService
{
    protected $baseUrl = 'https://api.zoom.us/v2';

    /**
     * Ambil access token menggunakan Server-to-Server OAuth (account credentials grant)
     */
    protected function getAccessToken()
    {
        $clientId = env('ZOOM_API_KEY');        // Sesuai environment variable kamu
        $clientSecret = env('ZOOM_API_SECRET');
        $accountId = env('ZOOM_ACCOUNT_ID');    // Isi dengan Account ID Zoom dari dashboard JWT/Server-to-Server app

        if (!$clientId || !$clientSecret || !$accountId) {
            throw new \Exception('Konfigurasi Zoom API belum lengkap di .env');
        }

        $response = Http::asForm()->withBasicAuth($clientId, $clientSecret)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Gagal mengambil access token Zoom: ' . $response->body());
        }

        $data = $response->json();

        if (!isset($data['access_token'])) {
            throw new \Exception('Response access token Zoom tidak valid.');
        }

        return $data['access_token'];
    }

    /**
     * Buat Zoom meeting via API
     * 
     * @param string $userEmail Email host Zoom
     * @param string $topic Judul meeting
     * @param string|Carbon $startTime Waktu mulai meeting (string/timestamp)
     * @param int $duration Durasi meeting (menit)
     * @return array Response API Zoom (json)
     * @throws \Exception Jika gagal
     */
    public function createMeeting($userEmail, $topic, $startTime, $duration = 60)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/users/{$userEmail}/meetings", [
                'topic' => $topic,
                'type' => 2,
                'start_time' => \Carbon\Carbon::parse($startTime)->toIso8601String(),
                'duration' => $duration,
                'timezone' => config('app.timezone', 'Asia/Jakarta'),
                'settings' => [
                    'join_before_host' => true,
                    'waiting_room' => false,
                    'approval_type' => 0,
                ],
            ]);

        if (!$response->successful()) {
            throw new Exception('Gagal membuat meeting Zoom: ' . $response->body());
        }

        return $response->json();
    }
    /**
     * Update Zoom meeting
     * 
     * @param string $meetingId ID meeting Zoom (contoh: "1234567890")
     * @param array $data Data update (misal: topic, start_time, duration, settings, dsb)
     * @return bool true jika berhasil, exception jika gagal
     * @throws \Exception
     */
    public function updateMeeting(string $meetingId, array $data)
    {
        $accessToken = $this->getAccessToken();

        // Jika ada start_time, convert ke ISO8601
        if (isset($data['start_time'])) {
            $data['start_time'] = Carbon::parse($data['start_time'])->toIso8601String();
        }

        $response = Http::withToken($accessToken)
            ->patch("{$this->baseUrl}/meetings/{$meetingId}", $data);

        if (!$response->successful()) {
            throw new \Exception('Gagal mengupdate meeting Zoom: ' . $response->body());
        }

        return true;
    }

    /**
     * Ambil detail Zoom meeting berdasarkan meeting ID
     * 
     * @param string $meetingId
     * @return array Data meeting (response JSON dari Zoom)
     * @throws \Exception
     */
    public function getMeeting(string $meetingId)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->get("{$this->baseUrl}/meetings/{$meetingId}");

        if (!$response->successful()) {
            throw new \Exception('Gagal mengambil data meeting Zoom: ' . $response->body());
        }

        return $response->json();
    }
    /**
     * Hapus Zoom meeting berdasarkan meeting ID
     * 
     * @param string $meetingId
     * @return bool true jika berhasil, exception jika gagal
     * @throws \Exception
     */
    public function deleteMeeting(string $meetingId)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->delete("{$this->baseUrl}/meetings/{$meetingId}");

        if (!$response->successful()) {
            throw new \Exception('Gagal menghapus meeting Zoom: ' . $response->body());
        }

        return true;
    }


}
