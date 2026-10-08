<?php

namespace App\Services\Kmd;

use App\Models\Kmd\Language;
use App\Models\Kmd\UiTranslation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * kom_v02 — Daftar teks tampilan untuk menu "Terjemahan Tampilan".
 *
 * Memindai semua teks __('...') di view & kode, lalu menyiapkan baris kosong
 * (value = NULL) di ui_translations untuk setiap bahasa selain bahasa utama.
 * Teks yang sudah ada TIDAK diubah.
 */
class TeksTampilanService
{
    /** Folder yang dipindai. */
    protected array $folders = ['resources/views', 'app/Http', 'app/Services'];

    /** @return string[] semua teks unik */
    public function pindai(): array
    {
        $teks = [];

        foreach ($this->folders as $folder) {
            $path = base_path($folder);
            if (! File::isDirectory($path)) {
                continue;
            }

            foreach (File::allFiles($path) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }
                $isi = $file->getContents();

                // __('teks') dan __("teks")
                preg_match_all("/__\\(\\s*'((?:[^'\\\\]|\\\\.)+)'/u", $isi, $m1);
                preg_match_all('/__\\(\\s*"((?:[^"\\\\]|\\\\.)+)"/u', $isi, $m2);

                foreach (array_merge($m1[1], $m2[1]) as $t) {
                    $t = stripcslashes($t);
                    // lewati key bergaya file (validation.required, auth.failed, dst.)
                    if (preg_match('/^[a-z_]+\.[a-z_.]+$/', $t)) {
                        continue;
                    }
                    $teks[$t] = true;
                }
            }
        }

        $hasil = array_keys($teks);
        sort($hasil);

        return $hasil;
    }

    /**
     * Pindai lalu tambahkan teks baru ke semua bahasa non-utama.
     *
     * @return int jumlah baris baru
     */
    public function sinkron(): int
    {
        $teks = $this->pindai();
        $baru = 0;

        foreach ($this->bahasaTerjemahan() as $code) {
            $baru += $this->tambahkan($teks, $code);
        }

        return $baru;
    }

    /** Siapkan baris kosong untuk bahasa yang baru ditambahkan. */
    public function siapkanBahasa(string $code): int
    {
        return $this->tambahkan($this->pindai(), $code);
    }

    protected function tambahkan(array $teks, string $code): int
    {
        $ada = UiTranslation::where('language_code', $code)->pluck('key')->flip();
        $now = now();
        $baris = [];

        foreach ($teks as $t) {
            if (! $ada->has($t) && mb_strlen($t) <= 191) {
                $baris[] = [
                    'id' => (string) Str::uuid(),
                    'group' => 'umum',
                    'key' => $t,
                    'language_code' => $code,
                    'value' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // insertOrIgnore: aman bila ada baris yang sudah ada (mis. dipindai bersamaan)
        foreach (array_chunk($baris, 200) as $chunk) {
            DB::table('ui_translations')->insertOrIgnore($chunk);
        }

        Cache::forget(UiTranslation::cacheKey($code));

        return count($baris);
    }

    /** @return string[] kode bahasa selain bahasa utama */
    public function bahasaTerjemahan(): array
    {
        $default = Language::defaultCode();

        return Language::where('code', '!=', $default)->orderBy('sort_order')->pluck('code')->all();
    }
}
