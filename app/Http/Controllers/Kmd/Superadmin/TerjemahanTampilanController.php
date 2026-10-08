<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kmd\Language;
use App\Models\Kmd\UiTranslation;
use App\Services\Kmd\TeksTampilanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * kom_v02 — Superadmin: Terjemahan Tampilan (menu, tombol, judul, pesan).
 */
class TerjemahanTampilanController extends Controller
{
    public function __construct(protected TeksTampilanService $teks)
    {
    }

    public function index(Request $request)
    {
        $bahasaList = Language::whereIn('code', $this->teks->bahasaTerjemahan())->orderBy('sort_order')->get();
        $bahasa = $request->query('bahasa', $bahasaList->first()?->code);

        $rows = collect();
        $total = $terisi = 0;

        if ($bahasa) {
            $query = UiTranslation::where('language_code', $bahasa);
            $total = (clone $query)->count();
            $terisi = (clone $query)->whereNotNull('value')->where('value', '!=', '')->count();

            if ($request->filled('cari')) {
                $cari = $request->query('cari');
                $query->where(fn ($q) => $q->where('key', 'like', "%{$cari}%")->orWhere('value', 'like', "%{$cari}%"));
            }
            if ($request->boolean('kosong')) {
                $query->where(fn ($q) => $q->whereNull('value')->orWhere('value', ''));
            }

            $rows = $query->orderBy('key')->paginate(40)->withQueryString();
        }

        return view('a_kom_digital_pusat.superadmin.terjemahan.index', compact('bahasaList', 'bahasa', 'rows', 'total', 'terisi'));
    }

    public function simpan(Request $request)
    {
        $data = $request->validate([
            'bahasa' => ['required', 'exists:languages,code'],
            'values' => ['array'],
            'values.*' => ['nullable', 'string', 'max:2000'],
        ]);

        $jumlah = 0;
        foreach ($data['values'] ?? [] as $id => $value) {
            $jumlah += UiTranslation::where('id', $id)
                ->where('language_code', $data['bahasa'])
                ->update(['value' => $value !== null ? trim($value) : null, 'updated_at' => now()]);
        }

        Cache::forget(UiTranslation::cacheKey($data['bahasa']));

        return back()->with('success', __(':n terjemahan tersimpan.', ['n' => $jumlah]));
    }

    public function pindai()
    {
        $baru = $this->teks->sinkron();

        return back()->with('success', __('Pemindaian selesai: :n teks baru ditambahkan.', ['n' => $baru]));
    }
}
