<?php

namespace App\Services\AdminCabang;

use App\Models\Kmd\KmdTarikSaldo;
use App\Models\Kmd\KmdDonasi;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdUserClassTutorial;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TarikSaldoService
{
    /**
     * Get saldo per kelas tutorial berdasarkan cabang dan user
     */
    public function getSaldoPerKelas(?string $cabangId, string $userId)
    {
        if (empty($cabangId)) {
            throw new \InvalidArgumentException('cabangId tidak boleh null');
        }
        try {
            // Ambil semua kelas tutorial di rayon (cabang) tersebut
            $kelasTutorial = KmdClassTutorial::with(['pengajar:id,name', 'kelas:id,name', 'modul:id,name'])
                ->where('rayon_id', $cabangId)
                ->get();

            return $kelasTutorial->map(function ($kelas) use ($cabangId, $userId) {
                // Cari semua userClassTutorial yang terkait kelas ini
                $userClassTutorialIds = \App\Models\Kmd\KmdUserClassTutorial::where('class_tutorial_id', $kelas->id)
                    ->pluck('id')
                    ->toArray();

                // Query donasi untuk kelas dan cabang tersebut
                $donasiQuery = KmdDonasi::where('cabang_id', $cabangId)
                    ->whereIn('userclasstutorial_id', $userClassTutorialIds)
                    ->where('status', 'paid'); 

                // Total donasi langsung amount tanpa potongan fee
                $totalDonasi = $donasiQuery->sum('amount');

                // Tarikan yang sudah disetujui / selesai
                $sudahDitarik = KmdTarikSaldo::where('cabang_id', $cabangId)
                    ->where('user_id', $userId)
                    ->whereIn('userclasstutorial_id', $userClassTutorialIds)
                    ->whereIn('status', ['approved', 'completed'])
                    ->sum('jumlah');

                // Tarikan pending
                $pending = KmdTarikSaldo::where('cabang_id', $cabangId)
                    ->where('user_id', $userId)
                    ->whereIn('userclasstutorial_id', $userClassTutorialIds)
                    ->where('status', 'pending')
                    ->sum('jumlah');

                // Hitung saldo tersedia (total donasi - penarikan selesai - penarikan pending)
                $saldoTersedia = $totalDonasi - $sudahDitarik - $pending;

                // Hitung jumlah donatur unik
                $jumlahDonatur = $donasiQuery->distinct('user_id')->count('user_id');

                return [
                    'class_tutorial' => $kelas,
                    'total_donasi' => $totalDonasi,
                    'total_fee' => 0, // fee dihilangkan sesuai permintaan
                    'net_donasi' => $totalDonasi,
                    'sudah_ditarik' => $sudahDitarik,
                    'pending' => $pending,
                    'saldo_tersedia' => max(0, $saldoTersedia),
                    'jumlah_donatur' => $jumlahDonatur,
                    'dapat_ditarik' => ($saldoTersedia > 0)
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error getting saldo per kelas', [
                'cabang_id' => $cabangId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }




    /**
     * Get saldo untuk kelas tertentu
     */
    public function getSaldoKelas(string $cabangId, string $userId, string $classTutorialId)
{
    try {
        $user = Auth::guard('kom')->user();
        $isAdminCabang = $user && method_exists($user, 'hasRole') && $user->hasRole('admin_cabang');

        // Total donasi untuk kelas tutorial dan cabang
        $totalDonasi = KmdDonasi::where('cabang_id', $cabangId)
            ->whereHas('userClassTutorial', function ($q) use ($classTutorialId) {
                $q->where('class_tutorial_id', $classTutorialId);
            })
            ->sum('amount');

        // Query penarikan yang sudah approved atau completed
        $sudahDitarikQuery = KmdTarikSaldo::where('cabang_id', $cabangId)
            ->whereHas('userClassTutorial', function ($q) use ($classTutorialId) {
                $q->where('class_tutorial_id', $classTutorialId);
            })
            ->whereIn('status', ['approved', 'completed']);

        // Query penarikan yang masih pending
        $pendingQuery = KmdTarikSaldo::where('cabang_id', $cabangId)
            ->whereHas('userClassTutorial', function ($q) use ($classTutorialId) {
                $q->where('class_tutorial_id', $classTutorialId);
            })
            ->where('status', 'pending');

        // Jika bukan admin_cabang, batasi penarikan hanya untuk user_id yang bersangkutan
        if (!$isAdminCabang) {
            $sudahDitarikQuery->where('user_id', $userId);
            $pendingQuery->where('user_id', $userId);
        }

        $sudahDitarik = $sudahDitarikQuery->sum('jumlah');
        $pending = $pendingQuery->sum('jumlah');

        $saldo = max(0, $totalDonasi - $sudahDitarik - $pending);

        return $saldo;
    } catch (\Exception $e) {
        Log::error('Error getting saldo kelas', [
            'cabang_id' => $cabangId,
            'user_id' => $userId,
            'class_tutorial_id' => $classTutorialId,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}





    /**
     * Proses penarikan saldo
     */
    public function prosesPenarikan(array $data)
{
    try {
        Log::info('Mulai proses penarikan saldo', ['data' => $data]);

        // Cari relasi user dengan kelas tutorial
        $userClassTutorial = KmdUserClassTutorial::where('class_tutorial_id', $data['class_tutorial_id'])
            ->where('user_id', $data['user_id'])
            ->first();

        if (!$userClassTutorial) {
            $msg = 'User tidak terdaftar di kelas tutorial ini';
            Log::warning($msg, ['data' => $data]);
            throw new \Exception($msg);
        }

        // Buat record penarikan saldo
        $penarikan = KmdTarikSaldo::create([
            'id' => (string) Str::uuid(),
            'user_id' => $data['user_id'],
            'cabang_id' => $data['cabang_id'],
            'userclasstutorial_id' => $userClassTutorial->id,
            'tanggal_penarikan' => $data['tanggal_penarikan'] ?? now(),
            'jumlah' => $data['jumlah'],
            'status' => $data['status'] ?? 'pending',
            // 'catatan' dihapus karena tidak ada di model
        ]);

        Log::info('Penarikan saldo berhasil dibuat', ['penarikan_id' => $penarikan->id]);

        return $penarikan;
    } catch (\Exception $e) {
        Log::error('Error proses penarikan saldo', [
            'data' => $data,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        throw $e;
    }
}



    /**
     * Get riwayat penarikan
     */
    public function getRiwayatPenarikan(string $cabangId, string $userId)
    {
        try {
            return KmdTarikSaldo::with([
                'user:id,name,email',
                'userClassTutorial.classTutorial' => function ($q) {
                    $q->with(['pengajar:id,name', 'kelas:id,name', 'modul:id,name']);
                }
            ])
                ->where('cabang_id', $cabangId)
                ->where('user_id', $userId)
                ->orderBy('tanggal_penarikan', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error getting riwayat penarikan', [
                'cabang_id' => $cabangId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get overall statistics
     */
    public function getOverallStats(string $cabangId, string $userId)
    {
        try {
            $saldoPerKelas = $this->getSaldoPerKelas($cabangId, $userId);

            $totalSaldoTersedia = $saldoPerKelas->sum('saldo_tersedia');
            $totalSudahDitarik = $saldoPerKelas->sum('sudah_ditarik');
            $totalPending = $saldoPerKelas->sum('pending');
            $jumlahKelas = $saldoPerKelas->count();

            return [
                'total_saldo_tersedia' => $totalSaldoTersedia,
                'total_sudah_ditarik' => $totalSudahDitarik,
                'total_pending' => $totalPending,
                'jumlah_kelas' => $jumlahKelas
            ];
        } catch (\Exception $e) {
            Log::error('Error getting overall stats', [
                'cabang_id' => $cabangId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
