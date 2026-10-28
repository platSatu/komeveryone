<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kmd\Superadmin\KomUserRequest;
use App\Http\Requests\Kmd\Superadmin\KomUserUpdateRequest;
use App\Services\Superadmin\KomUserService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\Models\Kmd\Cabang;
use App\Models\Kmd\KomUser;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdDonasi;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use App\Models\Kmd\KmdUserVideoProgress;
use App\Models\Kmd\KmdUserTutorialStatus;
use App\Models\Kmd\KmdUserReview;
use App\Models\Kmd\KmdUserQuestions;
use App\Models\Kmd\KmdUserModul;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KmdUserAbsensi;
use App\Models\Kmd\KmdProgressModul;
use App\Models\Kmd\KmdBooks;
use App\Models\Kmd\KmdPertanyaanUser;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;



class KomUserController extends Controller
{
    protected $service;

    public function __construct(KomUserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'role', 'status', 'cabang_id', 'asal_gereja']);
        $data = $this->service->getAll($filters);
        $cabangs = Cabang::all();

        // Tambahkan pengambilan data total
        $totalUsers = KomUser::count();
        $totalActiveKmdClassTutorial = KmdClassTutorial::where('status', 'active')->count();
        $totalDonations = KmdDonasi::sum('amount'); // sesuaikan nama kolom donasi

        return view('a_kom_digital_pusat.superadmin.user.index', compact(
            'data',
            'filters',
            'cabangs',
            'totalUsers',
            'totalActiveKmdClassTutorial',
            'totalDonations'
        ));
    }

    /**
     * kom_v02: daftar CK/Rayon untuk filter halaman user.
     * Dulu diambil langsung oleh browser dari gbigatsu.id, sering gagal (koneksi/CORS) sehingga muncul popup
     * "Gagal Memuat Data". Sekarang diambil server dan disimpan 6 jam; kalau API luar sedang bermasalah,
     * dipakai daftar terakhir yang berhasil, jadi halaman tetap jalan.
     */
    public function asalRayon()
    {
        $items = Cache::get('kom:asal-rayon');

        if ($items === null) {
            try {
                $data = Http::timeout(10)->acceptJson()->get('https://gbigatsu.id/api/kom-digital/api/asal-rayon')->json();
                $rows = $data['results'] ?? $data['data'] ?? (is_array($data) ? $data : []);
                $items = collect(is_array($rows) ? $rows : [])
                    ->map(fn ($item) => [
                        'id' => $item['id'] ?? $item['value'] ?? $item['kode'] ?? $item['nama'] ?? null,
                        'text' => $item['nama'] ?? $item['name'] ?? $item['text'] ?? null,
                    ])
                    ->filter(fn ($item) => $item['id'] !== null && filled($item['text']))
                    ->values()->all();
            } catch (\Throwable $e) {
                Log::warning('Gagal mengambil daftar asal-rayon: '.$e->getMessage());
                $items = [];
            }

            if ($items) {
                Cache::put('kom:asal-rayon', $items, now()->addHours(6));
                Cache::forever('kom:asal-rayon:last', $items);
            } else {
                $items = Cache::get('kom:asal-rayon:last', []);
            }
        }

        return response()->json(['results' => $items]);
    }

    public function rayonSearch(Request $request)
    {
        try {
            $searchTerm = $request->query('q');
            Log::info('RayonSearch called', ['query' => $searchTerm]);

            // Ambil data dari API eksternal
            $response = Http::timeout(30)->withOptions(['verify' => false])
                ->get('https://hmministry.net/api/cabang_api.php');

            if (!$response->successful()) {
                Log::error('API cabang gagal', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json(['results' => []]);
            }

            $cabangs = $response->json();

            if (!is_array($cabangs)) {
                Log::error('Data dari API bukan array', ['data' => $response->body()]);
                return response()->json(['results' => []]);
            }

            Log::info('Jumlah cabang sebelum filter', ['total' => count($cabangs)]);

            $filtered = collect($cabangs)->filter(function ($item) use ($searchTerm) {
                return stripos($item['nmcabang'] ?? '', $searchTerm) !== false;
            });

            Log::info('Jumlah cabang setelah filter', ['filtered' => $filtered->count()]);

            $results = $filtered->map(function ($item) {
                return [
                    'id' => $item['nmcabang'],
                    'text' => $item['nmcabang']
                ];
            })->values();

            Log::info('RayonSearch berhasil', ['result_count' => $results->count()]);

            return response()->json(['results' => $results]);

        } catch (\Exception $e) {
            Log::error('Exception saat rayonSearch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['results' => []], 500);
        }
    }

    private function getApiResponse($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            \Log::error('cURL error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return $response;
    }

    public function create()
    {
        $rayons = Cabang::all();
        return view('a_kom_digital_pusat.superadmin.user.create', compact('rayons'));
    }


    public function store(KomUserRequest $request)
    {
        $usersData = $request->validated()['users'];

        try {
            foreach ($usersData as $userData) {
                $this->service->store($userData);
            }

            return redirect()->route('kom.super_user.index')->with('success', 'Semua pengguna berhasil disimpan.');
        } catch (\Exception $e) {
            // Log error lengkap untuk debugging
            Log::error('Gagal menyimpan user: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $usersData,
            ]);

            // Tampilkan pesan error detail ke user (opsional)
            // Jika ingin tampilkan pesan error spesifik, gunakan ini:
            // return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());

            // Jika ingin tampilkan pesan error umum (lebih aman), gunakan ini:
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }


    public function show($id)
    {
        try {
            Log::info("Memulai proses show user", ['encrypted_id' => $id]);

            // Ganti decrypt ke decryptString
            $userId = Crypt::decryptString($id);
            Log::info("ID user berhasil didecrypt", ['user_id' => $userId]);

            $data = $this->service->find($userId);

            if (!$data) {
                Log::warning("User tidak ditemukan", ['user_id' => $userId]);
                return redirect()->route('kom.super_user.index')->with('error', 'User tidak ditemukan.');
            }

            // Eager load relasi
            $data->load([
                'progressVideos',
                'tutorialStatuses',
                'reviews',
                'questions',
                'modules',
                'progressModules',
                'classTutorials',
                'absensis',
                'progressModuls',
                'books',
                'pertanyaanUsers',
            ]);

            Log::info("Data user berhasil diload", [
                'user_id' => $userId,
                'relations_count' => [
                    'progressVideos' => $data->progressVideos->count(),
                    'tutorialStatuses' => $data->tutorialStatuses->count(),
                    'reviews' => $data->reviews->count(),
                    'questions' => $data->questions->count(),
                    'modules' => $data->modules->count(),
                    'progressModules' => $data->progressModules->count(),
                    'classTutorials' => $data->classTutorials->count(),
                    'absensis' => $data->absensis->count(),
                    'books' => $data->books->count(),
                    'pertanyaanUsers' => $data->pertanyaanUsers->count(),
                ]
            ]);

            return view('a_kom_digital_pusat.superadmin.user.show', compact('data'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan user', [
                'encrypted_id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('kom.super_user.index')
                ->with('error', 'Terjadi kesalahan saat menampilkan data. Silakan coba lagi.');
        }
    }



    public function edit($id)
    {
        try {
            // Ganti decrypt ke decryptString
            $decryptedId = Crypt::decryptString($id);
            $data = $this->service->find($decryptedId);
            $rayons = Cabang::all(); // Ambil semua data rayon

            return view('a_kom_digital_pusat.superadmin.user.edit', compact('data', 'rayons'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit user', [
                'encrypted_id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('kom.super_user.index')->with('error', 'Terjadi kesalahan saat menampilkan form edit. Silakan coba lagi.');
        }
    }



    public function updateStatus(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404); // atau response error lain
        }

        $user = KomUser::findOrFail($id);

        // kom_v02 (perbaikan): simpan 'aktif' / 'tidak_aktif' — sama dengan yang dicek saat login.
        // Dulu disimpan true/false ("1"/"0") sehingga user tetap ditolak "Akun Anda belum aktif".
        $aktif = $request->input('status') === 'aktif';
        $user->status = $aktif ? 'aktif' : 'tidak_aktif';
        if ($aktif && ! $user->email_verified_at) {
            $user->email_verified_at = now(); // diaktifkan superadmin = dianggap sudah terverifikasi
        }
        $user->save();

        return redirect()->back()->with('success', 'Status user berhasil diupdate.');
    }



    public function update(KomUserUpdateRequest $request, $id)
    {
        try {
            $this->service->update(Crypt::decrypt($id), $request->validated());
            return redirect()->route('kom.super_user.index')->with('success', 'Data berhasil diupdate.');
        } catch (\Exception $e) {
            \Log::error('Gagal mengupdate user: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete(Crypt::decryptString($id));
            return redirect()->route('kom.super_user.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.');
        }
    }


    // Fungsi reusable untuk reset data user
    protected function resetUserData($userId): void
    {
        try {
            DB::transaction(function () use ($userId) {
                \Log::info("Reset data for user_id: $userId");

                $deleted1 = KmdUserVideoProgress::where('user_id', $userId)->delete();
                $deleted2 = KmdUserTutorialStatus::where('user_id', $userId)->delete();
                $deleted3 = KmdUserReview::where('user_id', $userId)->delete();
                $deleted4 = KmdUserQuestions::where('user_id', $userId)->delete();
                $deleted5 = KmdUserModul::where('user_id', $userId)->delete();
                $deleted6 = KmdUserClassTutorial::where('user_id', $userId)->delete();
                $deleted7 = KmdUserAbsensi::where('user_id', $userId)->delete();
                $deleted8 = KmdProgressModul::where('user_id', $userId)->delete();
                $deleted9 = KmdBooks::where('user_id', $userId)->delete();
                $deleted11 = KmdDonasi::where('user_id', $userId)->delete();

                $deleted10 = KmdPertanyaanUser::where('user_id', $userId)->forceDelete();

                \Log::info("Deleted rows: video_progress=$deleted1, tutorial_status=$deleted2, review=$deleted3, questions=$deleted4, modul=$deleted5, class_tutorial=$deleted6, absensi=$deleted7, progress_modul=$deleted8, books=$deleted9, pertanyaan_user=$deleted10");

                \Log::info("Reset data completed for user_id: $userId");
            });
        } catch (\Exception $e) {
            \Log::error("Failed to reset data for user_id: $userId. Error: " . $e->getMessage());
            throw $e; // atau tangani sesuai kebutuhan
        }
    }



    // Contoh method controller untuk reset user berdasarkan encrypted id
    public function resetUser(string $encryptedId)
    {
        try {
            $userId = (int) Crypt::decrypt($encryptedId); // cast ke int
            $this->resetUserData($userId);
            return redirect()->back()->with('success', 'Data user berhasil direset.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mereset data user: ' . $e->getMessage());
        }
    }

    public function massAction(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'action' => 'required|string|in:reset,delete,activate,deactivate',
        ]);

        $action = $request->input('action');
        $encryptedUserIds = $request->input('user_ids', []);

        if (empty($encryptedUserIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu pengguna.');
        }

        if (!is_array($encryptedUserIds)) {
            $encryptedUserIds = [$encryptedUserIds];
        }

        $userIds = [];
        try {
            foreach ($encryptedUserIds as $encryptedId) {
                $userIds[] = Crypt::decryptString($encryptedId);
            }
        } catch (\Exception $e) {
            Log::error('Gagal mendekripsi user ID', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'ID pengguna tidak valid atau gagal didekripsi.');
        }


        // Lanjutkan proses sesuai $action dan $userIds
        try {
            switch ($action) {
                case 'reset':
                    foreach ($userIds as $id) {
                        $this->resetUserData($id); // Pastikan fungsi ini ada dan logging di dalamnya juga
                        Log::info('Reset user data', ['user_id' => $id]);
                    }
                    $message = 'Data pengguna terpilih berhasil direset.';
                    break;

                case 'delete':
                    KomUser::whereIn('id', $userIds)->delete();
                    Log::info('Deleted users', ['user_ids' => $userIds]);
                    $message = 'Pengguna terpilih berhasil dihapus.';
                    break;

                case 'aktif':
                    KomUser::whereIn('id', $userIds)->update(['status' => 'aktif']);
                    Log::info('Activated users', ['user_ids' => $userIds]);
                    $message = 'Pengguna terpilih berhasil diaktifkan.';
                    break;

                case 'nonaktif':
                    KomUser::whereIn('id', $userIds)->update(['status' => 'tidak_aktif']); // kom_v02: samakan dengan login
                    Log::info('Deactivated users', ['user_ids' => $userIds]);
                    $message = 'Pengguna terpilih berhasil dinonaktifkan.';
                    break;

                default:
                    Log::warning('Invalid mass action attempted', ['action' => $action]);
                    return redirect()->back()->with('error', 'Aksi tidak valid.');
            }
        } catch (\Exception $e) {
            Log::error('Exception during mass action', [
                'action' => $action,
                'user_ids' => $userIds,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menjalankan aksi.');
        }

        Log::info('Mass action completed successfully', [
            'action' => $action,
            'user_ids' => $userIds,
        ]);

        return redirect()->back()->with('success', $message);
    }



    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        try {
            $file = $request->file('file');
            $usersData = $this->parseFileToArray($file);

            if (empty($usersData)) {
                return redirect()->back()->with('error', 'File kosong atau format tidak sesuai.');
            }

            $importResult = $this->service->import($usersData);

            if ($importResult['failed'] > 0) {
                return redirect()->back()->with('error', 'Beberapa data gagal diimport.')->with('import_errors', $importResult['errors']);
            }

            return redirect()->route('kom.super_user.index')->with('success', 'Data berhasil diimport.');
        } catch (\Exception $e) {
            \Log::error('Gagal import data user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import data.');
        }
    }

    protected function parseFileToArray($file)
    {
        $array = Excel::toArray([], $file);
        return $array[0] ?? [];
    }

}


