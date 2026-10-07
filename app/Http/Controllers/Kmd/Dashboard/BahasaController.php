<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdUserClass;
use App\Models\Kmd\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * kom_v02 — Halaman "Pilih Bahasa Belajar".
 *
 * Aturan:
 *  - User baru wajib memilih bahasa sebelum melihat katalog kelas.
 *  - Bahasa TERKUNCI selama masih ada kelas yang sedang berjalan.
 *  - Setelah kelas selesai, user boleh memilih bahasa lain untuk kelas berikutnya.
 */
class BahasaController extends Controller
{
    /** Session: bahasa sudah dikonfirmasi untuk kelas (kode) ini. */
    public const SESSION_UNTUK_KELAS = 'kom_bahasa_untuk_kelas';

    public function index(Request $request)
    {
        $user = Auth::user();

        // kom_v02: dipanggil dari katalog sebelum memulai kelas berikutnya (?kelas=kom-200)
        $untukKelas = $request->filled('kelas')
            ? \App\Models\Kmd\KmdClass::with('translations')->where('code', $request->query('kelas'))->first()
            : null;
        $languages = Language::activeList();

        $kelasBerjalan = KmdUserClass::with('kelas.translations')
            ->where('user_id', $user->id)
            ->where('status', KmdUserClass::STATUS_ACTIVE)
            ->latest('enrolled_at')
            ->first();

        return view('a_kom_digital_pusat.bahasa.pilih', [
            'languages' => $languages,
            // Jika ada kelas berjalan, bahasa yang berlaku = bahasa kelas tersebut
            'current' => $kelasBerjalan->language_code ?? $user->language_code,
            'isLocked' => (bool) $kelasBerjalan,
            'kelasBerjalan' => $kelasBerjalan,
            'isFirstTime' => empty($user->language_code),
            'untukKelas' => $untukKelas,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $activeCodes = Language::activeList()->pluck('code')->all();

        $request->validate([
            'language_code' => ['required', 'string', 'in:'.implode(',', $activeCodes)],
        ], [
            'language_code.required' => __('Silakan pilih salah satu bahasa.'),
            'language_code.in' => __('Bahasa yang dipilih belum tersedia.'),
        ]);

        $code = $request->input('language_code');

        // Terkunci: selama ada kelas berjalan, bahasa WAJIB sama dengan bahasa kelas tersebut
        $kelasBerjalan = KmdUserClass::where('user_id', $user->id)
            ->where('status', KmdUserClass::STATUS_ACTIVE)
            ->latest('enrolled_at')
            ->first();

        if ($kelasBerjalan && $kelasBerjalan->language_code !== $code) {
            return back()->with('error', __('Bahasa belajar Anda masih terkunci karena ada kelas yang sedang berjalan. Selesaikan kelas tersebut terlebih dahulu.'));
        }

        $pertamaKali = empty($user->language_code);

        $user->language_code = $code;
        $user->save();

        Log::info('Bahasa belajar dipilih', ['user_id' => $user->id, 'language_code' => $code]);

        app()->setLocale($code);

        // Memilih bahasa untuk memulai kelas berikutnya → lanjut ke kode buku kelas tsb
        if ($request->filled('kelas')) {
            session([self::SESSION_UNTUK_KELAS => $request->input('kelas')]);

            return redirect()->route('kom.katalog.kode_buku', $request->input('kelas'));
        }

        // User baru → lanjut ke Pilih Kelas. Ganti bahasa dari menu atas → kembali ke halaman semula.
        if ($pertamaKali) {
            return redirect()->route('kom.katalog.index')
                ->with('success', __('Bahasa belajar berhasil disimpan. Silakan pilih kelas yang ingin Anda ikuti.'));
        }

        return back()->with('success', __('Bahasa belajar berhasil diganti.'));
    }
}
