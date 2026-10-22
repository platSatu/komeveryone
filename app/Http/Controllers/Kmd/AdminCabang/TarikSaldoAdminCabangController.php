<?php

namespace App\Http\Controllers\Kmd\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\AdminCabang\TarikSaldoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KmdTarikSaldo;
use App\Models\Kmd\KmdUserRekening;
use App\Services\Payment\BizbosDisbursementService;

class TarikSaldoAdminCabangController extends Controller
{
    public function __construct(
        protected TarikSaldoService $tarikSaldoService,
        protected BizbosDisbursementService $payout
    ) {
    }

    /**
     * Tampilkan saldo per kelas tutorial dan riwayat penarikan
     */
    public function index(): View
    {
        try {
            $user = Auth::user();

            // Get saldo per kelas tutorial (hanya 4 kelas terbaru yang ditampilkan)
            $saldoPerKelas = $this->tarikSaldoService->getSaldoPerKelas($user->cabang_id, $user->id)
                ->sortByDesc(function ($item) {
                    return $item['class_tutorial']->created_at;
                })
                ->take(4)
                ->values();

            // Get riwayat penarikan (hanya 4 data terbaru, sudah diurutkan tanggal_penarikan desc oleh service)
            $riwayatPenarikan = $this->tarikSaldoService->getRiwayatPenarikan($user->cabang_id, $user->id)->take(4);

            // Get statistik overall
            $overallStats = $this->tarikSaldoService->getOverallStats($user->cabang_id, $user->id);

            $rekening = KmdUserRekening::where('user_id', $user->id)->first();
            $rekeningBelumSiap = $this->payout->aktif() && ! $rekening?->terverifikasi();

            return view('a_kom_digital_pusat.admin_cabang.tarik_saldo.index', compact(
                'saldoPerKelas',
                'riwayatPenarikan',
                'overallStats',
                'rekeningBelumSiap'
            ));

        } catch (\Exception $e) {
            Log::error('Error in tarik saldo index', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return view('a_kom_digital_pusat.admin_cabang.tarik_saldo.index', [
                'saldoPerKelas' => collect([]),
                'riwayatPenarikan' => collect([]),
                'overallStats' => [
                    'total_saldo_tersedia' => 0,
                    'total_sudah_ditarik' => 0,
                    'total_pending' => 0,
                    'jumlah_kelas' => 0
                ],
                'rekeningBelumSiap' => false,
                'error' => 'Terjadi kesalahan'
            ]);
        }
    }

    /**
     * Tarik saldo dari kelas tutorial tertentu
     */
 
    public function tarikSaldo(Request $request)
    {
        try {
            Log::info('[TARIK SALDO] Masuk ke method tarikSaldo', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'is_ajax' => $request->ajax() // Deteksi AJAX
            ]);

            // Validasi input (tambah catatan opsional dan numeric strict)
            Log::info('[TARIK SALDO] Mulai validasi input');
            $request->validate([
                'class_tutorial_id' => 'required|uuid|exists:kmd_class_tutorial,id',
                'jumlah' => 'required|numeric|min:1000|integer', // Tambah integer untuk pastikan angka bulat
                'catatan' => 'nullable|string|max:500',
            ], [
                'class_tutorial_id.exists' => 'Kelas tutorial tidak ditemukan di database.',
                'jumlah.min' => 'Jumlah minimal Rp 1.000.',
                'jumlah.numeric' => 'Jumlah harus berupa angka.',
                'catatan.max' => 'Catatan maksimal 500 karakter.',
            ]);
            Log::info('[TARIK SALDO] Validasi input berhasil');

            $user = Auth::user();
            if (!$user) {
                Log::error('[TARIK SALDO] User tidak terautentikasi');
                return response()->json([
                    'success' => false,
                    'message' => 'User  tidak terautentikasi. Silakan login kembali.'
                ], 401);
            }

            // Cek role user
            Log::info('[TARIK SALDO] Cek role user', ['role' => $user->role]);
            if ($user->role !== 'admin_cabang') {
                Log::warning('[TARIK SALDO] Role tidak sah untuk tarik saldo', [
                    'user_id' => $user->id,
                    'role' => $user->role
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan penarikan saldo. Hanya admin cabang yang bisa.'
                ], 403);
            }

            // Cek kelas tutorial (pastikan di mysql2)
            Log::info('[TARIK SALDO] Cari class tutorial', ['class_tutorial_id' => $request->class_tutorial_id]);
            $classTutorial = KmdClassTutorial::find($request->class_tutorial_id);
            if (!$classTutorial) {
                Log::warning('[TARIK SALDO] Class tutorial tidak ditemukan di mysql2');
                return response()->json([
                    'success' => false,
                    'message' => 'Kelas tutorial tidak ditemukan di database.'
                ], 404);
            }
            Log::info('[TARIK SALDO] Class tutorial ditemukan', ['name' => $classTutorial->name ?? 'N/A']);

            // Cek apakah kelas ini milik cabang user
            if ($user->cabang_id !== $classTutorial->rayon_id) {
                Log::warning('[TARIK SALDO] Cabang tidak cocok', [
                    'user_cabang_id' => $user->cabang_id,
                    'class_cabang_id' => $classTutorial->rayon_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke kelas tutorial ini. Cabang tidak cocok.'
                ], 403);
            }

            // Dana dikirim otomatis lewat Bizbos → rekening wajib sudah dicek ke bank
            if ($this->payout->aktif() && ! KmdUserRekening::where('user_id', $user->id)->first()?->terverifikasi()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rekening Anda belum diverifikasi. Buka menu Info Rekening dan klik "Verifikasi Rekening" dulu.'
                ], 422);
            }

            Log::info('[TARIK SALDO] Mulai transaction');
            DB::beginTransaction();

            // Kunci kelas supaya pengajuan ganda (klik dua kali) tidak lolos bersamaan
            KmdClassTutorial::whereKey($classTutorial->id)->lockForUpdate()->first();

            // Cek saldo tersedia via service
            Log::info('[TARIK SALDO] Cek saldo tersedia via service');
            $saldoTersedia = $this->tarikSaldoService->getSaldoKelas(
                $user->cabang_id,
                $user->id,
                $request->class_tutorial_id
            );

            if ($saldoTersedia === null || $saldoTersedia < 0) {
                Log::error('[TARIK SALDO] Saldo tersedia tidak valid dari service', ['saldo' => $saldoTersedia]);
                throw new \Exception('Gagal mengambil saldo tersedia dari service.');
            }

            Log::info('[TARIK SALDO] Saldo tersedia berhasil diambil', [
                'user_id' => $user->id,
                'cabang_id' => $user->cabang_id,
                'class_tutorial_id' => $request->class_tutorial_id,
                'saldo_tersedia' => $saldoTersedia,
                'jumlah_diminta' => $request->jumlah,
            ]);

            $jumlah = (int) $request->jumlah; // Cast ke int
            if ($jumlah > $saldoTersedia) {
                DB::rollBack();
                Log::warning('[TARIK SALDO] Jumlah melebihi saldo tersedia', [
                    'saldo_tersedia' => $saldoTersedia,
                    'jumlah_diminta' => $jumlah
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah penarikan melebihi saldo tersedia. Saldo tersedia: Rp ' . number_format($saldoTersedia, 0, ',', '.')
                ], 400);
            }

            // Cari baris peserta (userClassTutorial) di kelas ini untuk dijadikan penghubung
            // record penarikan ke kelas tutorial-nya. Admin cabang tidak harus terdaftar
            // sebagai peserta di kelasnya sendiri, jadi tidak dibatasi user_id.
            Log::info('[TARIK SALDO] Cek userClassTutorial untuk kelas ini');
            $userClassTutorial = KmdUserClassTutorial::where('class_tutorial_id', $request->class_tutorial_id)
                ->first();

            if (!$userClassTutorial) {
                DB::rollBack();
                Log::warning('[TARIK SALDO] Belum ada peserta terdaftar di kelas tutorial ini', [
                    'user_id' => $user->id,
                    'class_tutorial_id' => $request->class_tutorial_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada peserta terdaftar pada kelas tutorial ini.'
                ], 403);
            }

            // Pengajuan penarikan hanya boleh 1x per kelas tutorial (kecuali yang ditolak / gagal kirim)
            $sudahPernahMengajukan = KmdTarikSaldo::where('user_id', $user->id)
                ->where('userclasstutorial_id', $userClassTutorial->id)
                ->whereNotIn('status', KmdTarikSaldo::STATUS_KEMBALI)
                ->exists();

            if ($sudahPernahMengajukan) {
                DB::rollBack();
                Log::warning('[TARIK SALDO] Pengajuan ditolak, sudah pernah mengajukan untuk kelas ini', [
                    'user_id' => $user->id,
                    'userclasstutorial_id' => $userClassTutorial->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya dapat mengajukan penarikan 1 kali untuk kelas tutorial ini.'
                ], 400);
            }

            // Data untuk dikirim ke service
            $penarikanData = [
                'user_id' => $user->id,
                'cabang_id' => $user->cabang_id,
                'class_tutorial_id' => $request->class_tutorial_id,
                'user_class_tutorial_id' => $userClassTutorial->id,
                'jumlah' => $jumlah,
                'status' => 'pending',
                'tanggal_penarikan' => now(),
                'catatan' => $request->catatan ?? null,
            ];

            Log::info('[TARIK SALDO] Data akan dikirim ke service', $penarikanData);

            // Proses penarikan via service (dengan logging)
            Log::info('[TARIK SALDO] Memanggil service prosesPenarikan');
            $penarikan = $this->tarikSaldoService->prosesPenarikan($penarikanData);

            if (!$penarikan || !isset($penarikan->id) || empty($penarikan->id)) {
                DB::rollBack();
                Log::error('[TARIK SALDO] Service prosesPenarikan gagal return valid data', [
                    'penarikan_result' => $penarikan,
                    'data_sent' => $penarikanData
                ]);
                throw new \Exception('Gagal membuat record penarikan di service. Hubungi admin.');
            }

            // Commit transaction
            DB::commit();
            Log::info('[TARIK SALDO] Transaction committed, penarikan berhasil');

            Log::info('[TARIK SALDO] Penarikan berhasil diajukan', [
                'user_id' => $user->id,
                'penarikan_id' => $penarikan->id,
                'jumlah' => $jumlah,
                'status' => $penarikan->status ?? 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penarikan saldo berhasil diajukan. Menunggu persetujuan admin. ID Penarikan: ' . $penarikan->id,
                'data' => [
                    'penarikan_id' => $penarikan->id,
                    'status' => $penarikan->status ?? 'pending',
                    'saldo_sisa' => $saldoTersedia - $jumlah // Opsional, hitung sisa
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Rollback jika validasi gagal (jarang, tapi aman)
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::warning('[TARIK SALDO] Validasi gagal', [
                'user_id' => Auth::id(),
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Data input tidak valid: ' . implode(', ', $e->errors()),
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Rollback transaction
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('[TARIK SALDO] Exception terdeteksi', [
                'user_id' => Auth::id(),
                'request' => $request->all(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage() . '. Silakan coba lagi atau hubungi admin.',
                'debug' => config('app.debug') ? $e->getMessage() : null // Hanya tampil detail di development
            ], 500);
        }
    }
}








