<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\VisitorHistory;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Halaman "Statistik Pengunjung" di menu Superadmin -- jumlah kunjungan
 * (total & pengunjung unik berdasarkan IP) dengan filter Minggu Ini/Bulan
 * Ini/Tahun Ini, plus daftar halaman paling banyak dikunjungi pada rentang
 * yang sama. Data diambil dari App\Models\VisitorHistory yang dicatat
 * App\Http\Middleware\TrackVisitor pada setiap request GET halaman biasa.
 *
 * Ditambahkan 8 September 2026 (permintaan user). Pencatatan baru aktif
 * SEJAK middleware ini didaftarkan (lihat bootstrap/app.php) -- data
 * sebelum tanggal itu tidak ada/tidak tercatat.
 */
class VisitorStatisticController extends Controller
{
    private const PERIODS = ['week', 'month', 'year'];

    public function index(Request $request)
    {
        $period = $request->query('period', 'week');

        if (! in_array($period, self::PERIODS, true)) {
            $period = 'week';
        }

        $now = CarbonImmutable::now('Asia/Jakarta');

        [$start, $end, $groupFormat, $labelFormat] = match ($period) {
            'week' => [$now->startOfWeek(), $now->endOfWeek(), '%Y-%m-%d', 'd M'],
            'month' => [$now->startOfMonth(), $now->endOfMonth(), '%Y-%m-%d', 'd M'],
            'year' => [$now->startOfYear(), $now->endOfYear(), '%Y-%m', 'M Y'],
        };

        $baseQuery = VisitorHistory::whereBetween('visited_at', [$start, $end]);

        $totalVisits = (clone $baseQuery)->count();
        $uniqueVisitors = (clone $baseQuery)->distinct('ip_address')->count('ip_address');

        $rows = (clone $baseQuery)
            ->selectRaw("DATE_FORMAT(visited_at, '{$groupFormat}') as period_key, COUNT(*) as total")
            ->groupBy('period_key')
            ->pluck('total', 'period_key');

        $chartLabels = [];
        $chartValues = [];
        $cursor = $start;

        while ($cursor->lte($end)) {
            $key = $period === 'year' ? $cursor->format('Y-m') : $cursor->format('Y-m-d');
            $chartLabels[] = $cursor->translatedFormat($labelFormat);
            $chartValues[] = (int) ($rows[$key] ?? 0);
            $cursor = $period === 'year' ? $cursor->addMonthNoOverflow() : $cursor->addDay();
        }

        $topPages = (clone $baseQuery)
            ->select('page_url', DB::raw('COUNT(*) as total'))
            ->groupBy('page_url')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('a_kom_digital_pusat.superadmin.visitor.index', [
            'period' => $period,
            'totalVisits' => $totalVisits,
            'uniqueVisitors' => $uniqueVisitors,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'topPages' => $topPages,
            'rangeLabel' => $start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y'),
        ]);
    }
}
