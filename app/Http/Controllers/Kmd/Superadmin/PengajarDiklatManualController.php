<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdPengajarDiklatManual;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KomUser;
use App\Services\Kmd\PengajarDiklatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Superadmin › Diklat Tutor › Pengajar Diklat (29 Okt 2026).
 *
 * Menandai pengajar SECARA MANUAL sudah ikut Diklat Tutor untuk satu kategori,
 * supaya namanya muncul di form Buat Kelas Tutorial admin cabang (lihat
 * App\Services\Kmd\PengajarDiklatService). Tidak menyentuh data diklat asli,
 * persembahan, maupun kelas yang sudah ada; tanda manual bisa dicabut kapan saja.
 */
class PengajarDiklatManualController extends Controller
{
    public function index(Request $request, PengajarDiklatService $service): View
    {
        $kategoriFilter = in_array($request->query('kategori'), PengajarDiklatService::CATEGORIES, true)
            ? $request->query('kategori') : null;
        $cari = trim((string) $request->query('cari', ''));

        $tabelSiap = PengajarDiklatService::manualTableExists();

        // Semua pengajar yang lulus per kategori, dengan sumbernya (Diklat / Manual).
        $daftar = collect();
        foreach (PengajarDiklatService::CATEGORIES as $kategori) {
            if ($kategoriFilter && $kategoriFilter !== $kategori) {
                continue;
            }

            $dariDiklat = KmdUserClassTutorial::where('category_diklat', $kategori)
                ->where('status', 'selesai')->distinct()->pluck('user_id')->flip();
            $manual = $tabelSiap
                ? KmdPengajarDiklatManual::where('category_diklat', $kategori)->get()->keyBy('user_id')
                : collect();

            $users = KomUser::where('role', 'pengajar')
                ->whereIn('id', $dariDiklat->keys()->merge($manual->keys())->unique()->all())
                ->when($cari !== '', fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$cari}%")->orWhere('email', 'like', "%{$cari}%")))
                ->orderBy('name')
                ->get(['id', 'name', 'email']);

            foreach ($users as $u) {
                $daftar->push((object) [
                    'kategori' => $kategori,
                    'user' => $u,
                    'diklat' => $dariDiklat->has($u->id),
                    'manual' => $manual->get($u->id),
                ]);
            }
        }

        $ringkasan = collect(PengajarDiklatService::CATEGORIES)->mapWithKeys(fn ($k) => [
            $k => $service->qualifiedUserIds($k)->count(),
        ]);

        $semuaPengajar = KomUser::where('role', 'pengajar')->orderBy('name')->get(['id', 'name', 'email']);

        return view('a_kom_digital_pusat.superadmin.pengajar_diklat.index', compact(
            'daftar', 'ringkasan', 'semuaPengajar', 'kategoriFilter', 'cari', 'tabelSiap'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(PengajarDiklatService::manualTableExists(), 503, 'Tabel belum dibuat. Jalankan php artisan migrate.');

        $data = $request->validate([
            'user_id' => ['required', 'string', Rule::exists('mysql.kmd_users', 'id')->where('role', 'pengajar')],
            'category_diklat' => ['required', Rule::in(PengajarDiklatService::CATEGORIES)],
            'catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'user_id.exists' => 'User yang dipilih bukan pengajar.',
        ]);

        $sudah = KmdPengajarDiklatManual::where('user_id', $data['user_id'])
            ->where('category_diklat', $data['category_diklat'])->exists();
        if ($sudah) {
            return back()->withInput()->withErrors(['user_id' => 'Pengajar ini sudah ditandai manual untuk kategori '.$data['category_diklat'].'.']);
        }

        $baris = KmdPengajarDiklatManual::create($data + ['created_by' => Auth::id()]);
        $nama = KomUser::whereKey($data['user_id'])->value('name');

        Log::info('[PengajarDiklatManual] ditambahkan', ['id' => $baris->id, 'user_id' => $data['user_id'], 'kategori' => $data['category_diklat'], 'oleh' => Auth::id()]);

        return redirect()->route('kom.super_pengajar_diklat.index')
            ->with('success', "{$nama} sekarang muncul sebagai pengajar {$data['category_diklat']} di form Buat Kelas Tutorial.");
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_unless(PengajarDiklatService::manualTableExists(), 503);

        $baris = KmdPengajarDiklatManual::findOrFail($id);
        $baris->delete();

        Log::info('[PengajarDiklatManual] dicabut', ['id' => $id, 'user_id' => $baris->user_id, 'kategori' => $baris->category_diklat, 'oleh' => Auth::id()]);

        return redirect()->route('kom.super_pengajar_diklat.index')
            ->with('success', 'Tanda manual dicabut. Kelas tutorial yang sudah dibuat tidak berubah.');
    }
}
