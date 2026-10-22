<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdClass;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\Language;
use App\Services\Kmd\BahasaKelolaService;
use App\Services\Kmd\KontenBahasaService;
use App\Services\Kmd\TeksTampilanService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * kom_v02 — Superadmin: Kelola Bahasa.
 *  - daftar bahasa + kelengkapan konten per kelas
 *  - tambah / ubah bahasa, aktifkan / nonaktifkan
 *  - salin konten kelas dari satu bahasa ke bahasa lain
 *  - ganti "bahasa yang sedang dikelola" (dropdown di halaman konten)
 */
class BahasaController extends Controller
{
    public function __construct(protected KontenBahasaService $konten)
    {
    }

    public function index()
    {
        $languages = Language::orderBy('sort_order')->get();
        $classes = KmdClass::with('translations')->orderBy('sort_order')->get();

        $ringkasan = [];
        $hapus = []; // kom_v02: ringkasan untuk tombol Hapus Konten / Hapus Bahasa
        $penghapus = app(\App\Services\Kmd\HapusKontenBahasaService::class);
        foreach ($languages as $lang) {
            foreach ($classes as $kelas) {
                $ringkasan[$lang->code][$kelas->id] = $this->konten->kelengkapan($kelas, $lang->code);
                if (! $lang->is_default && $ringkasan[$lang->code][$kelas->id]['modul'] > 0) {
                    $hapus[$lang->code]['kelas'][$kelas->id] = $penghapus->ringkasan($lang->code, $kelas);
                }
            }
            if (! $lang->is_default) {
                $hapus[$lang->code]['semua'] = $penghapus->ringkasan($lang->code);
            }
        }

        return view('a_kom_digital_pusat.superadmin.bahasa.index', compact('languages', 'classes', 'ringkasan', 'hapus'));
    }

    public function create()
    {
        return view('a_kom_digital_pusat.superadmin.bahasa.form', ['language' => new Language(['sort_order' => Language::max('sort_order') + 1])]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['is_active'] = false; // bahasa baru selalu mulai NONAKTIF
        $data['is_default'] = false;

        $language = Language::create($data);

        // siapkan baris kosong "Terjemahan Tampilan" untuk bahasa baru
        app(TeksTampilanService::class)->siapkanBahasa($language->code);

        return redirect()->route('kom.super_bahasa.index')
            ->with('success', __('Bahasa :nama berhasil ditambahkan. Langkah berikutnya: salin konten kelas, terjemahkan, lalu aktifkan.', ['nama' => $language->name]));
    }

    public function edit(Language $language)
    {
        return view('a_kom_digital_pusat.superadmin.bahasa.form', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $data = $this->validasi($request, $language);
        unset($data['code']); // kode bahasa tidak boleh diubah (dipakai di seluruh data)

        $language->update($data);

        return redirect()->route('kom.super_bahasa.index')->with('success', __('Data bahasa berhasil diperbarui.'));
    }

    public function toggle(Language $language)
    {
        if ($language->is_default && $language->is_active) {
            return back()->with('error', __('Bahasa utama tidak bisa dinonaktifkan.'));
        }

        $language->update(['is_active' => ! $language->is_active]);

        return back()->with('success', $language->is_active
            ? __('Bahasa :nama sekarang AKTIF dan bisa dipilih peserta.', ['nama' => $language->name])
            : __('Bahasa :nama dinonaktifkan. Peserta baru tidak bisa memilihnya lagi.', ['nama' => $language->name]));
    }

    public function salin(Request $request)
    {
        $kodeBahasa = Language::pluck('code')->all();

        $data = $request->validate([
            'class_id' => ['required', 'exists:kmd_classes,id'],
            'dari' => ['required', Rule::in($kodeBahasa)],
            'ke' => ['required', Rule::in($kodeBahasa), 'different:dari'],
        ], [
            'ke.different' => __('Bahasa tujuan harus berbeda dengan bahasa asal.'),
        ]);

        $kelas = KmdClass::findOrFail($data['class_id']);

        if (! KmdModuls::where('kom_class_id', $kelas->id)->where('language_code', $data['dari'])->exists()) {
            return back()->with('error', __('Kelas ini belum punya modul dalam bahasa asal yang dipilih.'));
        }

        $hasil = $this->konten->salinKelas($kelas, $data['dari'], $data['ke']);

        BahasaKelolaService::set($data['ke']); // langsung kelola bahasa tujuan

        return redirect()->route('kom.super_kom-modul.index')->with('success', __(
            'Berhasil disalin: :modul modul, :video video, :soal pertanyaan, :jawaban jawaban (:dilewati modul dilewati karena sudah pernah disalin). Sekarang Anda sedang mengelola bahasa tujuan — silakan terjemahkan teks dan ganti file videonya.',
            $hasil
        ));
    }

    /**
     * kom_v02: hapus konten salinan satu kelas (atau semua kelas) untuk sebuah bahasa.
     */
    public function hapusKonten(Request $request, Language $language, \App\Services\Kmd\HapusKontenBahasaService $penghapus)
    {
        $data = $request->validate([
            'class_id' => ['nullable', 'exists:kmd_classes,id'],
            'konfirmasi' => ['required', 'string'],
            'termasuk_peserta' => ['nullable', 'boolean'],
        ]);

        if (strtolower(trim($data['konfirmasi'])) !== strtolower($language->code)) {
            return back()->with('error', __('Konfirmasi tidak cocok. Ketik kode bahasa :kode untuk menghapus.', ['kode' => strtoupper($language->code)]));
        }

        $kelas = ! empty($data['class_id']) ? KmdClass::find($data['class_id']) : null;

        try {
            $hasil = $penghapus->hapusKonten($language->code, $kelas, $request->boolean('termasuk_peserta'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', __(
            'Konten :bahasa dihapus: :modul modul, :video video, :soal pertanyaan, :jawaban jawaban, :file file video. Konten Bahasa Indonesia tidak tersentuh.',
            $hasil + ['bahasa' => $language->name]
        ));
    }

    /**
     * kom_v02: hapus bahasa sampai bersih (konten semua kelas, nama kelas, terjemahan tampilan, data bahasa).
     */
    public function destroy(Request $request, Language $language, \App\Services\Kmd\HapusKontenBahasaService $penghapus)
    {
        $data = $request->validate([
            'konfirmasi' => ['required', 'string'],
            'termasuk_peserta' => ['nullable', 'boolean'],
        ]);

        if (strtolower(trim($data['konfirmasi'])) !== strtolower($language->code)) {
            return back()->with('error', __('Konfirmasi tidak cocok. Ketik kode bahasa :kode untuk menghapus.', ['kode' => strtoupper($language->code)]));
        }

        $nama = $language->name;

        try {
            $hasil = $penghapus->hapusBahasa($language, $request->boolean('termasuk_peserta'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('kom.super_bahasa.index')->with('success', __(
            'Bahasa :bahasa sudah dihapus bersih: :modul modul, :video video, :soal pertanyaan, :jawaban jawaban, :file file video, :nama_kelas nama kelas, :teks_tampilan teks tampilan.',
            $hasil + ['bahasa' => $nama]
        ));
    }

    /** Ganti "bahasa yang sedang dikelola" di halaman konten superadmin. */
    public function kelola(Request $request)
    {
        $request->validate(['language_code' => ['required', 'exists:languages,code']]);
        BahasaKelolaService::set($request->input('language_code'));

        if ($request->input('lanjut') === 'modul') {
            return redirect()->route('kom.super_kom-modul.index');
        }

        return back();
    }

    protected function validasi(Request $request, ?Language $language = null): array
    {
        return $request->validate([
            'code' => [$language ? 'nullable' : 'required', 'string', 'max:10', 'alpha_dash',
                Rule::unique('languages', 'code')->ignore($language?->id)],
            'name' => ['required', 'string', 'max:100'],
            'native_name' => ['nullable', 'string', 'max:100'],
            'certificate_prefix' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [
            'code.required' => __('Kode bahasa wajib diisi, mis. en atau zh.'),
            'code.unique' => __('Kode bahasa ini sudah dipakai.'),
            'name.required' => __('Nama bahasa wajib diisi.'),
        ]);
    }
}
