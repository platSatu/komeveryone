<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdBooks;
use App\Models\Kmd\KmdClass;
use App\Models\Kmd\KmdUserClass;
use App\Models\Kmd\Language;
use App\Services\Kmd\KodeBukuService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Halaman input kode buku LAMA (khusus KOM 100).
 *
 * kom_v02: input kode buku sekarang dilakukan per kelas lewat menu "Pilih Kelas"
 * (KatalogKelasController). Halaman lama diarahkan ke sana. Endpoint POST lama tetap
 * berfungsi (untuk berjaga-jaga), tapi kini juga mendaftarkan user ke kelas KOM 100.
 */
class ValidasiKodeBukuController extends Controller
{
    /** ID kelas KOM 100 yang dulu di-hardcode — dipakai hanya sebagai cadangan. */
    private const KOM100_CLASS_ID = '08a0dbf3-23f7-4b3c-8555-d458be60b632';

    public function formValidate()
    {
        return redirect()->route('kom.katalog.index');
    }

    public function validateKodeBuku(Request $request, KodeBukuService $kodeBuku)
    {
        $request->validate(['no_books' => 'required|string']);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => __('Silakan masuk terlebih dahulu.')]);
        }

        $kode = trim($request->no_books);
        $hasil = $kodeBuku->verify($kode);

        if (! $hasil['ok']) {
            return response()->json(['status' => 'error', 'message' => __($hasil['message'])]);
        }

        $kelas = KmdClass::where('code', 'kom-100')->first() ?? KmdClass::find(self::KOM100_CLASS_ID);
        $classId = $kelas?->id ?? self::KOM100_CLASS_ID;

        DB::connection('mysql')->transaction(function () use ($kode, $user, $classId) {
            $book = KmdBooks::firstOrNew(['no_books' => $kode]);
            $book->user_id = $user->id;
            $book->class_id = $classId;
            $book->date_publish = Carbon::now();
            $book->status = 'active';
            $book->save();

            KmdUserClass::firstOrCreate(
                ['user_id' => $user->id, 'class_id' => $classId],
                [
                    'language_code' => $user->language_code ?: Language::defaultCode(),
                    'book_id' => $book->id,
                    'status' => KmdUserClass::STATUS_ACTIVE,
                    'enrolled_at' => now(),
                ]
            );
        });

        Log::info('Kode buku berhasil disimpan (halaman lama)', ['no_books' => $kode, 'user_id' => $user->id]);

        return response()->json([
            'status' => 'success',
            'message' => __('Kode buku berhasil diaktifkan. Silakan buka menu Kelas Saya untuk mulai belajar.'),
        ]);
    }
}
