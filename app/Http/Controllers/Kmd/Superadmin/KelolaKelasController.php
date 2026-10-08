<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdClass;
use App\Models\Kmd\KmdClassTranslation;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KmdUserClass;
use App\Models\Kmd\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * kom_v02 — Superadmin: Kelola Kelas (KOM 100 / 200 / 300 / 400).
 *  - kode URL, urutan katalog, kelas prasyarat, gambar kartu
 *  - nama & deskripsi kelas per bahasa (tampil di katalog peserta)
 */
class KelolaKelasController extends Controller
{
    public function index()
    {
        $classes = KmdClass::with(['translations', 'prerequisite'])->orderBy('sort_order')->orderBy('name')->get();
        $languages = Language::orderBy('sort_order')->get();

        $modulPerBahasa = KmdModuls::select('kom_class_id', 'language_code', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('kom_class_id', 'language_code')->get()
            ->groupBy('kom_class_id');

        $peserta = KmdUserClass::select('class_id', DB::raw('COUNT(*) as jumlah'))->groupBy('class_id')->pluck('jumlah', 'class_id');

        return view('a_kom_digital_pusat.superadmin.kelola_kelas.index', compact('classes', 'languages', 'modulPerBahasa', 'peserta'));
    }

    public function create()
    {
        return $this->form(new KmdClass(['status' => 'active']));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $kelas = DB::connection('mysql')->transaction(function () use ($request, $data) {
            $kelas = KmdClass::create($this->kolomKelas($request, $data));
            $this->simpanTerjemahan($kelas, $data);

            return $kelas;
        });

        return redirect()->route('kom.super_kelola_kelas.index')
            ->with('success', __('Kelas :nama berhasil dibuat. Lanjutkan dengan menambah modul untuk kelas ini.', ['nama' => $kelas->name]));
    }

    public function edit(KmdClass $kelas)
    {
        return $this->form($kelas->load('translations'));
    }

    public function update(Request $request, KmdClass $kelas)
    {
        $data = $this->validasi($request, $kelas);

        DB::connection('mysql')->transaction(function () use ($request, $data, $kelas) {
            $kelas->update($this->kolomKelas($request, $data, $kelas));
            $this->simpanTerjemahan($kelas, $data);
        });

        return redirect()->route('kom.super_kelola_kelas.index')->with('success', __('Data kelas berhasil diperbarui.'));
    }

    // ------------------------------------------------------------------

    protected function form(KmdClass $kelas)
    {
        $languages = Language::orderBy('sort_order')->get();
        $prasyarat = KmdClass::when($kelas->exists, fn ($q) => $q->where('id', '!=', $kelas->id))
            ->orderBy('sort_order')->get();

        return view('a_kom_digital_pusat.superadmin.kelola_kelas.form', compact('kelas', 'languages', 'prasyarat'));
    }

    protected function validasi(Request $request, ?KmdClass $kelas = null): array
    {
        $default = Language::defaultCode();

        return $request->validate([
            'code' => ['nullable', 'string', 'max:50', 'alpha_dash', Rule::unique('kmd_classes', 'code')->ignore($kelas?->id)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'prerequisite_class_id' => ['nullable', 'exists:kmd_classes,id', Rule::notIn([$kelas?->id])],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'translations' => ['required', 'array'],
            "translations.{$default}.name" => ['required', 'string', 'max:255'],
            'translations.*.name' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string', 'max:2000'],
        ], [
            "translations.{$default}.name.required" => __('Nama kelas (Bahasa Indonesia) wajib diisi.'),
            'code.unique' => __('Kode kelas ini sudah dipakai kelas lain.'),
            'code.alpha_dash' => __('Kode kelas hanya boleh huruf, angka, dan tanda strip (mis. kom-200).'),
            'cover_image.image' => __('Gambar kelas harus berupa file gambar.'),
        ]);
    }

    protected function kolomKelas(Request $request, array $data, ?KmdClass $kelas = null): array
    {
        $default = Language::defaultCode();
        $nama = $data['translations'][$default]['name'];

        $kolom = [
            'name' => $nama,
            'code' => $data['code'] ?: ($kelas?->code ?: Str::slug($nama)),
            'sort_order' => $data['sort_order'] ?? (int) preg_replace('/\D/', '', $nama),
            'status' => $data['status'],
            'prerequisite_class_id' => $data['prerequisite_class_id'] ?? null,
        ];

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $nama = 'kelas_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('kelas'), $nama);
            $kolom['cover_image'] = 'kelas/'.$nama;
        } elseif ($request->boolean('hapus_cover')) {
            $kolom['cover_image'] = null;
        }

        return $kolom;
    }

    protected function simpanTerjemahan(KmdClass $kelas, array $data): void
    {
        foreach ($data['translations'] as $code => $t) {
            if (empty($t['name'])) {
                KmdClassTranslation::where('class_id', $kelas->id)->where('language_code', $code)->delete();
                continue;
            }

            KmdClassTranslation::updateOrCreate(
                ['class_id' => $kelas->id, 'language_code' => $code],
                ['name' => $t['name'], 'description' => $t['description'] ?? null]
            );
        }
    }
}
