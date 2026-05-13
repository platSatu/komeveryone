<?php

namespace App\Services\AdminCabang;

use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KmdPertanyaanUser;
use App\Models\Kmd\KmdDonasi;
use App\Models\Kmd\KmdUserReview;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Jobs\SendClassTutorialNotificationJob;

class KelasTutorService
{
    /**
     * Ambil semua kelas tutorial milik user yang login
     */
    protected $zoomApiKey;
    protected $zoomApiSecret;
    protected $zoomAccountId;
    protected $httpClient;
    protected $baseUrl = 'https://api.zoom.us/v2';

    public function __construct()
    {
        $this->zoomApiKey = env('ZOOM_API_KEY');
        $this->zoomApiSecret = env('ZOOM_API_SECRET');
        $this->zoomAccountId = env('ZOOM_ACCOUNT_ID');
        $this->httpClient = new Client([
            'base_uri' => 'https://api.zoom.us/v2/',
            'timeout' => 10,
        ]);
    }

    // public function getAllByUser(string $userId)
    // {
    //     // Ambil data kelas tutorial milik user yang login
    //     return KmdClassTutorial::where('user_id', $userId)
    //         ->with(['pengajar', 'rayon', 'kelas', 'modul']) // eager loading relasi jika ada
    //         ->orderBy('created_at', 'desc')
    //         ->get();
    // }
    /**
 * ⭐ Get All Data - dengan Pagination
 */
    public function getAllByUser(string $userId, int $perPage = 4)
    {
        return KmdClassTutorial::where('user_id', $userId)
            ->with(['pengajar', 'rayon', 'kelas', 'modul'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);  // ⭐ Ganti dari ->get() ke ->paginate()
    }

    public function getPertanyaanByKelasUser(string $userId)
    {
        $kelasTutorialIds = KmdClassTutorial::where('user_id', $userId)->pluck('id');

        return KmdPertanyaanUser::with(['user', 'modul', 'kelasTutorial'])
            ->whereIn('kelas_tutorial_id', $kelasTutorialIds)
            ->latest()
            ->get();
    }

    // public function getPertanyaanByKelasTutorialId(string $kelasTutorialId)
    // {
    //     return KmdPertanyaanUser::with(['user', 'modul', 'kelasTutorial'])
    //         ->where('kelas_tutorial_id', $kelasTutorialId)
    //         ->latest()
    //         ->get();
    // }
    public function getPertanyaanByKelasTutorialId(string $kelasTutorialId)
    {
        // Cari user_id yang sudah melakukan donasi paid untuk kelas ini
        $paidUserIds = KmdDonasi::where('status', 'paid')
            ->whereHas('userClassTutorial', function ($query) use ($kelasTutorialId) {
                $query->where('class_tutorial_id', $kelasTutorialId);
            })
            ->pluck('user_id')
            ->unique()
            ->toArray();

        if (empty($paidUserIds)) {
            return collect(); // Tidak ada yang sudah bayar
        }

        // Ambil pertanyaan dari user yang sudah membayar & sesuai kelas tutorial
        return KmdPertanyaanUser::with(['user', 'modul', 'kelasTutorial'])
            ->where('kelas_tutorial_id', $kelasTutorialId)
            ->whereIn('user_id', $paidUserIds)
            ->latest()
            ->get();
    }



    public function getDonasiDetailByKelasTutorial(string $kelasTutorialId)
    {
        // Ambil class tutorial untuk relasi kelas dan modul
        $kelasTutorial = KmdClassTutorial::with(['kelas', 'modul'])->find($kelasTutorialId);

        if (!$kelasTutorial) {
            return null;
        }

        // Ambil semua donasi yang terkait dengan class tutorial (via kmd_user_class_tutorial)
        $donasiQuery = KmdDonasi::with(['user'])
            ->whereIn('userclasstutorial_id', function ($query) use ($kelasTutorialId) {
                $query->select('id')
                    ->from('kmd_user_class_tutorial')
                    ->where('class_tutorial_id', $kelasTutorialId);
            })
            ->get();

        // Hitung total donasi yang statusnya 'paid'
        $totalDonasiPaid = $donasiQuery->where('status', 'paid')->sum('amount');

        return [
            'kelasTutorial' => $kelasTutorial,
            'donasi' => $donasiQuery,
            'totalDonasiPaid' => $totalDonasiPaid,
        ];
    }


    public function getReviewByKelasTutorial(string $kelasTutorialId)
    {
        // Ambil data kelas tutorial lengkap (termasuk pengajar dan modul/kela s jika perlu)
        $kelasTutorial = KmdClassTutorial::with(['pengajar', 'kelas', 'modul'])->find($kelasTutorialId);

        if (!$kelasTutorial) {
            return null;
        }

        // Ambil semua review peserta di kelas tutorial ini
        $reviews = KmdUserReview::with(['user', 'userClassTutorial'])
            ->whereHas('userClassTutorial', function ($q) use ($kelasTutorialId) {
                $q->where('class_tutorial_id', $kelasTutorialId);
            })
            ->get();

        return [
            'kelasTutorial' => $kelasTutorial,
            'reviews' => $reviews,
        ];
    }


    public function getZoomRegistrants($meetingId)
    {
        $accessToken = $this->getAccessToken();
        $allRegistrants = [];
        $pageSize = 100;
        $nextPageToken = null;

        do {
            $params = [
                'page_size' => $pageSize,
            ];

            if ($nextPageToken) {
                $params['next_page_token'] = $nextPageToken;
            }

            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/meetings/{$meetingId}/registrants", $params);

            \Log::info("Zoom Registrants Raw Response: " . $response->body());

            if (!$response->successful()) {
                throw new \Exception('Gagal mengambil registrants Zoom: ' . $response->body());
            }

            $data = $response->json();

            // Gabungkan registrants ke array utama
            $registrants = $data['registrants'] ?? [];
            $allRegistrants = array_merge($allRegistrants, $registrants);

            // Pagination
            $nextPageToken = $data['next_page_token'] ?? null;

        } while (!empty($nextPageToken));

        \Log::info("Total Zoom Registrants Retrieved: " . count($allRegistrants));

        return $allRegistrants;
    }


    public function getZoomCloudRecordings($meetingId)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->get("{$this->baseUrl}/meetings/{$meetingId}/recordings");

        \Log::info("Zoom Cloud Recordings Response:", [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        if (!$response->successful()) {
            \Log::error('Gagal mengambil rekaman Zoom:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('Gagal mengambil rekaman Zoom: ' . $response->body());
        }

        $data = $response->json();

        // Kembalikan bagian penting dari data rekaman Zoom
        return [
            'uuid' => $data['uuid'] ?? null,
            'meeting_id' => $data['id'] ?? null,
            'topic' => $data['topic'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'recording_files' => $data['recording_files'] ?? [],
            'participant_audio_files' => $data['participant_audio_files'] ?? [],
            'download_access_token' => $data['download_access_token'] ?? null,
            'recording_play_passcode' => $data['recording_play_passcode'] ?? null,
        ];
    }



    /**
     * Ambil satu kelas tutorial berdasarkan user dan ID terenkripsi
     */
    public function find(string $encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Kalau decrypt gagal, lempar exception agar controller bisa tangani
            throw $e;
        }

        $tutorial = KmdClassTutorial::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$tutorial) {
            abort(404, 'Data tidak ditemukan.');
        }

        return $tutorial;
    }



    protected function getAccessToken()
    {
        $clientId = env('ZOOM_API_KEY');
        $clientSecret = env('ZOOM_API_SECRET');
        $accountId = env('ZOOM_ACCOUNT_ID');

        if (!$clientId || !$clientSecret || !$accountId) {
            throw new Exception('Konfigurasi Zoom API belum lengkap di .env');
        }

        $response = Http::asForm()->withBasicAuth($clientId, $clientSecret)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if (!$response->successful()) {
            throw new Exception('Gagal mengambil access token Zoom: ' . $response->body());
        }

        $data = $response->json();

        if (!isset($data['access_token'])) {
            throw new Exception('Response access token Zoom tidak valid.');
        }

        return $data['access_token'];
    }

    // public function createZoomMeeting($userEmail, $topic, $startTime, $duration = 60)
    // {
    //     $accessToken = $this->getAccessToken();

    //     $response = Http::withToken($accessToken)
    //         ->post("{$this->baseUrl}/users/{$userEmail}/meetings", [
    //             'topic' => $topic,
    //             'type' => 2,
    //             'start_time' => \Carbon\Carbon::parse($startTime)->toIso8601String(),
    //             'duration' => $duration,
    //             'timezone' => config('app.timezone', 'Asia/Jakarta'),
    //             'settings' => [
    //                 'join_before_host' => true,
    //                 'waiting_room' => true,
    //                 'approval_type' => 1,
    //                 'auto_recording' => 'cloud',
    //                 'show_share_button' => false,
    //                 'registration_type' => 1,

    //             ],
    //         ]);



    //     if (!$response->successful()) {
    //         throw new Exception('Gagal membuat meeting Zoom: ' . $response->body());
    //     }

    //     return $response->json();
    // }
    public function createZoomMeeting($userEmail, $topic, $startTime, $duration = 60, $alternativeHostEmail = null)
    {
        $accessToken = $this->getAccessToken();

        $payload = [
            'topic' => $topic,
            'type' => 2,
            'start_time' => \Carbon\Carbon::parse($startTime)->toIso8601String(),
            'duration' => $duration,
            'timezone' => config('app.timezone', 'Asia/Jakarta'),
            'settings' => [
                'join_before_host' => true,
                'waiting_room' => true,
                'approval_type' => 1,
                'auto_recording' => 'cloud',
                'show_share_button' => false,
                'registration_type' => 1,
            ],
        ];

        // if ($alternativeHostEmail) {
        //     $payload['settings']['alternative_hosts'] = $alternativeHostEmail;
        // }

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/users/{$userEmail}/meetings", $payload);

        if (!$response->successful()) {
            throw new Exception('Gagal membuat meeting Zoom: ' . $response->body());
        }

        return $response->json();
    }


    /**
     * Buat kelas tutorial baru
     */
    public function create(string $userId, array $data)
    {
        $data['id'] = (string) Str::uuid();
        $data['user_id'] = $userId;
        $data['jml_ikut'] = $data['jml_ikut'] ?? 0;
        $data['sisa_quota'] = $data['sisa_quota'] ?? $data['quota'] ?? 0;
        $data['notifikasi'] = $data['notifikasi'] ?? false;
        $notifMinutesBefore = $data['notif_minutes_before'] ?? 5; // default 5 menit sebelum

        $data['host_email'] = 'natanaeltamto@gmail.com';


        $user = \App\Models\Kmd\KomUser::findOrFail($userId);
        $data['rayon_id'] = $user->cabang_id;

        if (!($data['start_time'] instanceof Carbon)) {
            $localTime = Carbon::parse($data['start_time'], 'Asia/Jakarta');
        } else {
            $localTime = $data['start_time']->copy()->setTimezone('Asia/Jakarta');
        }

        $startTimeForZoom = $localTime->toIso8601String();

        //$alternativeHostEmail = $user->email;

        $zoomMeeting = $this->createZoomMeeting(
            $data['host_email'],
            $data['topic'],
            $startTimeForZoom,
            $data['duration'] ?? 60,
            //$alternativeHostEmail
        );

        if (isset($zoomMeeting['start_time'])) {
            $data['start_time'] = Carbon::parse($zoomMeeting['start_time'])
                ->setTimezone('Asia/Jakarta')
                ->format('Y-m-d H:i:s');
        } else {
            $data['start_time'] = $localTime->format('Y-m-d H:i:s');
        }

        $data['zoom_meeting_id'] = $zoomMeeting['id'] ?? null;
        $data['host_email'] = $zoomMeeting['host_email'] ?? $data['host_email'] ?? null;
        $data['start_url'] = $zoomMeeting['start_url'] ?? null;
        $data['join_url'] = $zoomMeeting['join_url'] ?? null;
        $data['topic'] = $zoomMeeting['topic'] ?? ($data['topic'] ?? null);
        $data['duration'] = $zoomMeeting['duration'] ?? ($data['duration'] ?? null);

        $kelas = KmdClassTutorial::create($data);

        if ($data['notifikasi']) {
            // Hitung waktu delay untuk job, notifMinutesBefore menit sebelum start_time
            $startAt = Carbon::parse($data['start_time'], 'Asia/Jakarta');
            $sendAt = $startAt->copy()->subMinutes($notifMinutesBefore);
            $now = Carbon::now('Asia/Jakarta');

            // Jika waktu kirim notif sudah lewat sekarang, langsung dispatch tanpa delay
            if ($sendAt->lessThanOrEqualTo($now)) {
                SendClassTutorialNotificationJob::dispatch($kelas);
            } else {
                SendClassTutorialNotificationJob::dispatch($kelas)->delay($sendAt);
            }
        }

        return $kelas;
    }

    // public function registerZoomRegistrant($meetingId, $email, $username)
    // {
    //     $accessToken = $this->getAccessToken();

    //     $username = trim($username) ?: 'Peserta';

    //     // Pisah username untuk first_name dan last_name
    //     $nameParts = preg_split('/[\s\.]+/', $username);
    //     $firstName = $nameParts[0];
    //     $lastName = $nameParts[1] ?? '';

    //     // Log data yang akan dikirim
    //     \Log::info('Mengirim registrasi Zoom:', [
    //         'meeting_id' => $meetingId,
    //         'email' => $email,
    //         'first_name' => $firstName,
    //         'last_name' => $lastName,
    //     ]);

    //     // Kirim request ke Zoom
    //     $response = Http::withToken($accessToken)
    //         ->post("{$this->baseUrl}/meetings/{$meetingId}/registrants", [
    //             'email' => $email,
    //             'first_name' => $firstName,
    //             'last_name' => $lastName,
    //         ]);

    //     // Log response dari Zoom
    //     \Log::info('Response dari Zoom:', [
    //         'status' => $response->status(),
    //         'body' => $response->json(),
    //     ]);

    //     if (!$response->successful()) {
    //         \Log::error('Gagal registrasi Zoom:', [
    //             'status' => $response->status(),
    //             'error_body' => $response->body()
    //         ]);
    //         throw new \Exception('Gagal mendaftarkan peserta ke Zoom: ' . $response->body());
    //     }

    //     return $response->json();
    // }
    public function registerZoomRegistrant($meetingId, $email, $username)
    {
        $accessToken = $this->getAccessToken();

        $username = trim($username) ?: 'Peserta';

        // Pisah username untuk first_name dan last_name
        $nameParts = preg_split('/[\s\.]+/', $username);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? $firstName;

        // Log data yang akan dikirim
        \Log::info('Mengirim registrasi Zoom:', [
            'meeting_id' => $meetingId,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'auto_approve' => true,
        ]);

        // Kirim request ke Zoom dengan auto_approve true dan header application/json
        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->baseUrl}/meetings/{$meetingId}/registrants", [
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'auto_approve' => true,  // agar langsung approved
            ]);

        // Log response dari Zoom
        \Log::info('Response dari Zoom:', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if (!$response->successful()) {
            \Log::error('Gagal registrasi Zoom:', [
                'status' => $response->status(),
                'error_body' => $response->body()
            ]);
            throw new \Exception('Gagal mendaftarkan peserta ke Zoom: ' . $response->body());
        }

        return $response->json();
    }





    // di KelasTutorService atau service Zoom kamu

    public function endZoomMeeting($meetingId)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->put("{$this->baseUrl}/meetings/{$meetingId}/status", [
                'action' => 'end'
            ]);

        if (!$response->successful()) {
            throw new Exception('Gagal mengakhiri meeting Zoom: ' . $response->body());
        }

        return $response->json(); // response biasanya kosong atau status info
    }

    public function updatePesertaStatusSelesai($classTutorialId)
    {
        KmdUserClassTutorial::where('class_tutorial_id', $classTutorialId)
            ->where('status', 'booked')
            ->update(['status' => 'selesai']);
    }

    protected function sendNotificationEmails(KmdClassTutorial $kelas)
    {
        $peserta = \App\Models\Kmd\KmdUserClassTutorial::where('class_tutorial_id', $kelas->id)
            ->where('status', 'aktif') // atau sesuai status valid
            ->get();

        foreach ($peserta as $p) {
            $user = $p->user;
            if (!$user || !$user->email)
                continue;

            \Illuminate\Support\Facades\Mail::to($user->email)
                ->queue(new \App\Mail\ClassTutorialNotificationMail($kelas, $user));
        }
    }


    /**
     * Update kelas tutorial milik user
     */
    public function update(string $encryptedId, array $data)
    {
        $tutorial = $this->find($encryptedId);

        $data['notifikasi'] = $data['notifikasi'] ?? false;
        $data['sisa_quota'] = $data['sisa_quota'] ?? $tutorial->sisa_quota;

        return $tutorial->update($data);
    }

    public function deleteZoomMeeting($meetingId)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->delete("{$this->baseUrl}/meetings/{$meetingId}");

        if (!$response->successful() && $response->status() !== 404) {
            // Kalau gagal, tapi bukan karena meeting sudah tidak ada (404), lempar error
            throw new Exception('Gagal menghapus Zoom meeting: ' . $response->body());
        }

        return true;
    }


    /**
     * Hapus kelas tutorial milik user
     */
    public function delete(string $userId, string $encryptedId)
    {
        $tutorial = $this->find($encryptedId);

        // Hapus meeting dari Zoom jika ada
        if ($tutorial->zoom_meeting_id) {
            try {
                $this->deleteZoomMeeting($tutorial->zoom_meeting_id);
            } catch (Exception $e) {
                \Log::warning('Gagal hapus Zoom meeting saat hapus kelas tutorial', [
                    'zoom_meeting_id' => $tutorial->zoom_meeting_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $tutorial->delete();
    }

   public function editZoomMeeting(string $encryptedId, array $data)
{
    $tutorial = $this->find($encryptedId);

    if (!$tutorial->zoom_meeting_id) {
        throw new \Exception('Meeting Zoom belum dibuat untuk kelas ini.');
    }

    $accessToken = $this->getAccessToken();

    $payload = [];

    // Update topic
    if (!empty($data['topic'])) {
        $payload['topic'] = $data['topic'];
    }

    // Update start time
    if (!empty($data['start_time'])) {
        $localTime = $data['start_time'] instanceof \Carbon\Carbon
            ? $data['start_time']->copy()->setTimezone('Asia/Jakarta')
            : \Carbon\Carbon::parse($data['start_time'], 'Asia/Jakarta');

        $payload['start_time'] = $localTime->copy()->setTimezone('UTC')->toIso8601String();
        $payload['timezone'] = 'Asia/Jakarta';

        // Simpan juga ke $data untuk update database lokal
        $data['start_time'] = $localTime->format('Y-m-d H:i:s');
    }

    // Tambahkan pengaturan lainnya jika perlu
    if (!empty($data['duration'])) {
        $payload['duration'] = $data['duration'];
    }

    if (!empty($data['agenda'])) {
        $payload['agenda'] = $data['agenda'];
    }

    if (!empty($payload)) {
        $response = Http::withToken($accessToken)
            ->patch("{$this->baseUrl}/meetings/{$tutorial->zoom_meeting_id}", $payload);

        if (!$response->successful()) {
            throw new \Exception('Gagal update Zoom meeting: ' . $response->body());
        }
    }

    // Update database lokal
    $tutorial->update($data);

    return $tutorial;
}


}
