<?php

namespace App\Services\AdminCabang;

use App\Models\Kmd\KmdUserAbsensi;
use App\Models\Kmd\KmdClassTutorial;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AbsensiService
{
    /**
     * Get absensi data per kelas tutorial berdasarkan cabang user
     * 
     * @param string $cabangId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAbsensiPerKelas(string $cabangId, array $filters = [])
    {
        try {
            $query = KmdUserAbsensi::with([
                'user:id,name,email',
                'userClassTutorial.classTutorial' => function($q) {
                    $q->with(['pengajar:id,name', 'kelas:id,name', 'modul:id,name']);
                }
            ])
            ->whereHas('userClassTutorial.classTutorial', function($q) use ($cabangId) {
                $q->where('rayon_id', $cabangId);
            });

            // Apply filters
            if (!empty($filters['class_tutorial_id'])) {
                $query->whereHas('userClassTutorial', function($q) use ($filters) {
                    $q->where('class_tutorial_id', $filters['class_tutorial_id']);
                });
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['kehadiran'])) {
                if ($filters['kehadiran'] === 'hadir') {
                    $query->where('hadir', true);
                } elseif ($filters['kehadiran'] === 'tidak_hadir') {
                    $query->where('tidak_hadir', true);
                }
            }

            if (!empty($filters['date_from'])) {
                $query->whereHas('userClassTutorial.classTutorial', function($q) use ($filters) {
                    $q->where('date_class', '>=', $filters['date_from']);
                });
            }

            if (!empty($filters['date_to'])) {
                $query->whereHas('userClassTutorial.classTutorial', function($q) use ($filters) {
                    $q->where('date_class', '<=', $filters['date_to']);
                });
            }

            return $query->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            Log::error('Error getting absensi per kelas', [
                'cabang_id' => $cabangId,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get statistik absensi berdasarkan cabang dan kelas tutorial
     * 
     * @param string $cabangId
     * @param string|null $classTutorialId
     * @return array
     */
    public function getStatistikAbsensi(string $cabangId, string $classTutorialId = null)
    {
        try {
            $query = KmdUserAbsensi::whereHas('userClassTutorial.classTutorial', function($q) use ($cabangId) {
                $q->where('rayon_id', $cabangId);
            });

            if ($classTutorialId) {
                $query->whereHas('userClassTutorial', function($q) use ($classTutorialId) {
                    $q->where('class_tutorial_id', $classTutorialId);
                });
            }

            $total = $query->count();
            $hadir = (clone $query)->where('hadir', true)->count();
            $tidakHadir = (clone $query)->where('tidak_hadir', true)->count();

            // Statistik berdasarkan status
            $statusStats = (clone $query)->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            return [
                'total' => $total,
                'hadir' => $hadir,
                'tidak_hadir' => $tidakHadir,
                'persentase_hadir' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0,
                'persentase_tidak_hadir' => $total > 0 ? round(($tidakHadir / $total) * 100, 2) : 0,
                'status_breakdown' => [
                    'normal' => $statusStats['normal'] ?? 0,
                    'sakit' => $statusStats['sakit'] ?? 0,
                    'izin' => $statusStats['izin'] ?? 0,
                    'alpha' => $statusStats['alpha'] ?? 0,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting statistik absensi', [
                'cabang_id' => $cabangId,
                'class_tutorial_id' => $classTutorialId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get summary absensi per kelas tutorial (untuk dashboard/overview)
     * 
     * @param string $cabangId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSummaryPerKelas(string $cabangId)
    {
        try {
            return KmdClassTutorial::with(['pengajar:id,name', 'kelas:id,name', 'modul:id,name'])
                ->where('rayon_id', $cabangId)
                ->withCount([
                    'userClassTutorials as total_peserta',
                    'userClassTutorials as hadir' => function($q) {
                        $q->whereHas('absensi', function($subQ) {
                            $subQ->where('hadir', true);
                        });
                    },
                    'userClassTutorials as tidak_hadir' => function($q) {
                        $q->whereHas('absensi', function($subQ) {
                            $subQ->where('tidak_hadir', true);
                        });
                    }
                ])
                ->orderBy('date_class', 'desc')
                ->get()
                ->map(function($kelas) {
                    $kelas->persentase_hadir = $kelas->total_peserta > 0 
                        ? round(($kelas->hadir / $kelas->total_peserta) * 100, 2) 
                        : 0;
                    return $kelas;
                });
        } catch (\Exception $e) {
            Log::error('Error getting summary per kelas', [
                'cabang_id' => $cabangId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
