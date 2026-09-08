<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\VisitorHistory;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Halaman "Statistik Pengunjung" di menu Superadmin -- jumlah kunjungan
 * dengan filter Minggu Ini/Bulan Ini/Tahun Ini/Rentang Tanggal (custom),
 * plus daftar halaman paling banyak dikunjungi pada rentang yang sama.
 * Data diambil dari App\Models\VisitorHistory yang dicatat
 * App\Http\Middleware\TrackVisitor pada setiap request GET halaman biasa.
 *
 * Ditambahkan 8 September 2026 (permintaan user). Pencatatan baru aktif
 * SEJAK middleware ini didaftarkan (lihat bootstrap/app.php) -- data
 * sebelum tanggal itu tidak ada/tidak tercatat.
 *
 * Update 8 September 2026: ditambahkan filter rentang tanggal custom
 * (period=custom&start=YYYY-MM-DD&end=YYYY-MM-DD), dan metrik "Pengunjung
 * Unik (IP)" dihapus dari tampilan sesuai permintaan user.
 */
class VisitorStatisticController extends Controller
{
    private const PERIODS = ['week', 'month', 'year', 'custom'];

    public function index(Request $request)
    {
        $period = $request->query('period', 'week');

        if (! in_array($period, self::PERIODS, true)) {
            $period = 'week';
        }

        $now = CarbonImmutable::now('Asia/Jakarta');

        $customStart = $request->query('start');
        $customEnd = $request->query('end');

        if ($period === 'custom' && $customStart && $customEnd) {
            try {
                $start = CarbonImmutable::parse($customStart, 'Asia/Jakarta')->startOfDay();
                $end = CarbonImmutable::parse($customEnd, 'Asia/Jakarta')->endOfDay();
            } catch (\Throwable $e) {
                $start = null;
                $end = null;
            }

            // Kalau tanggal tidak valid atau end < start, jatuhkan balik ke "Minggu Ini".
            if (! $start || ! $end || $end->lt($start)) {
                $period = 'week';
            }
        } elseif ($period === 'custom') {
            // period=custom dipilih tapi start/end belum diisi -> jatuhkan balik ke "Minggu Ini".
            $period = 'week';
        }

        if ($period !== 'custom') {
            [$start, $end] = match ($period) {
                'week' => [$now->startOfWeek(), $now->endOfWeek()],
                'month' => [$now->startOfMonth(), $now->endOfMonth()],
                'year' => [$now->startOfYear(), $now->endOfYear()],
            };
        }

        // Untuk rentang custom, kelompokkan per bulan kalau rentangnya panjang (> 62 hari)
        // supaya grafik tidak dipenuhi ratusan titik harian; selain itu per hari.
        if ($period === 'year') {
            $groupFormat = '%Y-%m';
            $labelFormat = 'M Y';
            $groupByMonth = true;
        } elseif ($period === 'custom' && $start->diffInDays($end) > 62) {
            $groupFormat = '%Y-%m';
            $labelFormat = 'M Y';
            $groupByMonth = true;
        } else {
            $groupFormat = '%Y-%m-%d';
            $labelFormat = 'd M';
            $groupByMonth = false;
        }

        $baseQuery = VisitorHistory::whereBetween('visited_at', [$start, $end]);

        $totalVisits = (clone $baseQuery)->count();

        $rows = (clone $baseQuery)
            ->selectRaw("DATE_FORMAT(visited_at, '{$groupFormat}') as period_key, COUNT(*) as total")
            ->groupBy('period_key')
            ->pluck('total', 'period_key');

        $chartLabels = [];
        $chartValues = [];
        $cursor = $start;

        while ($cursor->lte($end)) {
            $key = $groupByMonth ? $cursor->format('Y-m') : $cursor->format('Y-m-d');
            $chartLabels[] = $cursor->translatedFormat($labelFormat);
            $chartValues[] = (int) ($rows[$key] ?? 0);
            $cursor = $groupByMonth ? $cursor->addMonthNoOverflow() : $cursor->addDay();
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
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'topPages' => $topPages,
            'rangeLabel' => $start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y'),
            'customStart' => $start->format('Y-m-d'),
            'customEnd' => $end->format('Y-m-d'),
        ]);
    }
}
