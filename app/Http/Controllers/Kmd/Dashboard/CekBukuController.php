<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdBooks;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CekBukuController extends Controller
{
    public function formValidate()
    {
        return view('a_kom_digital_pusat.dashboard.cekbuku');
    }

    public function validateKodeBuku(Request $request)
    {
        $kode = trim($request->kode_buku);
        Log::info('Mulai validasi kode buku', ['kode_buku' => $kode]);

        $request->validate([
            'kode_buku' => 'required|string'
        ]);

        $user = Auth::guard('kom')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Login terlebih dahulu.']);
        }

        try {
            // STEP 1: Cek API 1 (apakah kode ada)
            $resp1 = Http::withOptions(['verify' => false])->get('https://hmministry.net/api/nobuku_api.php', [
                'no' => $kode,
                'mod' => 'KOM'
            ]);
            Log::info('API 1 response', ['body' => $resp1->body()]);

            if ($resp1->failed()) {
                return response()->json(['status' => 'error', 'message' => 'Tidak bisa mengakses API 1.']);
            }

            $data1 = $resp1->json();

            if (empty($data1['status']) || !$data1['status'] || empty($data1['data'])) {
                return response()->json(['status' => 'error', 'message' => 'Kode buku tidak ditemukan (API 1).']);
            }

            $dataKode1 = $data1['data'][0]['nomer'] ?? null;
            if (!$dataKode1 || strtolower($dataKode1) !== strtolower($kode)) {
                return response()->json(['status' => 'error', 'message' => 'Kode buku tidak valid (API 1).']);
            }

            // STEP 2: Cek API 2 (apakah kode sudah terpakai)
            $resp2 = Http::withOptions(['verify' => false])->get('https://hmministry.net/api/nobuku_offline_api.php', [
                'nobukukom' => $kode
            ]);
            Log::info('API 2 response', ['body' => $resp2->body()]);

            if ($resp2->failed()) {
                return response()->json(['status' => 'error', 'message' => 'Tidak bisa mengakses API 2.']);
            }

            $data2 = $resp2->json();

            if (!empty($data2['status']) && $data2['status'] === true && !empty($data2['data'])) {
                // Data ada berarti sudah dipakai
                return response()->json(['status' => 'error', 'message' => 'Kode buku sudah digunakan sebelumnya.']);
            }

            // STEP 3: Insert/update ke DB (kode belum terpakai)
            $book = KmdBooks::firstOrNew(['kode_buku' => $kode]);
            if ($book->exists && $book->status === 'aktif') {
                return response()->json(['status' => 'error', 'message' => 'Kode buku sudah aktif di sistem.']);
            }

            $book->kom_user_id = $user->id;
            $book->kom_class_id = 1;
            $book->tgl_aktifasi = Carbon::now();
            $book->status = 'aktif';
            $book->save();

            Log::info('Kode buku berhasil disimpan', ['kode_buku' => $kode, 'user_id' => $user->id]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kode buku berhasil diaktifkan dan disimpan. silahkan cek refresh atau cek menu di sebelah kiri.'
            ]);

        } catch (\Exception $e) {
            Log::error('Exception saat validasi kode buku', [
                'kode_buku' => $kode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan server.']);
        }
    }
}
