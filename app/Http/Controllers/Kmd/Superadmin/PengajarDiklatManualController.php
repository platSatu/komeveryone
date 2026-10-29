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
 * - Menandai pengajar MANUAL sudah ikut Diklat Tutor untuk satu kategori.
 * - Mengaktifkan / menonaktifkan pengajar per kategori (hasil diklat maupun manual).
 * Yang tampil di form Buat Kelas Tutorial admin cabang hanya pengajar yang lulus
 * diklat (atau manual) DAN aktif — lihat App\Services\Kmd\PengajarDiklatService.
 * Tidak menyentuh data diklat asli, persembahan, maupun kelas yang sudah dibuat.
 */
class PengajarDiklatManualController extends Controller
{
    public function index(Request $request, PengajarDiklatService $service): View
    {
        $kategoriFilter = in_array($request->query('kategori'), PengajarDiklatService::CATEGORIES, true)
            ? $request->query('kategori') : null;
        $statusFilter = in_array($request->query('status'), ['aktif', 'nonaktif'], true) ? $request->query('status') : null;
        $cari = trim((string) $request->query('cari', ''));

        $tabelSiap = PengajarDiklatService::manualTableExists();
        $statusSiap = PengajarDiklatService::statusColumnsExist();

        // Semua pengajar lulus diklat / manual per kategori (termasuk yang nonaktif).
        $daftar = collect();
        foreach (PengajarDiklatService::CATEGORIES as $kategori) {
            if ($kategoriFilter && $kategoriFilter !== $kategori) {
                continue;
            }

            $dariDiklat = KmdUserClassTutorial::where('category_diklat', $kategori)
                ->where('status', 'selesai')->distinct()->pluck('user_id')->flip();
            $baris = $tabelSiap
                ? KmdPengajarDiklatManual::with('penonaktif:id,name')->where('category_diklat', $kategori)->get()->keyBy('user_id')
                : collect();
            $idManual = $baris->filter(fn ($b) => ! $statusSiap || $b->is_manual)->keys();

            $users = KomUser::where('role', 'pengajar')
                ->whereIn('id', $dariDiklat->keys()->merge($idManual)->unique()->all())
                ->when($cari !== '', fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$cari}%")->orWhere('email', 'like', "%{$cari}%")))
                ->orderBy('name')
                ->get(['id', 'name', 'email']);

            foreach ($users as $u) {
                $b = $baris->get($u->id);
                $aktif = ! ($statusSiap && $b && ! $b->is_active);

                if (($statusFilter === 'aktif' && ! $aktif) || ($statusFilter === 'nonaktif' && $aktif)) {
                    continue;
                }

                $daftar->push((object) [
                    'kategori' => $kategori,
                    'user' => $u,
                    'diklat' => $dariDiklat->has($u->id),
                    'manual' => ($b && (! $statusSiap || $b->is_manual)) ? $b : null,
                    'baris' => $b,
                    'aktif' => $aktif,
                ]);
            }
        }

        // Jumlah yang benar-benar muncul di form Buat Kelas Tutorial (aktif).
        $ringkasan = collect(PengajarDiklatService::CATEGORIES)->mapWithKeys(fn ($k) => [
            $k => $service->qualifiedUserIds($k)->count(),
        ]);

        $semuaPengajar = KomUser::where('role', 'pengajar')->orderBy('name')->get(['id', 'name', 'email']);

        return view('a_kom_digital_pusat.superadmin.pengajar_diklat.index', compact(
            'daftar', 'ringkasan', 'semuaPengajar', 'kategoriFilter', 'statusFilter', 'cari', 'tabelSiap', 'statusSiap'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(PengajarDiklatService::manualTableExists(), 503, 'Tabel belum dibuat. Jalankan php artisan migrate.');

        // 29 Okt 2026: satu pengajar bisa ditandai untuk 1 atau 2 kategori sekaligus.
        $data = $request->validate([
            'user_id' => ['required', 'string', Rule::exists('mysql.kmd_users', 'id')->where('role', 'pengajar')],
            'category_diklat' => ['required', 'array', 'min:1'],
            'category_diklat.*' => ['required', 'distinct', Rule::in(PengajarDiklatService::CATEGORIES)],
            'catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'user_id.exists' => 'User yang dipilih bukan pengajar.',
            'category_diklat.required' => 'Pilih minimal satu kategori diklat.',
        ]);

        $nama = KomUser::whereKey($data['user_id'])->value('name');
        $statusSiap = PengajarDiklatService::statusColumnsExist();
        $ditambah = [];
        $sudahAda = [];
        $masihNonaktif = [];

        foreach ($data['category_diklat'] as $kategori) {
            $ada = KmdPengajarDiklatManual::where('user_id', $data['user_id'])->where('category_diklat', $kategori)->first();

            if ($ada && (! $statusSiap || $ada->is_manual)) {
                $sudahAda[] = $kategori;
                continue;
            }

            if ($ada) {
                // Sebelumnya hanya baris status (pengajar hasil diklat yang dinonaktifkan): jadikan manual.
                $ada->update(['is_manual' => true, 'catatan' => $data['catatan'] ?? $ada->catatan]);
                $baris = $ada;
            } else {
                $baris = KmdPengajarDiklatManual::create([
                    'user_id' => $data['user_id'],
                    'category_diklat' => $kategori,
                    'catatan' => $data['catatan'] ?? null,
                    'created_by' => Auth::id(),
                ]);
            }

            $ditambah[] = $kategori;
            if ($statusSiap && ! $baris->is_active) {
                $masihNonaktif[] = $kategori;
            }
        }

        Log::info('[PengajarDiklatManual] ditambahkan', ['user_id' => $data['user_id'], 'kategori' => $ditambah, 'oleh' => Auth::id()]);

        if (! $ditambah) {
            return back()->withInput()->withErrors(['user_id' => "{$nama} sudah ditandai manual untuk ".implode(' & ', $sudahAda).'.']);
        }

        $pesan = "{$nama} ditandai manual: ".implode(' & ', $ditambah).'.';
        if ($sudahAda) {
            $pesan .= ' ('.implode(' & ', $sudahAda).' sudah ada sebelumnya.)';
        }
        if ($masihNonaktif) {
            $pesan .= ' Status '.implode(' & ', $masihNonaktif).' masih NONAKTIF, aktifkan lewat tombol Edit.';
        }

        return redirect()->route('kom.super_pengajar_diklat.index')->with('success', $pesan);
    }

    /** Edit semua kategori satu pengajar sekaligus (mis. pengajar pindah kategori). */
    public function edit(string $userId): View
    {
        abort_unless(PengajarDiklatService::statusColumnsExist(), 503, 'Kolom status belum dibuat. Jalankan php artisan migrate.');

        $pengajar = KomUser::where('role', 'pengajar')->findOrFail($userId, ['id', 'name', 'email']);
        $kategori = $this->stateKategori($userId);

        return view('a_kom_digital_pusat.superadmin.pengajar_diklat.edit', compact('pengajar', 'kategori'));
    }

    public function update(Request $request, string $userId): RedirectResponse
    {
        abort_unless(PengajarDiklatService::statusColumnsExist(), 503, 'Kolom status belum dibuat. Jalankan php artisan migrate.');

        $pengajar = KomUser::where('role', 'pengajar')->findOrFail($userId, ['id', 'name']);

        $rules = [];
        $pesanError = [];
        foreach (PengajarDiklatService::CATEGORIES as $k) {
            $rules["kategori.$k.manual"] = ['nullable', 'boolean'];
            $rules["kategori.$k.aktif"] = ['required', 'boolean'];
            $rules["kategori.$k.alasan"] = ['nullable', "required_if:kategori.$k.aktif,0", 'string', 'max:255'];
            $rules["kategori.$k.catatan"] = ['nullable', 'string', 'max:255'];
            $pesanError["kategori.$k.alasan.required_if"] = "Alasan nonaktif {$k} wajib diisi.";
        }
        $data = $request->validate($rules, $pesanError);

        $perubahan = [];
        foreach (PengajarDiklatService::CATEGORIES as $k) {
            $input = $data['kategori'][$k];
            $manual = (bool) ($input['manual'] ?? false);
            $aktif = (bool) $input['aktif'];
            $baris = KmdPengajarDiklatManual::where('user_id', $userId)->where('category_diklat', $k)->first();

            // Baris dibutuhkan kalau ditandai manual ATAU dinonaktifkan; selain itu tidak perlu disimpan.
            if (! $manual && $aktif) {
                if ($baris) {
                    $baris->delete();
                    $perubahan[$k] = 'tanpa tanda manual, aktif';
                }
                continue;
            }

            $baris ??= new KmdPengajarDiklatManual(['user_id' => $userId, 'category_diklat' => $k, 'created_by' => Auth::id()]);
            $sebelumAktif = $baris->exists ? (bool) $baris->is_active : true;

            $baris->fill([
                'is_manual' => $manual,
                'is_active' => $aktif,
                'catatan' => $input['catatan'] ?? null,
            ]);

            if ($aktif) {
                $baris->fill(['alasan_nonaktif' => null, 'nonaktif_by' => null, 'nonaktif_at' => null]);
            } else {
                $baris->alasan_nonaktif = $input['alasan'];
                if ($sebelumAktif) {
                    $baris->nonaktif_by = Auth::id();
                    $baris->nonaktif_at = now();
                }
            }

            if ($baris->isDirty() || ! $baris->exists) {
                $baris->save();
                $perubahan[$k] = ($manual ? 'manual, ' : '').($aktif ? 'aktif' : 'nonaktif');
            }
        }

        Log::info('[PengajarDiklatManual] diedit', ['user_id' => $userId, 'perubahan' => $perubahan, 'oleh' => Auth::id()]);

        $muncul = collect(PengajarDiklatService::CATEGORIES)
            ->filter(fn ($k) => app(PengajarDiklatService::class)->qualifiedUserIds($k)->contains($userId))
            ->values();

        $pesan = "Data {$pengajar->name} disimpan. Muncul di form Buat Kelas Tutorial untuk: "
            .($muncul->isNotEmpty() ? $muncul->implode(' & ') : 'tidak ada kategori').'.';

        return redirect()->route('kom.super_pengajar_diklat.index')->with('success', $pesan);
    }

    /** Keadaan tiap kategori untuk satu pengajar (dipakai halaman Edit). */
    private function stateKategori(string $userId): array
    {
        $hasil = [];
        foreach (PengajarDiklatService::CATEGORIES as $k) {
            $baris = KmdPengajarDiklatManual::with('penonaktif:id,name')
                ->where('user_id', $userId)->where('category_diklat', $k)->first();

            $hasil[$k] = (object) [
                'diklat' => KmdUserClassTutorial::where('user_id', $userId)->where('category_diklat', $k)->where('status', 'selesai')->exists(),
                'manual' => (bool) ($baris?->is_manual),
                'aktif' => $baris ? (bool) $baris->is_active : true,
                'catatan' => $baris?->catatan,
                'alasan' => $baris?->alasan_nonaktif,
                'nonaktif_at' => $baris?->nonaktif_at,
                'penonaktif' => $baris?->penonaktif?->name,
            ];
        }

        return $hasil;
    }

    /** Aktifkan / nonaktifkan pengajar untuk satu kategori. */
    public function updateStatus(Request $request): RedirectResponse
    {
        abort_unless(PengajarDiklatService::statusColumnsExist(), 503, 'Kolom status belum dibuat. Jalankan php artisan migrate.');

        $data = $request->validate([
            'user_id' => ['required', 'string', Rule::exists('mysql.kmd_users', 'id')->where('role', 'pengajar')],
            'category_diklat' => ['required', Rule::in(PengajarDiklatService::CATEGORIES)],
            'aktif' => ['required', 'boolean'],
            'alasan' => ['nullable', 'required_if:aktif,0', 'string', 'max:255'],
        ], [
            'alasan.required_if' => 'Alasan menonaktifkan wajib diisi.',
        ]);

        $nama = KomUser::whereKey($data['user_id'])->value('name');
        $baris = KmdPengajarDiklatManual::where('user_id', $data['user_id'])
            ->where('category_diklat', $data['category_diklat'])->first();

        if (! $request->boolean('aktif')) {
            if (! $baris) {
                $baris = new KmdPengajarDiklatManual([
                    'user_id' => $data['user_id'],
                    'category_diklat' => $data['category_diklat'],
                    'is_manual' => false,
                    'created_by' => Auth::id(),
                ]);
            }
            $baris->fill([
                'is_active' => false,
                'alasan_nonaktif' => $data['alasan'],
                'nonaktif_by' => Auth::id(),
                'nonaktif_at' => now(),
            ])->save();

            $pesan = "{$nama} dinonaktifkan untuk {$data['category_diklat']} dan tidak muncul lagi di form Buat Kelas Tutorial.";
        } else {
            if ($baris && ! $baris->is_manual) {
                // Baris hanya untuk status nonaktif pengajar hasil diklat: cukup dihapus.
                $baris->delete();
            } elseif ($baris) {
                $baris->update(['is_active' => true, 'alasan_nonaktif' => null, 'nonaktif_by' => null, 'nonaktif_at' => null]);
            }

            $pesan = "{$nama} diaktifkan kembali untuk {$data['category_diklat']}.";
        }

        Log::info('[PengajarDiklatManual] status diubah', [
            'user_id' => $data['user_id'], 'kategori' => $data['category_diklat'],
            'aktif' => $request->boolean('aktif'), 'alasan' => $data['alasan'] ?? null, 'oleh' => Auth::id(),
        ]);

        return redirect()->back()->with('success', $pesan);
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_unless(PengajarDiklatService::manualTableExists(), 503);

        $baris = KmdPengajarDiklatManual::findOrFail($id);

        if (PengajarDiklatService::statusColumnsExist() && ! $baris->is_active) {
            // Tetap simpan status nonaktifnya, hanya tanda manual yang dicabut.
            $baris->update(['is_manual' => false]);
        } else {
            $baris->delete();
        }

        Log::info('[PengajarDiklatManual] dicabut', ['id' => $id, 'user_id' => $baris->user_id, 'kategori' => $baris->category_diklat, 'oleh' => Auth::id()]);

        return redirect()->back()->with('success', 'Tanda manual dicabut. Kelas tutorial yang sudah dibuat tidak berubah.');
    }
}
