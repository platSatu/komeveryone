<?php

namespace App\Services\AdminCabang;

use App\Models\Kmd\KmdUserReview;
use App\Models\Kmd\KomUser;
use App\Models\Kmd\KmdClassTutorial;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReviewPengajarService
{
    /**
     * Get reviews grouped by pengajar berdasarkan cabang user
     */
    public function getReviewsByUser(string $cabangId, array $filters = [])
    {
        try {
            $query = KmdUserReview::with([
                'user:id,name,email',
                'pengajar:id,name,email',
                'userClassTutorial.classTutorial' => function ($q) {
                    $q->with(['kelas:id,name', 'modul:id,name']);
                }
            ])
                ->whereHas('userClassTutorial.classTutorial', function ($q) use ($cabangId) {
                    $q->where('rayon_id', $cabangId);
                });

            // Apply filters
            if (!empty($filters['pengajar_id'])) {
                $query->where('pengajar_id', $filters['pengajar_id']);
            }

            if (!empty($filters['rating_filter'])) {
                $query->where('isi_pengajaran', $filters['rating_filter']);
            }

            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            $reviews = $query->orderBy('created_at', 'desc')->get();

            // Group by pengajar
            return $reviews->groupBy('pengajar_id')->map(function ($pengajarReviews) {
                $pengajar = $pengajarReviews->first()->pengajar;

                return [
                    'pengajar' => $pengajar,
                    'reviews' => $pengajarReviews,
                    'total_reviews' => $pengajarReviews->count(),
                    'avg_ratings' => $this->calculateAverageRatings($pengajarReviews),
                    'latest_review' => $pengajarReviews->first(),
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error getting reviews by user', [
                'cabang_id' => $cabangId,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate average ratings for a pengajar
     */
    private function calculateAverageRatings($reviews)
    {
        $ratingFields = [
            'isi_pengajaran',
            'kebenaran_teologis',
            'pembukaan_materi',
            'kesinambungan_materi',
            'kesaksian_pribadi'
        ];

        $averages = [];
        foreach ($ratingFields as $field) {
            $averages[$field] = $this->calculateFieldAverage($reviews, $field);
        }

        // Calculate overall average
        $averages['overall'] = collect($averages)->avg();

        return $averages;
    }

    /**
     * Calculate average for specific field
     */
    private function calculateFieldAverage($reviews, $field)
    {
        $ratingValues = [
            'sangat_kurang' => 1,
            'kurang' => 2,
            'cukup' => 3,
            'baik' => 4,
            'sangat_baik' => 5
        ];

        $total = 0;
        $count = 0;

        foreach ($reviews as $review) {
            if (isset($ratingValues[$review->$field])) {
                $total += $ratingValues[$review->$field];
                $count++;
            }
        }

        return $count > 0 ? round($total / $count, 2) : 0;
    }

    /**
     * Get list pengajar berdasarkan cabang
     */
    public function getPengajarList(string $cabangId)
    {
        try {
            return KomUser::where('cabang_id', $cabangId)
                ->where('role', 'pengajar')
                ->whereHas('pengajarReviews.userClassTutorial.classTutorial', function ($q) use ($cabangId) {
                    $q->where('rayon_id', $cabangId);
                })
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error getting pengajar list', [
                'cabang_id' => $cabangId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get overall statistics
     */
    public function getOverallStats(string $cabangId)
    {
        try {
            $totalReviews = KmdUserReview::whereHas('userClassTutorial.classTutorial', function ($q) use ($cabangId) {
                $q->where('rayon_id', $cabangId);
            })->count();

            $totalPengajar = KomUser::where('cabang_id', $cabangId)
                ->where('role', 'pengajar')
                ->whereHas('pengajarReviews.userClassTutorial.classTutorial', function ($q) use ($cabangId) {
                    $q->where('rayon_id', $cabangId);
                })
                ->count();

            // Calculate average rating
            $reviews = KmdUserReview::whereHas('userClassTutorial.classTutorial', function ($q) use ($cabangId) {
                $q->where('rayon_id', $cabangId);
            })->get();

            $avgRating = $this->calculateFieldAverage($reviews, 'isi_pengajaran');

            // Rating breakdown
            $ratingBreakdown = KmdUserReview::whereHas('userClassTutorial.classTutorial', function ($q) use ($cabangId) {
                $q->where('rayon_id', $cabangId);
            })
                ->select('isi_pengajaran', DB::raw('count(*) as total'))
                ->groupBy('isi_pengajaran')
                ->pluck('total', 'isi_pengajaran')
                ->toArray();

            return [
                'total_reviews' => $totalReviews,
                'total_pengajar' => $totalPengajar,
                'avg_rating' => $avgRating,
                'rating_breakdown' => [
                    'sangat_baik' => $ratingBreakdown['sangat_baik'] ?? 0,
                    'baik' => $ratingBreakdown['baik'] ?? 0,
                    'cukup' => $ratingBreakdown['cukup'] ?? 0,
                    'kurang' => $ratingBreakdown['kurang'] ?? 0,
                    'sangat_kurang' => $ratingBreakdown['sangat_kurang'] ?? 0,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting overall stats', [
                'cabang_id' => $cabangId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getAllReviews(array $filters = [])
{
    try {
        $user = auth()->user();

        $query = KmdUserReview::with([
            'user:id,name,email',
            'pengajar:id,name,email',
            'userClassTutorial.classTutorial.kelas:id,name',
            'userClassTutorial.classTutorial.modul:id,name',
        ]);

        // Filter berdasarkan cabang user yang login (khusus admin_cabang)
        if ($user && $user->role === 'admin_cabang') {
            $query->whereHas('userClassTutorial.classTutorial', function ($q) use ($user) {
                $q->where('rayon_id', $user->cabang_id); // <== PENTING: pastikan 'rayon_id' adalah cabang_id
            });
        }

        // Filter tambahan
        if (!empty($filters['pengajar_id'])) {
            $query->where('pengajar_id', $filters['pengajar_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();

    } catch (\Exception $e) {
        Log::error('Error getting all reviews', [
            'filters' => $filters,
            'error' => $e->getMessage(),
        ]);

        return collect(); // return kosong kalau error
    }
}




}
