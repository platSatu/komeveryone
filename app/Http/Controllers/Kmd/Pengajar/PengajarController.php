<?php

namespace App\Http\Controllers\Kmd\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdClassTutorial;
use App\Services\Pengajar\PengajarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PengajarController extends Controller
{
    protected $pengajarService;

    public function __construct(PengajarService $pengajarService)
    {
        $this->pengajarService = $pengajarService;
        // $this->middleware('auth:kom');
    }

    /**
     * Halaman dashboard pengajar
     */
    public function index()
    {
        try {
            $user = Auth::user();

            // Log info user login
            Log::info('Pengajar dashboard access attempt', [
                'user_id' => $user->id ?? null,
                'user_name' => $user->name ?? null,
                'role' => $user->role ?? null,
                'cabang_id' => $user->cabang_id ?? null,
                'email' => $user->email ?? null,
            ]);

            // Cek role pengajar atau admin_cabang
            if (!$user || !in_array($user->role, ['pengajar', 'superadmin'])) {
                Log::warning('Unauthorized access to pengajar dashboard', [
                    'user_id' => $user->id ?? null,
                    'role' => $user->role ?? null,
                    'allowed_roles' => ['pengajar', 'superadmin']
                ]);
                return redirect()->back()->with('error', 'Akses ditolak. Anda bukan pengajar atau admin cabang.');
            }

            // Ambil statistik pengajar
            $statistik = $this->pengajarService->getStatistikPengajar();

            // Ambil pertanyaan terbaru (limit 5)
            $pertanyaan = $this->pengajarService->getPertanyaanPengajar(5);
            if (!$pertanyaan || !method_exists($pertanyaan, 'count')) {
                $pertanyaan = collect();
            }

            Log::info('Pertanyaan terbaru count', [
                'count' => $pertanyaan->count(),
                'pertanyaan_ids' => $pertanyaan->pluck('id')->toArray(),
            ]);

            // Ambil kelas tutorial dengan eager loading peserta.user dan pertanyaan.user
            $kelasTutorial = $this->pengajarService->getKelasTutorialPengajarOrdered('start_time', 'desc')->paginate(12);

            if (!$kelasTutorial || !method_exists($kelasTutorial, 'count')) {
                $kelasTutorial = collect();
            }

            Log::info('Kelas tutorial fetched', [
                'count' => $kelasTutorial->count(),
                'kelas_ids' => $kelasTutorial->pluck('id')->toArray(),
            ]);

            // Siapkan detail peserta dan pertanyaan per kelas tutorial
            $kelasDetails = [];
            foreach ($kelasTutorial as $kelas) {
                // Pastikan relasi peserta dan pertanyaan sudah eager loaded
                $peserta = $kelas->relationLoaded('peserta') ? $kelas->peserta : $kelas->load('peserta.user')->peserta;
                $pertanyaanKelas = $kelas->relationLoaded('pertanyaan') ? $kelas->pertanyaan : $kelas->load('pertanyaan.user')->pertanyaan;

                Log::info("Kelas tutorial detail", [
                    'kelas_id' => $kelas->id,
                    'modul_name' => $kelas->modul->name ?? null,
                    'kelas_name' => $kelas->kelas->name ?? null,
                    'peserta_count' => $peserta->count(),
                    'peserta_ids' => $peserta->pluck('user_id')->toArray(),
                    'pertanyaan_count' => $pertanyaanKelas->count(),
                    'pertanyaan_ids' => $pertanyaanKelas->pluck('id')->toArray(),
                ]);

                $kelasDetails[$kelas->id] = [
                    'peserta' => $peserta,
                    'pertanyaan' => $pertanyaanKelas,
                ];
            }

            Log::info('User  accessed pengajar dashboard', [
                'user_id' => $user->id,
                'cabang_id' => $user->cabang_id ?? null,
                'role' => $user->role,
                'access_type' => $user->role === 'admin_cabang' ? 'admin_access' : 'pengajar_access',
                'pertanyaan_count' => $pertanyaan->count(),
                'kelas_count' => $kelasTutorial->count()
            ]);

            return view('a_kom_digital_pusat.pengajar.index', compact(
                'statistik',
                'pertanyaan',
                'kelasTutorial',
                'kelasDetails'
            ));

        } catch (\Exception $e) {
            Log::error('Error in pengajar index', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('a_kom_digital_pusat.pengajar.index', [
                'statistik' => [
                    'total_pertanyaan' => 0,
                    'total_kelas' => 0,
                    'total_peserta' => 0,
                    'pertanyaan_bulan_ini' => 0
                ],
                'pertanyaan' => collect(),
                'kelasTutorial' => collect(),
                'kelasDetails' => []
            ])->with('error', 'Terjadi kesalahan saat memuat dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Halaman daftar pertanyaan (VIEW ONLY)
     */
    public function pertanyaan(Request $request)
    {
        try {
            $user = Auth::user();

            // Cek role pengajar ATAU admin_cabang
            if (!$user || !in_array($user->role, ['pengajar', 'admin_cabang'])) {
                Log::warning('Unauthorized access to pertanyaan', [
                    'user_id' => $user->id ?? null,
                    'role' => $user->role ?? null,
                    'allowed_roles' => ['pengajar', 'admin_cabang']
                ]);
                return redirect()->back()->with('error', 'Akses ditolak. Anda bukan pengajar atau admin cabang.');
            }

            // Validasi parameter
            $perPage = $request->get('per_page', 15);
            $perPage = in_array($perPage, [10, 15, 25, 50]) ? $perPage : 15;

            $data = $this->pengajarService->getPertanyaanPengajar($perPage);
            $statistik = $this->pengajarService->getStatistikPengajar();

            Log::info('User accessed pertanyaan page', [
                'user_id' => $user->id,
                'cabang_id' => $user->cabang_id,
                'role' => $user->role,
                'per_page' => $perPage,
                'total_pertanyaan' => $data->total(),
                'access_type' => $user->role === 'admin_cabang' ? 'admin_access' : 'pengajar_access'
            ]);

            return view('a_kom_digital_pusat.pengajar.pertanyaan', compact('data', 'statistik'));

        } catch (\Exception $e) {
            Log::error('Error in pengajar pertanyaan', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat pertanyaan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman daftar kelas tutorial
     */
    public function kelasTutorial()
    {
        try {
            $user = Auth::user();

            // Cek role pengajar ATAU admin_cabang
            if (!$user || !in_array($user->role, ['pengajar', 'admin_cabang'])) {
                Log::warning('Unauthorized access to kelas tutorial', [
                    'user_id' => $user->id ?? null,
                    'role' => $user->role ?? null,
                    'allowed_roles' => ['pengajar', 'admin_cabang']
                ]);
                return redirect()->back()->with('error', 'Akses ditolak. Anda bukan pengajar atau admin cabang.');
            }

            $data = $this->pengajarService->getKelasTutorialPengajar();
            $statistik = $this->pengajarService->getStatistikPengajar();

            Log::info('User accessed kelas tutorial page', [
                'user_id' => $user->id,
                'cabang_id' => $user->cabang_id,
                'role' => $user->role,
                'total_kelas' => $data->count(),
                'access_type' => $user->role === 'admin_cabang' ? 'admin_access' : 'pengajar_access'
            ]);

            return view('a_kom_digital_pusat.pengajar.kelas_tutorial', compact('data', 'statistik'));

        } catch (\Exception $e) {
            Log::error('Error in pengajar kelas tutorial', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat kelas tutorial: ' . $e->getMessage());
        }
    }

    /**
     * Detail kelas tutorial dengan peserta (VIEW ONLY)
     */
    public function detailKelas($kelasId)
    {
        try {
            $user = Auth::user();

            // Cek role pengajar ATAU admin_cabang
            if (!$user || !in_array($user->role, ['pengajar', 'admin_cabang'])) {
                Log::warning('Unauthorized access to detail kelas', [
                    'user_id' => $user->id ?? null,
                    'role' => $user->role ?? null,
                    'kelas_id' => $kelasId,
                    'allowed_roles' => ['pengajar', 'admin_cabang']
                ]);
                return redirect()->back()->with('error', 'Akses ditolak. Anda bukan pengajar atau admin cabang.');
            }

            // Validasi kelasId
            if (!$kelasId || !is_string($kelasId)) {
                return redirect()->route('pengajar.kelas-tutorial')
                    ->with('error', 'ID kelas tidak valid.');
            }

            $data = $this->pengajarService->getDetailKelasTutorial($kelasId);

            if (!$data) {
                Log::warning('Kelas tutorial not found or no access', [
                    'user_id' => $user->id,
                    'kelas_id' => $kelasId,
                    'cabang_id' => $user->cabang_id,
                    'role' => $user->role
                ]);

                return redirect()->route('pengajar.kelas-tutorial')
                    ->with('error', 'Kelas tutorial tidak ditemukan atau Anda tidak memiliki akses.');
            }

            Log::info('User accessed kelas detail', [
                'user_id' => $user->id,
                'kelas_id' => $kelasId,
                'cabang_id' => $user->cabang_id,
                'role' => $user->role,
                'jumlah_peserta' => $data['jumlah_peserta'] ?? 0,
                'access_type' => $user->role === 'admin_cabang' ? 'admin_access' : 'pengajar_access'
            ]);

            return view('a_kom_digital_pusat.pengajar.detail_kelas', $data);

        } catch (\Exception $e) {
            Log::error('Error in pengajar detail kelas', [
                'user_id' => Auth::id(),
                'kelas_id' => $kelasId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('pengajar.kelas-tutorial')
                ->with('error', 'Terjadi kesalahan saat memuat detail kelas: ' . $e->getMessage());
        }
    }
}
