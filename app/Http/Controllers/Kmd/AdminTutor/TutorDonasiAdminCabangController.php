<?php

namespace App\Http\Controllers\Kmd\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\AdminCabang\DonasiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Kmd\KmdTarikSaldo;
use Illuminate\Support\Facades\Validator;
use App\Models\Kmd\KmdDonasi;



// class DonasiAdminCabangController extends Controller
// {
//     public function __construct(
//         protected DonasiService $donasiService
//     ) {}

//     /**
//      * Tampilkan donasi per kelas tutorial berdasarkan user dan cabang yang login
//      */
//     public function index(Request $request): View
//     {
//         try {
//             $user = Auth::user();

//             // Get filter parameters
//             $filters = [
//                 'class_tutorial_id' => $request->get('class_tutorial_id'),
//                 'amount_min' => $request->get('amount_min'),
//                 'amount_max' => $request->get('amount_max'),
//                 'date_from' => $request->get('date_from'),
//                 'date_to' => $request->get('date_to'),
//                 'payment_type' => $request->get('payment_type'),
//             ];

//             // Get donasi data per kelas tutorial
//             $donasiData = $this->donasiService->getDonasiPerKelas($user->cabang_id, $filters);

//             // Get list kelas tutorial untuk filter dropdown
//             $kelasTutorialList = $this->donasiService->getKelasTutorialList($user->cabang_id);

//             // Get statistik overall
//             $overallStats = $this->donasiService->getOverallStats($user->cabang_id, $filters);

//             return view('a_kom_digital_pusat.admin_cabang.persembahan.index', compact(
//                 'donasiData',
//                 'kelasTutorialList', 
//                 'filters',
//                 'overallStats'
//             ));

//         } catch (\Exception $e) {
//             Log::error('Error in donasi index', [
//                 'user_id' => Auth::id(),
//                 'error' => $e->getMessage()
//             ]);

//             return view('a_kom_digital_pusat.admin_cabang.persembahan.index', [
//                 'donasiData' => collect([]),
//                 'kelasTutorialList' => collect([]),
//                 'filters' => [],
//                 'overallStats' => [
//                     'total_donasi' => 0,
//                     'total_amount' => 0,
//                     'total_fee' => 0,
//                     'net_amount' => 0,
//                     'avg_amount' => 0,
//                     'payment_type_breakdown' => collect([]), 
//                     //'payment_type_breakdown' => [],
//                     //'monthly_stats' => []
//                     'monthly_stats' => collect([]),
//                 ],
//                 'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
//             ]);
//         }
//     }

//     public function testDonasiPolos()
// {
//     try {
//         $user = auth()->user();

//         // Pastikan user adalah admin cabang
//         if ($user->role !== 'admin_cabang') {
//             abort(403, 'Akses ditolak. Hanya admin cabang yang bisa mengakses data ini.');
//         }

//         // Ambil data donasi dengan relasi
//         $donasiData = \App\Models\Kmd\KmdDonasi::with([
//             'user:id,name',
//             'userClassTutorial',
//             'modul:id,name',
//             'kelas:id,name',
//             'cabang:id,name' // jika model Cabang ada dan punya kolom 'nama'
//         ])
//         ->where('cabang_id', $user->cabang_id)
//         ->orderBy('transaction_time', 'desc')
//         ->get();

//         return view('a_kom_digital_pusat.admin_cabang.persembahan.testDonasi', compact('donasiData'));

//     } catch (\Exception $e) {
//         return view('a_kom_digital_pusat.admin_cabang.persembahan.testDonasi', [
//             'donasiData' => collect([]),
//             'error' => 'Gagal mengambil data: ' . $e->getMessage()
//         ]);
//     }
// }

// }
class DonasiAdminCabangController extends Controller
{
    protected $donasiService;

    public function __construct(DonasiService $donasiService)
    {
        $this->donasiService = $donasiService;
    }

    // public function index(Request $request): View
    // {
    //     try {
    //         $user = Auth::user();

    //         $filters = [
    //             'class_tutorial_id' => $request->get('class_tutorial_id'),
    //             'amount_min' => $request->get('amount_min'),
    //             'amount_max' => $request->get('amount_max'),
    //             'date_from' => $request->get('date_from'),
    //             'date_to' => $request->get('date_to'),
    //             'payment_type' => $request->get('payment_type'),
    //         ];

    //         $donasiData = $this->donasiService->getDonasiPerKelas($user->cabang_id, $filters);
    //         $kelasTutorialList = $this->donasiService->getKelasTutorialList($user->cabang_id);
    //         $overallStats = $this->donasiService->getOverallStats($user->cabang_id, $filters);

    //         return view('a_kom_digital_pusat.admin_cabang.persembahan.index', compact(
    //             'donasiData',
    //             'kelasTutorialList',
    //             'filters',
    //             'overallStats'
    //         ));
    //     } catch (\Exception $e) {
    //         Log::error('Error in donasi index', [
    //             'user_id' => Auth::id(),
    //             'error' => $e->getMessage()
    //         ]);

    //         return view('a_kom_digital_pusat.admin_cabang.persembahan.index', [
    //             'donasiData' => collect([]),
    //             'kelasTutorialList' => collect([]),
    //             'filters' => [],
    //             'overallStats' => [
    //                 'total_donasi' => 0,
    //                 'total_amount' => 0,
    //                 'total_fee' => 0,
    //                 'net_amount' => 0,
    //                 'avg_amount' => 0,
    //                 'payment_type_breakdown' => collect([]),
    //                 'monthly_stats' => collect([]),
    //             ],
    //             'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
    //         ]);
    //     }
    // }
   public function index(Request $request)
{
    $user = auth()->user();

    // Query dasar
    $baseQuery = \App\Models\Kmd\KmdDonasi::with(['user', 'userClassTutorial'])
        ->where('cabang_id', $user->cabang_id);

    // Filter tanggal
    if ($request->filled('date_from')) {
        $baseQuery->whereDate('transaction_time', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $baseQuery->whereDate('transaction_time', '<=', $request->date_to);
    }

    // Data paginate
    $donasiData = (clone $baseQuery)->orderBy('transaction_time', 'desc')->paginate(10);

    // Total persembahan hanya status paid
    $totalPersembahan = (clone $baseQuery)
        ->where('status', 'paid')
        ->sum('amount');

    // Total kelas unik dari semua data (tidak berdasarkan status)
    $totalKelas = (clone $baseQuery)
        ->distinct('userclasstutorial_id')
        ->count('userclasstutorial_id');

    // Total user unik dari semua data
    $totalUser = (clone $baseQuery)
        ->distinct('user_id')
        ->count('user_id');

    // Group data berdasarkan pagination
    $groupedDonasi = $donasiData->groupBy('userclasstutorial_id')->map(function ($donasiGroup, $uctId) {
        return [
            'total_donasi' => $donasiGroup->count(),
            'total_amount' => $donasiGroup->sum('amount'),
            'donasi_list' => $donasiGroup,
            'user_class_tutorial' => $donasiGroup->first()->userClassTutorial,
        ];
    });

    return view('a_kom_digital_pusat.admin_cabang.persembahan.indexCabang', compact(
        'groupedDonasi',
        'totalPersembahan',
        'totalKelas',
        'totalUser',
        'donasiData'
    ));
}







public function tarikDana(Request $request)
{
    Log::info('Mulai proses tarikDana', ['request' => $request->all()]);

    // Validasi input dari request
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|uuid',
        'cabang_id' => 'required|uuid',
        'donasi_id' => 'nullable|uuid',
        'userclasstutorial_id' => 'required|uuid',
        'tanggal_penarikan' => 'required|date',
        'jumlah' => 'required|numeric|min:1',
        'catatan' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        Log::warning('Validasi input gagal', ['errors' => $validator->errors()]);
        return response()->json([
            'status' => 'error',
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }
    Log::info('Validasi input berhasil');

    // Hitung total donasi hanya berdasarkan userclasstutorial_id (kelas)
    $totalDonasi = KmdDonasi::where('userclasstutorial_id', $request->userclasstutorial_id)
        ->sum('amount');
    Log::info('Total donasi dihitung', ['totalDonasi' => $totalDonasi]);

    // Hitung total dana yang sudah ditarik (status pending atau approved) berdasarkan userclasstutorial_id
    $totalDitarik = KmdTarikSaldo::where('userclasstutorial_id', $request->userclasstutorial_id)
        ->whereIn('status', ['pending', 'approved'])
        ->sum('jumlah');
    Log::info('Total dana sudah ditarik dihitung', ['totalDitarik' => $totalDitarik]);

    $saldoTersedia = $totalDonasi - $totalDitarik;
    Log::info('Saldo tersedia dihitung', ['saldoTersedia' => $saldoTersedia]);

    if ($request->jumlah > $saldoTersedia) {
        Log::error('Jumlah penarikan melebihi saldo tersedia', [
            'jumlahDiminta' => $request->jumlah,
            'saldoTersedia' => $saldoTersedia
        ]);
        return response()->json([
            'status' => 'error',
            'message' => 'Jumlah penarikan melebihi saldo yang tersedia.',
            'saldo_tersedia' => $saldoTersedia,
        ], 400);
    }

    // Simpan data tarik saldo dengan status 'pending'
    $tarikSaldo = KmdTarikSaldo::create([
        'user_id' => $request->user_id,
        'cabang_id' => $request->cabang_id,
        'donasi_id' => $request->donasi_id,
        'userclasstutorial_id' => $request->userclasstutorial_id,
        'tanggal_penarikan' => $request->tanggal_penarikan,
        'jumlah' => $request->jumlah,
        'status' => 'pending',
        'catatan' => $request->catatan,
        'approved_by' => null,
        'approved_at' => null,
        'rejected_reason' => null,
    ]);
    Log::info('Permintaan tarik dana berhasil disimpan', ['tarikSaldo_id' => $tarikSaldo->id]);

    // **UPDATE KmdDonasi: set semua amount jadi 0 untuk userclasstutorial_id ini**
    KmdDonasi::where('userclasstutorial_id', $request->userclasstutorial_id)
        ->update(['amount' => 0]);
    Log::info('Semua donasi terkait userclasstutorial diupdate amount=0', ['userclasstutorial_id' => $request->userclasstutorial_id]);

    return response()->json([
        'status' => 'success',
        'message' => 'Permintaan tarik dana berhasil dibuat',
        'data' => $tarikSaldo
    ]);
}




}




