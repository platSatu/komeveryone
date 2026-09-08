<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\NginxAccessLog;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Halaman "Statistik Pengunjung (Log Nginx)" di menu Superadmin.
 *
 * Sumber datanya BEDA dari halaman "Statistik Pengunjung" biasa (yang dari
 * App\Models\VisitorHistory / middleware TrackVisitor): halaman ini dari
 * tabel nginx_access_logs, hasil parse file access.log nginx lewat
 * App\Console\Commands\ImportNginxLog yang dijadwalkan di routes/console.php.
 * Karena itu datanya mencakup SEMUA request yang sampai ke nginx (termasuk
 * file statis, bot, request yang gagal/diblokir sebelum sampai ke Laravel),
 * bukan cuma yang lewat middleware aplikasi.
 *
 * Dibuat 8 September 2026 (permintaan user).
 */
class NginxLogStatisticController extends Controller
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

            if (! $start || ! $end || $end->lt($start)) {
                $period = 'week';
            }
        } elseif ($period === 'custom') {
            $period = 'week';
        }

        if ($period !== 'custom') {
            [$start, $end] = match ($period) {
                'week' => [$now->startOfWeek(), $now->endOfWeek()],
                'month' => [$now->startOfMonth(), $now->endOfMonth()],
                'year' => [$now->startOfYear(), $now->endOfYear()],
            };
        }

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

        $assetExtensions = config('nginxlog.asset_extensions', []);
        $extPattern = $assetExtensions ? '\\.('.implode('|', array_map('preg_quote', $assetExtensions)).')$' : null;

        $baseQuery = NginxAccessLog::whereBetween('requested_at', [$start, $end]);

        // Total mentah -- SEMUA baris log di rentang ini (termasuk aset & bot).
        $totalRequests = (clone $baseQuery)->count();

        // "Kunjungan halaman" -- disaring supaya sebanding dengan definisi di
        // halaman Statistik Pengunjung biasa: GET, sukses/redirect, bukan aset.
        $pageQuery = (clone $baseQuery)
            ->where('method', 'GET')
            ->whereBetween('status_code', [200, 399]);

        if ($extPattern) {
            $pageQuery->where('path', 'not regexp', $extPattern);
        }

        $totalPageViews = (clone $pageQuery)->count();

        $rows = (clone $pageQuery)
            ->selectRaw("DATE_FORMAT(requested_at, '{$groupFormat}') as period_key, COUNT(*) as total")
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

        $topPages = (clone $pageQuery)
            ->select('path', DB::raw('COUNT(*) as total'))
            ->groupBy('path')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $lastImportedAt = NginxAccessLog::max('requested_at');

        return view('a_kom_digital_pusat.superadmin.nginx_log.index', [
            'period' => $period,
            'totalRequests' => $totalRequests,
            'totalPageViews' => $totalPageViews,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'topPages' => $topPages,
            'rangeLabel' => $start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y'),
            'customStart' => $start->format('Y-m-d'),
            'customEnd' => $end->format('Y-m-d'),
            'lastImportedAt' => $lastImportedAt,
        ]);
    }
}
