<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdBooks;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class ValidasiKodeBukuController extends Controller
{
    public function formValidate()
    {
        return view('a_kom_digital_pusat.dashboard.validasi_kode_buku');
    }

    public function validateKodeBuku(Request $request)
    {
        $kode = trim($request->no_books);
        Log::info('Mulai validasi kode buku', ['no_books' => $kode]);

        $request->validate([
            'no_books' => 'required|string'
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
                return response()->json(['status' => 'error', 'message' => 'Kode buku tidak ditemukan']);
            }

            $dataKode1 = $data1['data'][0]['nomer'] ?? null;
            if (!$dataKode1 || strtolower($dataKode1) !== strtolower($kode)) {
                return response()->json(['status' => 'error', 'message' => 'Kode buku tidak valid']);
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
            $book = KmdBooks::firstOrNew(['no_books' => $kode]);
            if ($book->exists && $book->status === 'active') {
                return response()->json(['status' => 'error', 'message' => 'Kode buku sudah aktif di sistem.']);
            }

            $book->user_id = $user->id;
            $book->class_id = '08a0dbf3-23f7-4b3c-8555-d458be60b632';
            $book->date_publish = Carbon::now();
            $book->status = 'active';
            $book->save();

            Log::info('Kode buku berhasil disimpan', ['no_books' => $kode, 'user_id' => $user->id]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kode buku berhasil diaktifkan dan disimpan. silahkan cek refresh atau cek menu di sebelah kiri.'
            ]);

        } catch (\Exception $e) {
            Log::error('Exception saat validasi kode buku', [
                'no_books' => $kode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan server.']);
        }
    }
}
