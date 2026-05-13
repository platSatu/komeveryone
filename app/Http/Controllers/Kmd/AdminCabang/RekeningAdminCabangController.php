<?php

namespace App\Http\Controllers\Kmd\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Kmd\KmdUserRekening;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Rules\CustomDbRule;

class RekeningAdminCabangController extends Controller
{
    /**
     * Tampilkan halaman rekening user
     */
    public function index(): View
    {
        try {
            $user = Auth::user();
            Log::info('Accessing rekening index', ['user_id' => $user->id]);
            
            // Cek apakah user sudah punya rekening
            $userRekening = KmdUserRekening::where('user_id', $user->id)->first();
            Log::info('User rekening check', [
                'user_id' => $user->id,
                'has_rekening' => $userRekening ? true : false
            ]);
            
            // Get list bank untuk dropdown
            $listBank = $this->getListBank();

            return view('a_kom_digital_pusat.admin_cabang.rekening.index', compact(
                'userRekening',
                'listBank'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in rekening index', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('a_kom_digital_pusat.admin_cabang.rekening.index', [
                'userRekening' => null,
                'listBank' => $this->getListBank(),
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Simpan data rekening user (hanya bisa 1x)
     */
    public function store(Request $request)
{
    // Log awal request
    Log::info('=== START STORE REKENING ===');
    Log::info('Request received', [
        'method' => $request->method(),
        'url' => $request->url(),
        'user_id' => Auth::id(),
        'all_data' => $request->all(),
        'headers' => $request->headers->all()
    ]);

    try {
        // Step 1: Validasi (tetap seperti kode Anda, dengan CustomDbRule ke 'mysql2')
        Log::info('Step 1: Starting validation');
        
        $id = $request->route('id') ?? $request->input('id') ?? null; // Null untuk create
        
        $request->validate([
            'nama_rek' => 'required|string|max:100',
            'no_rek' => [
                'required',
                'string',
                'max:50',
                'regex:/^[0-9]+$/',
                // CustomDbRule: Unique di 'mysql2', tabel 'kmd_user_rekening'
                new CustomDbRule('mysql2', 'kmd_user_rekening', 'no_rek', 'unique', $id),
            ],
            'nama_bank' => 'required|string|max:100',
            'cabang_bank' => 'required|string|max:100',
            // Tambahan validasi dari log error (user_id dan cabang_id)
            'user_id' => 'nullable|uuid', // Opsional, ambil dari auth jika kosong
            'cabang_id' => 'nullable|uuid', // Opsional, ambil dari user
        ], [
            'nama_rek.required' => 'Nama pemilik rekening harus diisi',
            'nama_rek.max' => 'Nama pemilik rekening maksimal 100 karakter',
            'no_rek.required' => 'Nomor rekening harus diisi',
            'no_rek.regex' => 'Nomor rekening hanya boleh berisi angka',
            'no_rek.max' => 'Nomor rekening maksimal 50 karakter',
            'no_rek.unique' => 'Nomor rekening sudah terdaftar di database.',
            'nama_bank.required' => 'Nama bank harus diisi',
            'nama_bank.max' => 'Nama bank maksimal 100 karakter',
            'cabang_bank.required' => 'Cabang bank harus diisi',
            'cabang_bank.max' => 'Cabang bank maksimal 100 karakter',
            'user_id.uuid' => 'User  ID harus format UUID yang valid.',
            'cabang_id.uuid' => 'Cabang ID harus format UUID yang valid.',
        ]);

        Log::info('Step 1: Validation passed');

        // Step 2: Get user (dari default connection, asumsi User di 'mysql')
        Log::info('Step 2: Getting user data');
        $user = Auth::user();
        
        if (!$user) {
            Log::error('Step 2: User not authenticated');
            return response()->json([
                'success' => false,
                'message' => 'User  tidak terautentikasi'
            ], 401);
        }

        // Ambil user_id dan cabang_id dari auth/request (prioritas: request > user)
        $userId = $request->input('user_id', $user->id);
        $cabangId = $request->input('cabang_id', $user->cabang_id ?? null);

        Log::info('Step 2: User data retrieved', [
            'user_id' => $userId,
            'user_name' => $user->name,
            'cabang_id' => $cabangId
        ]);
        
        // Step 3: Cek existing rekening (sekarang ke 'mysql2' via model atau DB connection)
        Log::info('Step 3: Checking existing rekening di mysql2');
        $existingRekening = KmdUserRekening::where('user_id', $userId)->first(); // Model pakai 'mysql2'
        // Atau fallback: DB::connection('mysql2')->table('kmd_user_rekening')->where('user_id', $userId)->first();
        
        if ($existingRekening) {
            Log::warning('Step 3: User already has rekening', [
                'user_id' => $userId,
                'existing_rekening_id' => $existingRekening->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki data rekening. Setiap user hanya dapat menginput rekening sekali.'
            ], 400);
        }

        Log::info('Step 3: No existing rekening found di mysql2');

        // Step 4: Begin transaction (sekarang khusus 'mysql2')
        Log::info('Step 4: Starting database transaction di mysql2');
        DB::connection('mysql2')->beginTransaction();
        
        // Step 5: Prepare data
        Log::info('Step 5: Preparing data for insert');
        $dataToInsert = [
            'id' => (string) Str::uuid(), // Cast ke string untuk UUID
            'user_id' => $userId,
            'cabang_id' => $cabangId,
            'nama_rek' => $request->nama_rek,
            'no_rek' => $request->no_rek,
            'nama_bank' => $request->nama_bank,
            'cabang_bank' => $request->cabang_bank,
            'created_at' => now(),
            'updated_at' => now()
        ];

        Log::info('Step 5: Data prepared', $dataToInsert);

        // Step 6: Insert data (sekarang ke 'mysql2' via model atau DB connection)
        Log::info('Step 6: Inserting data to mysql2');
        
        // Opsi 1: Pakai model (rekomendasi, jika model punya $connection = 'mysql2')
        $rekening = KmdUserRekening::create($dataToInsert);
        $inserted = $rekening ? true : false;
        
        // Opsi 2: Fallback pakai DB::connection jika model belum diubah
        // $inserted = DB::connection('mysql2')->table('kmd_user_rekening')->insert($dataToInsert);
        
        Log::info('Step 6: Insert result', ['inserted' => $inserted, 'rekening_id' => $rekening->id ?? 'null']);

        if (!$inserted) {
            throw new Exception('Failed to insert data to mysql2 database');
        }

        // Step 7: Commit transaction (mysql2)
        Log::info('Step 7: Committing transaction di mysql2');
        DB::connection('mysql2')->commit();

        // Step 8: Get inserted data (dari 'mysql2')
        Log::info('Step 8: Retrieving inserted data dari mysql2');
        $rekening = KmdUserRekening::where('user_id', $userId)->first(); // Model pakai 'mysql2'
        // Atau fallback: DB::connection('mysql2')->table('kmd_user_rekening')->where('user_id', $userId)->first();

        Log::info('Step 8: Data retrieved', [
            'rekening_id' => $rekening ? $rekening->id : 'null',
            'rekening_data' => $rekening ? $rekening->toArray() : 'null'
        ]);

        Log::info('=== SUCCESS STORE REKENING ===');

        return response()->json([
            'success' => true,
            'message' => 'Data rekening berhasil disimpan.',
            'data' => $rekening,
            'debug_info' => [
                'user_id' => $userId,
                'inserted_id' => $dataToInsert['id'],
                'connection_used' => 'mysql2',
                'timestamp' => now()
            ]
        ]);

    } catch (ValidationException $e) {
        // Rollback jika validasi gagal (walaupun jarang butuh untuk validasi)
        if (DB::connection('mysql2')->transactionLevel() > 0) {
            DB::connection('mysql2')->rollBack();
        }
        
        Log::error('Validation error in store rekening', [
            'user_id' => Auth::id(),
            'errors' => $e->errors(),
            'validator_messages' => $e->validator->messages()->toArray()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Data tidak valid.',
            'errors' => $e->errors(),
            'debug_info' => [
                'error_type' => 'validation',
                'timestamp' => now()
            ]
        ], 422);
        
    } catch (Exception $e) {
        // Rollback transaction
        if (DB::connection('mysql2')->transactionLevel() > 0) {
            DB::connection('mysql2')->rollBack();
        }
        
        Log::error('=== ERROR STORE REKENING ===', [
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'connection_used' => 'mysql2' // Tambah untuk debug
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            'debug_info' => [
                'error_type' => 'exception',
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'connection_used' => 'mysql2',
                'timestamp' => now()
            ]
        ], 500);
    }
}


    /**
     * Get list bank populer di Indonesia
     */
    private function getListBank()
    {
        return [
            'BCA' => 'Bank Central Asia (BCA)',
            'BRI' => 'Bank Rakyat Indonesia (BRI)',
            'BNI' => 'Bank Negara Indonesia (BNI)',
            'Mandiri' => 'Bank Mandiri',
            'CIMB' => 'CIMB Niaga',
            'Danamon' => 'Bank Danamon',
            'Permata' => 'Bank Permata',
            'OCBC' => 'OCBC NISP',
            'Maybank' => 'Maybank Indonesia',
            'BTPN' => 'Bank BTPN',
            'Jenius' => 'Jenius (BTPN)',
            'BSI' => 'Bank Syariah Indonesia (BSI)',
            'Muamalat' => 'Bank Muamalat',
            'BCA Syariah' => 'BCA Syariah',
            'BRI Syariah' => 'BRI Syariah',
            'BNI Syariah' => 'BNI Syariah',
            'Mandiri Syariah' => 'Mandiri Syariah',
            'Mega' => 'Bank Mega',
            'Bukopin' => 'Bank Bukopin',
            'HSBC' => 'HSBC Indonesia',
            'Citibank' => 'Citibank',
            'Standard Chartered' => 'Standard Chartered',
            'UOB' => 'United Overseas Bank (UOB)',
            'Panin' => 'Bank Panin',
            'Sinarmas' => 'Bank Sinarmas',
            'Lainnya' => 'Bank Lainnya'
        ];
    }
}
