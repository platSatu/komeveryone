<?php

namespace App\Http\Controllers\Kmd\AdminCabang;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdDonasi;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\AdminCabang\ReviewPengajarService;
use App\Services\AdminCabang\DonasiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Models\Kmd\KmdUserReview;

class ReviewPengajarAdminCabangController extends Controller
{
    public function __construct(
        protected ReviewPengajarService $reviewPengajarService,
        protected DonasiService $donasiService
    ) {
    }

    /**
     * Tampilkan semua review pengajar yang dibuat oleh user yang login
     */
    public function index(Request $request): View
    {
        try {
            $user = Auth::user();

            // Get filter parameters
            $filters = [
                'pengajar_id' => $request->get('pengajar_id'),
                'rating_filter' => $request->get('rating_filter'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ];

            $perPage = $request->get('per_page', 12);

            // Get review data grouped by pengajar
            $reviewData = $this->reviewPengajarService->getReviewsByUser($user->cabang_id, $filters);

            // Get list pengajar untuk filter dropdown
            $pengajarList = $this->reviewPengajarService->getPengajarList($user->cabang_id);

            // Get statistik overall
            $overallStats = $this->reviewPengajarService->getOverallStats($user->cabang_id);

            $groupedDonasi = $this->donasiService->getGroupedDonasiByCabang($user->cabang_id, $filters);


            return view('a_kom_digital_pusat.admin_cabang.review_pengajar.reviewpengajar', compact(
                
                'reviewData',
                'pengajarList',
                'filters',
                'overallStats',
                'groupedDonasi'
            ));

        } catch (\Exception $e) {
            Log::error('Error in review pengajar index', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return view('a_kom_digital_pusat.admin_cabang.review_pengajar.index', [
                'reviewData' => collect([]),
                'pengajarList' => collect([]),
                'filters' => [],
                'overallStats' => [
                    'total_reviews' => 0,
                    'total_pengajar' => 0,
                    'avg_rating' => 0,
                    'rating_breakdown' => [
                        'sangat_baik' => 0,
                        'baik' => 0,
                        'cukup' => 0,
                        'kurang' => 0,
                        'sangat_kurang' => 0,
                    ]
                ],
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ]);
        }
    }

    public function getGroupedDonasiByCabang(string $cabangId, array $filters = []): Collection
    {
        try {
            $query = KmdDonasi::with(['user', 'userClassTutorial'])
                ->whereHas('userClassTutorial.classTutorial', function ($q) use ($cabangId) {
                    $q->where('rayon_id', $cabangId);
                });

            if (!empty($filters['date_from'])) {
                $query->whereDate('transaction_time', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('transaction_time', '<=', $filters['date_to']);
            }

            if (!empty($filters['search'])) {
                $query->whereHas('userClassTutorial.user', function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%');
                });
            }

            $donations = $query->get();

            // Group berdasarkan user_class_tutorial_id
            return $donations->groupBy('user_class_tutorial_id')->map(function ($groupedDonasi) {
                return [
                    'user_class_tutorial' => $groupedDonasi->first()->userClassTutorial,
                    'total_amount' => $groupedDonasi->sum('amount'),
                    'total_donasi' => $groupedDonasi->count(),
                    'donasi_list' => $groupedDonasi
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error grouping donasi', [
                'cabang_id' => $cabangId,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }

    public function showAll(Request $request)
    {
        try {
            $filters = [
                'pengajar_id' => $request->get('pengajar_id'),
                'user_id' => $request->get('user_id'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ];

            // ✅ Panggil dari Service, bukan langsung
            $reviews = $this->reviewPengajarService->getAllReviews($filters);

            // Kelompokkan per Kelas Tutorial supaya semua peserta yang me-review
            // kelas tutorial yang sama muncul jadi satu baris di index, dengan
            // detail tiap peserta ditampilkan di dalam popup.
            $groupedReviews = $reviews
                ->groupBy(function ($review) {
                    return optional(optional($review->userClassTutorial)->classTutorial)->id ?? $review->pengajar_id;
                })
                ->map(function ($groupReviews) {
                    $first = $groupReviews->first();
                    $classTutorial = optional($first->userClassTutorial)->classTutorial;

                    return [
                        'modul' => optional(optional($classTutorial)->modul)->name ?? '-',
                        'kelas_tutorial' => optional($classTutorial)->topic ?? '-',
                        'pengajar' => optional($first->pengajar)->name ?? '-',
                        'reviews' => $groupReviews,
                        'latest_created_at' => $groupReviews->max('created_at'),
                    ];
                })
                ->sortByDesc('latest_created_at')
                ->take(4)
                ->values();

            return view('a_kom_digital_pusat.admin_cabang.review_pengajar.reviewpengajar', compact('groupedReviews', 'filters'));
        } catch (\Exception $e) {
            Log::error('Error loading all reviews', [
                'error' => $e->getMessage(),
            ]);

            return view('a_kom_digital_pusat.admin_cabang.review_pengajar.reviewpengajar', [
                'groupedReviews' => collect(),
                'filters' => [],
                'error' => 'Gagal memuat data review.',
            ]);
        }
    }

}
