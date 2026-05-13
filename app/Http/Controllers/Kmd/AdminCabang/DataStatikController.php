<?php

namespace App\Http\Controllers\Kmd\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kmd\KmdUserVideoProgress;
use App\Models\Kmd\KmdUserModul;
use App\Models\Kmd\KmdProgressModul;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DataStatikController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter dari request
        $filterType = $request->get('filter_type', 'month'); // 'month' atau 'year'
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        if ($filterType === 'year') {
            // Filter per tahun: tampilkan data per bulan dalam tahun tersebut
            $labels = [];
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = Carbon::create($year, $m, 1)->format('M');
            }

            // Inisialisasi data kosong 12 bulan
            $videoWatchedPerMonth = array_fill(0, 12, 0);
            $modulesCompletedPerMonth = array_fill(0, 12, 0);
            $progressStatusCountPerMonth = array_fill(0, 12, 0);

            // Query video watched per bulan di tahun $year
            $videoData = KmdUserVideoProgress::select(
                    DB::raw('MONTH(watched_at) as month'),
                    DB::raw('COUNT(*) as total_watched')
                )
                ->whereNotNull('watched_at')
                ->where('is_watched', true)
                ->whereYear('watched_at', $year)
                ->groupBy('month')
                ->get();

            foreach ($videoData as $item) {
                $videoWatchedPerMonth[$item->month - 1] = $item->total_watched;
            }

            // Query modul completed per bulan di tahun $year
            $modulData = KmdUserModul::select(
                    DB::raw('MONTH(completed_at) as month'),
                    DB::raw('COUNT(*) as total_completed')
                )
                ->whereNotNull('completed_at')
                ->where('is_completed', true)
                ->whereYear('completed_at', $year)
                ->groupBy('month')
                ->get();

            foreach ($modulData as $item) {
                $modulesCompletedPerMonth[$item->month - 1] = $item->total_completed;
            }

            // Query progress modul completed per bulan di tahun $year
            $progressData = KmdProgressModul::select(
                    DB::raw('MONTH(updated_at) as month'),
                    DB::raw('COUNT(*) as total_status')
                )
                ->where('status', 'completed')
                ->whereYear('updated_at', $year)
                ->groupBy('month')
                ->get();

            foreach ($progressData as $item) {
                $progressStatusCountPerMonth[$item->month - 1] = $item->total_status;
            }

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Video Watched',
                        'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                        'borderColor' => 'rgba(54, 162, 235, 1)',
                        'borderWidth' => 1,
                        'data' => $videoWatchedPerMonth,
                    ],
                    [
                        'label' => 'Modules Completed',
                        'backgroundColor' => 'rgba(255, 206, 86, 0.5)',
                        'borderColor' => 'rgba(255, 206, 86, 1)',
                        'borderWidth' => 1,
                        'data' => $modulesCompletedPerMonth,
                    ],
                    [
                        'label' => 'Progress Modul Completed',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'borderWidth' => 1,
                        'data' => $progressStatusCountPerMonth,
                    ],
                ],
            ];

        } else {
            // Filter per bulan: tampilkan data per hari dalam bulan dan tahun yang dipilih
            $daysInMonth = Carbon::create($year, $month)->daysInMonth;
            $labels = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $d;
            }

            // Inisialisasi data kosong per hari
            $videoWatchedPerDay = array_fill(0, $daysInMonth, 0);
            $modulesCompletedPerDay = array_fill(0, $daysInMonth, 0);
            $progressStatusCountPerDay = array_fill(0, $daysInMonth, 0);

            // Query video watched per hari di bulan dan tahun tertentu
            $videoData = KmdUserVideoProgress::select(
                    DB::raw('DAY(watched_at) as day'),
                    DB::raw('COUNT(*) as total_watched')
                )
                ->whereNotNull('watched_at')
                ->where('is_watched', true)
                ->whereYear('watched_at', $year)
                ->whereMonth('watched_at', $month)
                ->groupBy('day')
                ->get();

            foreach ($videoData as $item) {
                $videoWatchedPerDay[$item->day - 1] = $item->total_watched;
            }

            // Query modul completed per hari di bulan dan tahun tertentu
            $modulData = KmdUserModul::select(
                    DB::raw('DAY(completed_at) as day'),
                    DB::raw('COUNT(*) as total_completed')
                )
                ->whereNotNull('completed_at')
                ->where('is_completed', true)
                ->whereYear('completed_at', $year)
                ->whereMonth('completed_at', $month)
                ->groupBy('day')
                ->get();

            foreach ($modulData as $item) {
                $modulesCompletedPerDay[$item->day - 1] = $item->total_completed;
            }

            // Query progress modul completed per hari di bulan dan tahun tertentu
            $progressData = KmdProgressModul::select(
                    DB::raw('DAY(updated_at) as day'),
                    DB::raw('COUNT(*) as total_status')
                )
                ->where('status', 'completed')
                ->whereYear('updated_at', $year)
                ->whereMonth('updated_at', $month)
                ->groupBy('day')
                ->get();

            foreach ($progressData as $item) {
                $progressStatusCountPerDay[$item->day - 1] = $item->total_status;
            }

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Video Watched',
                        'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                        'borderColor' => 'rgba(54, 162, 235, 1)',
                        'borderWidth' => 1,
                        'data' => $videoWatchedPerDay,
                    ],
                    [
                        'label' => 'Modules Completed',
                        'backgroundColor' => 'rgba(255, 206, 86, 0.5)',
                        'borderColor' => 'rgba(255, 206, 86, 1)',
                        'borderWidth' => 1,
                        'data' => $modulesCompletedPerDay,
                    ],
                    [
                        'label' => 'Progress Modul Completed',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'borderWidth' => 1,
                        'data' => $progressStatusCountPerDay,
                    ],
                ],
            ];
        }

        // Kirim data filter ke view agar bisa ditampilkan di form
        return view('a_kom_digital_pusat.admin_cabang.data_statik', compact('chartData', 'filterType', 'year', 'month'));
    }
}
