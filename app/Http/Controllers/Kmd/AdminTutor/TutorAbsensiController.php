<?php

namespace App\Http\Controllers\Kmd\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\AdminCabang\AbsensiService;
use App\Services\AdminCabang\KelasTutorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    public function __construct(
        protected AbsensiService $absensiService,
        protected KelasTutorService $kelasTutorService
    ) {}

    /**
     * Tampilkan seluruh kelas tutorial yang dibuat oleh user yang login
     */
    public function index(): View
    {
        try {
            $user = Auth::user();
            
            // Get semua kelas tutorial yang dibuat oleh user yang login
            $kelasTutorialList = $this->kelasTutorService->getAllByUser($user->id);

            // Get statistik overall untuk dashboard
            $overallStats = $this->absensiService->getStatistikAbsensi($user->cabang_id);

            return view('a_kom_digital_pusat.admin_cabang.absensi.index', compact(
                'kelasTutorialList',
                'overallStats'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in absensi index', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return view('a_kom_digital_pusat.admin_cabang.absensi.index', [
                'kelasTutorialList' => collect([]),
                'overallStats' => [
                    'total' => 0,
                    'hadir' => 0,
                    'tidak_hadir' => 0,
                    'persentase_hadir' => 0,
                    'persentase_tidak_hadir' => 0,
                    'status_breakdown' => [
                        'normal' => 0,
                        'sakit' => 0,
                        'izin' => 0,
                        'alpha' => 0,
                    ]
                ],
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Tampilkan data absensi per kelas tutorial dengan filter
     */
    public function perKelas(Request $request): View
    {
        try {
            $user = Auth::user();
            
            // Get filter parameters
            $filters = [
                'class_tutorial_id' => $request->get('class_tutorial_id'),
                'status' => $request->get('status'),
                'kehadiran' => $request->get('kehadiran'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ];

            // Get data absensi per kelas tutorial
            $absensiData = $this->absensiService->getAbsensiPerKelas($user->cabang_id, $filters);
            
            // Get list kelas tutorial untuk filter dropdown
            $kelasTutorialList = $this->kelasTutorService->getAllByUser($user->id);

            // Get statistik jika ada filter kelas tertentu
            $statistik = null;
            if (!empty($filters['class_tutorial_id'])) {
                $statistik = $this->absensiService->getStatistikAbsensi($user->cabang_id, $filters['class_tutorial_id']);
            }

            return view('a_kom_digital_pusat.admin_cabang.absensi.per_kelas', compact(
                'absensiData',
                'kelasTutorialList', 
                'filters',
                'statistik'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in absensi perKelas', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return view('a_kom_digital_pusat.admin_cabang.absensi.per_kelas', [
                'absensiData' => collect([]),
                'kelasTutorialList' => collect([]),
                'filters' => [],
                'statistik' => null,
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Tampilkan summary/dashboard absensi
     */
    public function summary(): View
    {
        try {
            $user = Auth::user();
            
            // Get summary data per kelas
            $summaryData = $this->absensiService->getSummaryPerKelas($user->cabang_id);
            
            // Get overall statistik
            $overallStats = $this->absensiService->getStatistikAbsensi($user->cabang_id);

            return view('a_kom_digital_pusat.admin_cabang.absensi.summary', compact(
                'summaryData',
                'overallStats'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in absensi summary', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return view('a_kom_digital_pusat.admin_cabang.absensi.summary', [
                'summaryData' => collect([]),
                'overallStats' => [
                    'total' => 0,
                    'hadir' => 0,
                    'tidak_hadir' => 0,
                    'persentase_hadir' => 0,
                    'persentase_tidak_hadir' => 0,
                    'status_breakdown' => [
                        'normal' => 0,
                        'sakit' => 0,
                        'izin' => 0,
                        'alpha' => 0,
                    ]
                ],
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ]);
        }
    }
}
