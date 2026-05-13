<?php

namespace App\Services\Pengajar;

use App\Models\Kmd\KmdPertanyaanUser;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdUserClassTutorial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PengajarService
{
    /**
     * Ambil data pertanyaan yang dimiliki oleh pengajar yang login.
     * Admin cabang bisa melihat semua pertanyaan di cabangnya.
     */
    public function getPertanyaanPengajar($perPage = 15)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['pengajar', 'admin_cabang'])) {
            Log::warning('Unauthorized access to get pertanyaan', [
                'user_id' => $user->id ?? null,
                'role' => $user->role ?? null
            ]);
            return (new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage))->setPath(request()->url());
        }

        try {
            Log::info('Cek nilai filter query', [
                'user_id' => $user->id,
                'role' => $user->role,
                'cabang_id_user' => $user->cabang_id
            ]);

            $query = KmdPertanyaanUser::with(['user', 'class', 'modul', 'rayon']);

            if ($user->role === 'pengajar') {
                $query->where('pengajar_id', $user->id);
            } else if ($user->role === 'admin_cabang') {
                $query->where('rayon_id', $user->cabang_id);
            }

            $result = $query->orderBy('tgl_submit', 'desc')->paginate($perPage);

            if ($result->isEmpty()) {
                Log::info('Tidak ada pertanyaan ditemukan', [
                    'user_id' => $user->id,
                    'role' => $user->role
                ]);
            } else {
                Log::info('Pertanyaan ditemukan', [
                    'jumlah' => $result->total(),
                    'user_id' => $user->id
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error get pertanyaan', [
                'user_id' => $user->id,
                'role' => $user->role,
                'error' => $e->getMessage()
            ]);
            return (new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage))->setPath(request()->url());
        }
    }



    public function getKelasTutorialPengajarOrdered($orderBy = 'start_time', $direction = 'desc')
    {
        $user = Auth::user();

        $query = KmdClassTutorial::with(['modul', 'kelas', 'peserta.user', 'pertanyaan.user']);

        if ($user->role === 'pengajar') {
            $query->where('pengajar_id', $user->id);
        } elseif ($user->role === 'admin_cabang') {
            $query->where('rayon_id', $user->cabang_id);
        }

        return $query->orderBy($orderBy, $direction);
    }


    /**
     * Ambil kelas tutorial yang diajar oleh pengajar yang login
     * Admin cabang bisa melihat semua kelas di cabangnya.
     */
    public function getKelasTutorialPengajar()
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['pengajar', 'admin_cabang'])) {
            Log::warning('Unauthorized access to get kelas tutorial', [
                'user_id' => $user->id ?? null,
                'role' => $user->role ?? null,
                'allowed_roles' => ['pengajar', 'admin_cabang']
            ]);
            return collect();
        }

        try {
            $query = KmdClassTutorial::with(['kelas', 'modul', 'rayon'])
                ->where('rayon_id', $user->cabang_id)
                ->withCount('peserta'); // hitung jumlah peserta

            // Jika pengajar, filter berdasarkan pengajar_id
            // Jika admin_cabang, tampilkan semua di cabang tersebut
            if ($user->role === 'pengajar') {
                $query->where('pengajar_id', $user->id);
            }

            return $query->orderBy('start_time', 'desc')->get();

        } catch (\Exception $e) {
            Log::error('Error getting kelas tutorial', [
                'user_id' => $user->id,
                'role' => $user->role,
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    /**
     * Ambil detail kelas tutorial dengan pesertanya (VIEW ONLY)
     * Admin cabang bisa melihat detail semua kelas di cabangnya.
     */
    public function getDetailKelasTutorial($kelasId)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['pengajar', 'admin_cabang'])) {
            Log::warning('Unauthorized access to get detail kelas', [
                'user_id' => $user->id ?? null,
                'role' => $user->role ?? null,
                'kelas_id' => $kelasId,
                'allowed_roles' => ['pengajar', 'admin_cabang']
            ]);
            return null;
        }

        try {
            $query = KmdClassTutorial::with(['kelas', 'modul', 'rayon', 'pengajar'])
                ->where('id', $kelasId)
                ->where('rayon_id', $user->cabang_id);

            // Jika pengajar, filter berdasarkan pengajar_id
            // Jika admin_cabang, tidak perlu filter pengajar_id
            if ($user->role === 'pengajar') {
                $query->where('pengajar_id', $user->id);
            }

            $kelas = $query->first();

            if (!$kelas) {
                Log::warning('Kelas tutorial not found', [
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'kelas_id' => $kelasId,
                    'cabang_id' => $user->cabang_id
                ]);
                return null;
            }

            // Ambil peserta yang terdaftar di kelas ini
            $peserta = KmdUserClassTutorial::with(['user'])
                ->where('class_tutorial_id', $kelasId)
                ->orderBy('date_booking', 'desc')
                ->get();

            return [
                'kelas' => $kelas,
                'peserta' => $peserta,
                'jumlah_peserta' => $peserta->count()
            ];

        } catch (\Exception $e) {
            Log::error('Error getting detail kelas tutorial', [
                'user_id' => $user->id,
                'role' => $user->role,
                'kelas_id' => $kelasId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Ambil statistik untuk pengajar atau admin cabang
     * Admin cabang melihat statistik seluruh cabang, pengajar hanya miliknya
     */
    public function getStatistikPengajar()
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['pengajar', 'admin_cabang'])) {
            return [
                'total_pertanyaan' => 0,
                'total_peserta_bertanya' => 0,
                'total_modul' => 0,
                'pertanyaan_hari_ini' => 0
            ];
        }

        try {
            $pertanyaanQuery = KmdPertanyaanUser::where('rayon_id', $user->cabang_id);

            // Jika pengajar, filter berdasarkan pengajar_id
            // Jika admin_cabang, hitung semua di cabang tersebut
            if ($user->role === 'pengajar') {
                $pertanyaanQuery->where('pengajar_id', $user->id);
            }

            return [
                'total_pertanyaan' => $pertanyaanQuery->count(),
                'total_peserta_bertanya' => $pertanyaanQuery->distinct('user_id')->count(),
                'total_modul' => $pertanyaanQuery->distinct('modul_id')->count(),
                'pertanyaan_hari_ini' => $pertanyaanQuery->whereDate('tgl_submit', today())->count()
            ];

        } catch (\Exception $e) {
            Log::error('Error getting statistik', [
                'user_id' => $user->id,
                'role' => $user->role,
                'error' => $e->getMessage()
            ]);

            return [
                'total_pertanyaan' => 0,
                'total_peserta_bertanya' => 0,
                'total_modul' => 0,
                'pertanyaan_hari_ini' => 0
            ];
        }
    }
}
