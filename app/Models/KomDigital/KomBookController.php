<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomBook;
use App\Models\Kom\KomCertificate;
use App\Models\Kom\KomClass;
use App\Models\Kom\KomUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class KomBookController extends Controller
{

    public function formValidate()
    {
        return view('kom_digital.book.validate');
    }

    // public function validateKodeBuku(Request $request)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'kode_buku' => 'required|string',
    //     ]);

    //     // Ambil user yang sedang login
    //     $user = auth('kom')->user();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Anda harus login untuk mengaktifkan kode buku.'
    //         ]);
    //     }

    //     // Cari buku berdasarkan kode
    //     $book = KomBook::where('kode_buku', $request->kode_buku)->first();

    //     // Jika buku tidak ditemukan
    //     if (!$book) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Kode buku tidak ditemukan.'
    //         ]);
    //     }

    //     // Cek apakah buku sudah digunakan
    //     if ($book->status == 'aktif') {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Kode buku sudah digunakan.'
    //         ]);
    //     }

    //     // Jika kom_class_id = 1, beri pesan selamat
    //     if ($book->kom_class_id == 1) {
    //         // Langsung aktifkan buku
    //         $book->update([
    //             'kom_user_id' => $user->id,
    //             'tgl_aktifasi' => Carbon::now(),
    //             'status' => 'aktif',
    //         ]);

    //         return response()->json([
    //             'status' => 'valid',
    //             'message' => 'Selamat, Anda bisa mengikuti kelas ' . $book->komClass->name
    //                 . '. Kode buku telah berhasil diaktifkan oleh ' . $user->username,
    //             'refresh' => true // <- ini penting
    //         ]);
    //     }



    //     // Lanjutkan jika kode_buku sesuai dengan kom_class_id 2, 3, atau 4, minta input sertifikat
    //     if (in_array($book->kom_class_id, [2, 3, 4])) {
    //         // Mengirimkan respons untuk menampilkan form input no sertifikat
    //         return response()->json([
    //             'status' => 'need_certificate',
    //             'message' => 'Kode buku cocok dengan kelas tertentu, masukkan nomor sertifikat.',
    //             'book_id' => $book->id, // Kirim ID buku untuk input sertifikat
    //         ]);
    //     }

    //     // Jika kode_buku sesuai dan statusnya belum digunakan, aktifkan buku
    //     $book->update([
    //         'kom_user_id' => $user->id,
    //         'tgl_aktifasi' => Carbon::now(),
    //         'status' => 'aktif', // Mengubah status menjadi "aktif"
    //     ]);

    //     return response()->json([
    //         'status' => 'valid',
    //         'message' => 'Kode buku berhasil diaktifkan oleh ' . $user->username
    //     ]);
    // }


    // public function validateKodeBuku(Request $request)
    // {
    //     $kode = trim($request->kode_buku);
    //     Log::info('Mulai validasi kode buku', ['kode_buku' => $kode]);

    //     $request->validate([
    //         'kode_buku' => 'required|string'
    //     ]);

    //     $user = Auth::guard('kom')->user();
    //     if (!$user) {
    //         return response()->json(['status' => 'error', 'message' => 'Login terlebih dahulu.']);
    //     }

    //     try {
    //         // STEP 1: Cek API 1 (Apakah kode ada?)
    //         $resp1 = Http::withOptions(['verify' => false])->get('https://hmministry.net/api/nobuku_api.php', [
    //             'no' => $kode,
    //             'mod' => 'KOM'
    //         ]);
    //         Log::info('API 1 response', ['body' => $resp1->body()]);

    //         if ($resp1->failed()) {
    //             return response()->json(['status' => 'error', 'message' => 'Tidak bisa mengakses API 1.']);
    //         }

    //         $data1 = $resp1->json();
    //         $dataKode1 = $data1['data'][0]['nomer'] ?? null;

    //         if (!$dataKode1 || strtolower($dataKode1) !== strtolower($kode)) {
    //             return response()->json(['status' => 'error', 'message' => 'Kode buku tidak ditemukan (API 1).']);
    //         }

    //         // STEP 2: Cek API 2 (Apakah kode sudah dipakai?)
    //         $resp2 = Http::withOptions(['verify' => false])->get('https://hmministry.net/api/nobuku_offline_api.php', [
    //             'nobukukom' => $kode,
    //             'nobukukom100_j' => $kode
    //         ]);
    //         Log::info('API 2 response', ['body' => $resp2->body()]);

    //         if ($resp2->failed()) {
    //             return response()->json(['status' => 'error', 'message' => 'Tidak bisa mengakses API 2.']);
    //         }

    //         $data2 = $resp2->json();

    //         // Jika data ada dan kode cocok, maka kode sudah dipakai
    //         $used = false;
    //         if (!empty($data2['data'])) {
    //             foreach ($data2['data'] as $entry) {
    //                 if (
    //                     isset($entry['nobukukom100']) &&
    //                     strtolower($entry['nobukukom100']) === strtolower($kode)
    //                 ) {
    //                     $used = true;
    //                     break;
    //                 }
    //             }
    //         }

    //         if ($used) {
    //             return response()->json(['status' => 'error', 'message' => 'Kode buku sudah digunakan sebelumnya.']);
    //         }

    //         // STEP 3: Simpan ke database
    //         $book = KomBook::firstOrNew(['kode_buku' => $kode]);
    //         if ($book->exists && $book->status === 'aktif') {
    //             return response()->json(['status' => 'error', 'message' => 'Kode buku sudah aktif di sistem.']);
    //         }

    //         $book->kom_user_id = $user->id;
    //         $book->tgl_aktifasi = Carbon::now();
    //         $book->status = 'aktif';
    //         $book->save();

    //         Log::info('Kode buku berhasil disimpan', ['kode_buku' => $kode, 'user_id' => $user->id]);

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Kode buku berhasil diaktifkan dan disimpan.'
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('Exception saat validasi kode buku', [
    //             'kode_buku' => $kode,
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan server.']);
    //     }
    // }
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
            $book = KomBook::firstOrNew(['kode_buku' => $kode]);
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




    public function index()
    {
        // Mengambil data buku dengan relasi komUser, komClass, dan komCertificate
        $books = KomBook::with(['komUser', 'komClass', 'komCertificate'])->latest()->paginate(10);

        return view('kom_digital.book.index', compact('books'));
    }


    // Tampilkan detail buku tertentu
    public function show($id)
    {
        $book = KomBook::findOrFail($id);
        return view('kom_digital.book.show', compact('book'));
    }

    // Tampilkan form edit buku
    public function edit($id)
    {
        $book = KomBook::findOrFail($id);
        $komUsers = KomUser::all();
        $komClasses = KomClass::all();
        $komCertificates = KomCertificate::all();

        return view('kom_digital.book.edit', compact('book', 'komUsers', 'komClasses', 'komCertificates'));
    }

    // Update data buku
    public function update(Request $request, $id)
    {
        $book = KomBook::findOrFail($id);

        $request->validate([
            'kom_user_id' => 'required|exists:kom_users,id',
            'kom_class_id' => 'required|exists:kom_classes,id',
            'kom_certificate_id' => 'required|exists:kom_certificates,id',
            'kode_buku' => 'required|unique:kom_books,kode_buku,' . $book->id,
            'tgl_aktifasi' => 'required|date',
            'status' => 'required|boolean',
        ]);

        $book->update([
            'kom_user_id' => $request->kom_user_id,
            'kom_class_id' => $request->kom_class_id,
            'kom_certificate_id' => $request->kom_certificate_id,
            'kode_buku' => $request->kode_buku,
            'tgl_aktifasi' => $request->tgl_aktifasi,
            'status' => $request->status == 'terpakai', // Convert to boolean
        ]);

        return redirect()->route('kom.kode_buku.index')->with('success', 'Buku berhasil diperbarui.');
    }

    // Hapus buku
    public function destroy($id)
    {
        $book = KomBook::findOrFail($id);
        $book->delete();

        return redirect()->route('kom.kode_buku.index')->with('success', 'Buku berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
    {
        // Validasi input untuk memastikan status yang diterima adalah salah satu dari opsi yang ada
        $request->validate([
            'status' => 'required|in:aktif,belum_aktif',
        ]);

        // Cari buku berdasarkan ID
        $book = KomBook::findOrFail($id);

        // Logika untuk toggle status
        if ($book->status == 'belum_aktif') {
            $book->status = 'aktif';  // Ubah status menjadi aktif
        } else {
            $book->status = 'belum_aktif';  // Ubah status menjadi belum aktif
        }

        // Simpan perubahan status
        $book->save();

        // Redirect kembali ke halaman daftar buku dengan pesan sukses
        return redirect()->route('kom.kode_buku.index') // Sesuaikan dengan route yang sesuai
            ->with('success', 'Status buku berhasil diperbarui ke ' . $book->status);
    }




}
