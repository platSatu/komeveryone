<?php

namespace App\Services\AdminCabang;

use App\Models\Kmd\KmdDonasi;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdUserClassTutorial;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DonasiService
{
    /**
     * Terapkan filter umum ke query donasi
     */
    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['class_tutorial_id'])) {
            $query->whereHas('userClassTutorial', function($q) use ($filters) {
                $q->where('class_tutorial_id', $filters['class_tutorial_id']);
            });
        }

        if (!empty($filters['amount_min'])) {
            $query->where('amount', '>=', $filters['amount_min']);
        }

        if (!empty($filters['amount_max'])) {
            $query->where('amount', '<=', $filters['amount_max']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('transaction_time', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('transaction_time', '<=', $filters['date_to']);
        }

        if (!empty($filters['payment_type'])) {
            $query->where('payment_type', $filters['payment_type']);
        }

        return $query;
    }

    /**
     * Get donasi per kelas tutorial berdasarkan cabang user (status paid only)
     */
    public function getDonasiPerKelas(string $cabangId, array $filters = [])
    {
        try {
            $query = KmdDonasi::with([
                'user:id,name,email',
                'userClassTutorial.classTutorial' => function($q) {
                    $q->with(['pengajar:id,name', 'kelas:id,name', 'modul:id,name']);
                },
                'modul:id,name',
                'kelas:id,name'
            ])
            ->where('cabang_id', $cabangId) // Filter berdasarkan cabang user di tabel donasi
            ->where('status', 'paid') // Hanya donasi yang sudah dibayar
            ->where('transaction_status', 'settlement'); // Transaksi sukses

            $query = $this->applyFilters($query, $filters);

            $donasi = $query->orderBy('transaction_time', 'desc')->get();

            // Group donasi berdasarkan kelas tutorial
            return $donasi->groupBy(function($item) {
                return $item->userClassTutorial->classTutorial->id ?? 'no_class';
            })->map(function ($kelasDonasi, $kelasId) {
                $classTutorial = $kelasDonasi->first()->userClassTutorial->classTutorial ?? null;

                return [
                    'class_tutorial' => $classTutorial,
                    'donasi_list' => $kelasDonasi,
                    'total_donasi' => $kelasDonasi->count(),
                    'total_amount' => $kelasDonasi->sum('amount'),
                    'total_fee' => $kelasDonasi->sum('fee_payment_gateway'),
                    'net_amount' => $kelasDonasi->sum('amount') - $kelasDonasi->sum('fee_payment_gateway'),
                    'avg_amount' => $kelasDonasi->avg('amount'),
                    'latest_donasi' => $kelasDonasi->first(),
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error getting donasi per kelas', [
                'cabang_id' => $cabangId,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get list kelas tutorial berdasarkan cabang (rayon_id)
     */
    public function getKelasTutorialList(string $cabangId)
    {
        try {
            return KmdClassTutorial::with(['pengajar:id,name', 'kelas:id,name', 'modul:id,name'])
                ->where('rayon_id', $cabangId) // Rayon_id disini adalah cabang user
                ->whereHas('donations', function($q) {
                    $q->where('status', 'paid')
                      ->where('transaction_status', 'settlement');
                })
                ->orderBy('start_time', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error getting kelas tutorial list', [
                'cabang_id' => $cabangId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get overall statistics donasi berdasarkan cabang
     */
    public function getOverallStats(string $cabangId, array $filters = [])
    {
        try {
            $query = KmdDonasi::where('cabang_id', $cabangId)
                ->where('status', 'paid')
                ->where('transaction_status', 'settlement');

            $query = $this->applyFilters($query, $filters);

            $totalDonasi = $query->count();
            $totalAmount = $query->sum('amount');
            $totalFee = $query->sum('fee_payment_gateway');
            $netAmount = $totalAmount - $totalFee;
            $avgAmount = $totalDonasi > 0 ? $totalAmount / $totalDonasi : 0;

            // Breakdown payment type
            $paymentTypeBreakdown = (clone $query)
                ->select('payment_type', DB::raw('count(*) as total'), DB::raw('sum(amount) as total_amount'))
                ->groupBy('payment_type')
                ->get()
                ->keyBy('payment_type');

            // Statistik bulanan 6 bulan terakhir
            $monthlyStats = (clone $query)
                ->select(
                    DB::raw('YEAR(transaction_time) as year'),
                    DB::raw('MONTH(transaction_time) as month'),
                    DB::raw('count(*) as total_donasi'),
                    DB::raw('sum(amount) as total_amount')
                )
                ->where('transaction_time', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            return [
                'total_donasi' => $totalDonasi,
                'total_amount' => $totalAmount,
                'total_fee' => $totalFee,
                'net_amount' => $netAmount,
                'avg_amount' => $avgAmount,
                'payment_type_breakdown' => $paymentTypeBreakdown,
                'monthly_stats' => $monthlyStats
            ];
        } catch (\Exception $e) {
            Log::error('Error getting overall stats', [
                'cabang_id' => $cabangId,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
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

}
