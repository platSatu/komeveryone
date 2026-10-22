<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdBooks;
use App\Models\Kmd\KmdClass;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KmdUserClass;
use App\Models\Kmd\Language;
use App\Services\Kmd\KelasAktifService;
use App\Services\Kmd\KodeBukuService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * kom_v02 — Katalog kelas: "Pilih Kelas" (KOM 100 / 200 / 300 / 400).
 *
 * Status kartu kelas untuk user:
 *  - berjalan  : sudah terdaftar & belum selesai          → "Lanjutkan Belajar"
 *  - selesai   : sudah lulus                               → "Buka Materi Lagi"
 *  - terkunci  : kelas prasyarat belum selesai             → info kelas yang harus diselesaikan
 *  - segera    : kelas nonaktif / materi bahasa ini belum ada → "Segera Hadir"
 *  - tersedia  : boleh didaftar                            → "Mulai Kelas" (input kode buku)
 */
class KatalogKelasController extends Controller
{
    public function __construct(
        protected KelasAktifService $kelasAktif,
        protected KodeBukuService $kodeBuku,
    ) {
    }

    // ------------------------------------------------------------------
    // Katalog
    // ------------------------------------------------------------------
    public function index()
    {
        $user = Auth::user();
        $language = $user->language_code ?: Language::defaultCode();

        $classes = KmdClass::with('translations')->orderBy('sort_order')->orderBy('name')->get();

        $kartu = $classes->map(function (KmdClass $kelas) use ($user, $language) {
            $data = $this->kartuKelas($kelas, $user, $language);
            // kom_v02: kelas berikutnya (mis. KOM 200) → tanya bahasa lewat pop-up di katalog
            $data['perlu_bahasa'] = $data['status'] === 'tersedia' && $this->perluPilihBahasa($user, $kelas);

            return $data;
        });

        $bahasa = Language::activeList()->firstWhere('code', $language);

        return view('a_kom_digital_pusat.katalog.index', [
            'kartu' => $kartu,
            'bahasa' => $bahasa,
            'bahasaTerkunci' => ! $user->canChangeLanguage(),
        ]);
    }

    // ------------------------------------------------------------------
    // Masuk ke kelas yang sudah didaftar
    // ------------------------------------------------------------------
    public function masuk(string $kode)
    {
        $user = Auth::user();
        $kelas = $this->findKelas($kode);

        $userClass = KmdUserClass::where('user_id', $user->id)->where('class_id', $kelas->id)->first();

        if (! $userClass) {
            return redirect()->route('kom.katalog.kode_buku', $kelas->code);
        }

        $this->kelasAktif->set($userClass);

        return redirect()->route('kom.kelas_lecture.index');
    }

    // ------------------------------------------------------------------
    // Input kode buku untuk membuka kelas
    // ------------------------------------------------------------------
    public function kodeBuku(string $kode)
    {
        $user = Auth::user();
        $kelas = $this->findKelas($kode);
        $kartu = $this->kartuKelas($kelas, $user, $user->language_code ?: Language::defaultCode());

        if (in_array($kartu['status'], ['berjalan', 'selesai'], true)) {
            return redirect()->route('kom.katalog.masuk', $kelas->code);
        }

        if ($kartu['status'] !== 'tersedia') {
            return redirect()->route('kom.katalog.index')->with('error', $kartu['keterangan']);
        }

        if ($this->perluPilihBahasa($user, $kelas)) {
            return redirect()->route('kom.bahasa.index', ['kelas' => $kelas->code]);
        }

        return view('a_kom_digital_pusat.katalog.kode_buku', [
            'kelas' => $kelas,
            'kartu' => $kartu,
            'bahasa' => Language::activeList()->firstWhere('code', $user->language_code),
        ]);
    }

    public function simpanKodeBuku(Request $request, string $kode)
    {
        $user = Auth::user();
        $kelas = $this->findKelas($kode);
        $language = $user->language_code ?: Language::defaultCode();
        $kartu = $this->kartuKelas($kelas, $user, $language);

        if (in_array($kartu['status'], ['berjalan', 'selesai'], true)) {
            return redirect()->route('kom.katalog.masuk', $kelas->code);
        }

        if ($kartu['status'] !== 'tersedia') {
            return redirect()->route('kom.katalog.index')->with('error', $kartu['keterangan']);
        }

        if ($this->perluPilihBahasa($user, $kelas)) {
            return redirect()->route('kom.bahasa.index', ['kelas' => $kelas->code]);
        }

        $request->validate([
            'no_books' => ['required', 'string', 'max:50'],
        ], [
            'no_books.required' => __('Kode buku wajib diisi.'),
        ]);

        $noBuku = trim($request->input('no_books'));
        $hasil = $this->kodeBuku->verify($noBuku);

        if (! $hasil['ok']) {
            return back()->withInput()->withErrors(['no_books' => __($hasil['message'])]);
        }

        $userClass = DB::connection('mysql')->transaction(function () use ($noBuku, $user, $kelas, $language) {
            $book = KmdBooks::firstOrNew(['no_books' => $noBuku]);
            $book->user_id = $user->id;
            $book->class_id = $kelas->id;
            $book->date_publish = Carbon::now();
            $book->status = 'active';
            $book->save();

            return KmdUserClass::firstOrCreate(
                ['user_id' => $user->id, 'class_id' => $kelas->id],
                [
                    'language_code' => $language,
                    'book_id' => $book->id,
                    'status' => KmdUserClass::STATUS_ACTIVE,
                    'enrolled_at' => now(),
                ]
            );
        });

        Log::info('Pendaftaran kelas via kode buku', [
            'user_id' => $user->id, 'class_id' => $kelas->id, 'language_code' => $language, 'no_books' => $noBuku,
        ]);

        $this->kelasAktif->set($userClass);
        session()->forget(BahasaController::SESSION_UNTUK_KELAS);

        return redirect()->route('kom.kelas_lecture.index')
            ->with('success', __('Kode buku berhasil diaktifkan. Selamat belajar!'));
    }

    /**
     * kom_v02: user yang SUDAH pernah lulus kelas wajib memilih bahasa lagi
     * sebelum memulai kelas berikutnya (bahasa dipilih di depan setiap rangkaian).
     */
    protected function perluPilihBahasa($user, KmdClass $kelas): bool
    {
        $pernahLulus = KmdUserClass::where('user_id', $user->id)
            ->where('status', KmdUserClass::STATUS_COMPLETED)
            ->exists();

        return $pernahLulus && session(BahasaController::SESSION_UNTUK_KELAS) !== $kelas->code;
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------
    protected function findKelas(string $kode): KmdClass
    {
        return KmdClass::with('translations')->where('code', $kode)->firstOrFail();
    }

    /**
     * Data satu kartu kelas di katalog untuk user tertentu.
     */
    protected function kartuKelas(KmdClass $kelas, $user, string $language): array
    {
        $userClass = KmdUserClass::where('user_id', $user->id)->where('class_id', $kelas->id)->first();

        $jumlahModul = KmdModuls::where('kom_class_id', $kelas->id)
            ->where('language_code', $userClass->language_code ?? $language)
            ->where('status', 'active')
            ->count();

        $data = [
            'kelas' => $kelas,
            'nama' => $kelas->translatedName($language),
            'deskripsi' => $kelas->translatedDescription($language),
            'jumlah_modul' => $jumlahModul,
            'user_class' => $userClass,
            'status' => 'tersedia',
            'keterangan' => null,
        ];

        if ($userClass) {
            $data['status'] = $userClass->status === KmdUserClass::STATUS_COMPLETED ? 'selesai' : 'berjalan';

            return $data;
        }

        if ($kelas->status !== 'active' || $jumlahModul === 0) {
            $data['status'] = 'segera';
            $data['keterangan'] = __('Kelas ini segera hadir dalam bahasa yang Anda pilih.');

            return $data;
        }

        if ($kelas->prerequisite_class_id) {
            $lulusPrasyarat = KmdUserClass::where('user_id', $user->id)
                ->where('class_id', $kelas->prerequisite_class_id)
                ->where('status', KmdUserClass::STATUS_COMPLETED)
                ->exists();

            if (! $lulusPrasyarat) {
                $prasyarat = KmdClass::with('translations')->find($kelas->prerequisite_class_id);
                $data['status'] = 'terkunci';
                $data['keterangan'] = __('Selesaikan :kelas terlebih dahulu untuk membuka kelas ini.', [
                    'kelas' => $prasyarat?->translatedName($language) ?? '-',
                ]);
            }
        }

        return $data;
    }
}
