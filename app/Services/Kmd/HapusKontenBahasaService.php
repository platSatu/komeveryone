<?php

namespace App\Services\Kmd;

use App\Models\Kmd\KmdClass;
use App\Models\Kmd\Language;
use App\Models\Kmd\UiTranslation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * kom_v02 — Menghapus konten salinan sebuah bahasa sampai bersih.
 *
 *  - hapusKonten(bahasa, kelas?) : modul → video → relasi soal-video → soal → jawaban
 *                                  (+ file video yang hanya dipakai salinan ini)
 *  - hapusBahasa(bahasa)         : semua konten di atas untuk SEMUA kelas
 *                                  + nama/deskripsi kelas bahasa tsb
 *                                  + Terjemahan Tampilan bahasa tsb
 *                                  + data bahasanya sendiri
 *
 * Pengaman:
 *  - Bahasa utama (Indonesia) TIDAK PERNAH bisa dihapus.
 *  - DITOLAK jika ada data yang tidak boleh hilang: sertifikat, persembahan (donasi),
 *    jadwal kelas tutorial (punya meeting Zoom) di bahasa tsb.
 *  - Jika ada data belajar peserta (progress video, jawaban, pendaftaran kelas, dll),
 *    superadmin harus mencentang "hapus juga data peserta" — data itu ikut dihapus
 *    dan peserta akan memilih bahasa lagi.
 */
class HapusKontenBahasaService
{
    protected const KONEKSI = 'mysql';

    // ------------------------------------------------------------------
    // Ringkasan: apa saja yang akan terhapus & apa yang menghalangi
    // ------------------------------------------------------------------

    /**
     * @return array{
     *   konten: array{modul:int, video:int, soal:int, jawaban:int},
     *   peserta: array<string,int>, penghalang: array<string,int>,
     *   ada_peserta: bool, boleh: bool
     * }
     */
    public function ringkasan(string $bahasa, ?KmdClass $kelas = null): array
    {
        $db = DB::connection(self::KONEKSI);
        [$modulIds, $videoIds, $soalIds] = $this->idKonten($bahasa, $kelas);

        $konten = [
            'modul' => $modulIds->count(),
            'video' => $videoIds->count(),
            'soal' => $soalIds->count(),
            'jawaban' => $soalIds->isEmpty() ? 0 : $db->table('kmd_answereds')->whereIn('questions_id', $soalIds)->count(),
        ];

        // Data yang TIDAK boleh dihapus otomatis
        $penghalang = [
            __('Sertifikat') => $db->table('kmd_certificates')->where('language_code', $bahasa)
                ->when($kelas, fn ($q) => $q->where('class_id', $kelas->id))->count(),
            __('Jadwal kelas tutorial') => $this->hitung('kmd_class_tutorial', 'modul_id', $modulIds),
            __('Persembahan') => $this->hitung('kmd_donations', 'modul_id', $modulIds),
        ];

        // Data belajar peserta (boleh ikut dihapus jika superadmin setuju)
        $peserta = [
            __('Progress video') => $this->hitung('kmd_user_progress_video', 'video_id', $videoIds),
            __('Jawaban pertanyaan video') => $this->hitung('kmd_user_questions', 'question_id', $soalIds),
            __('Progress modul') => $this->hitung('kmd_progress_moduls', 'moduls_id', $modulIds)
                + $this->hitung('kmd_user_module', 'module_id', $modulIds)
                + $this->hitung('kmd_user_tutorial_status', 'module_id', $modulIds),
            __('Pendaftaran kelas') => $this->tabelAda('kmd_user_classes')
                ? $db->table('kmd_user_classes')->where('language_code', $bahasa)
                    ->when($kelas, fn ($q) => $q->where('class_id', $kelas->id))->count()
                : 0,
        ];

        if (! $kelas) {
            $peserta[__('Peserta yang memilih bahasa ini')] = $db->table('kmd_users')->where('language_code', $bahasa)->count();
        }

        $penghalang = array_filter($penghalang);
        $peserta = array_filter($peserta);

        return [
            'konten' => $konten,
            'peserta' => $peserta,
            'penghalang' => $penghalang,
            'ada_peserta' => ! empty($peserta),
            'boleh' => empty($penghalang) && ! $this->bahasaUtama($bahasa),
        ];
    }

    // ------------------------------------------------------------------
    // Hapus konten (satu kelas atau semua kelas)
    // ------------------------------------------------------------------

    /**
     * @return array{modul:int, video:int, soal:int, jawaban:int, file:int}
     */
    public function hapusKonten(string $bahasa, ?KmdClass $kelas = null, bool $termasukPeserta = false): array
    {
        $this->pastikanBoleh($bahasa, $kelas, $termasukPeserta);

        [$modulIds, $videoIds, $soalIds] = $this->idKonten($bahasa, $kelas);
        $db = DB::connection(self::KONEKSI);

        $fileVideo = $videoIds->isEmpty() ? collect() : $db->table('kmd_videos')->whereIn('id', $videoIds)
            ->whereNotNull('video')->where('video', '!=', '')->pluck('video')->unique()->values();

        $hasil = ['modul' => 0, 'video' => 0, 'soal' => 0, 'jawaban' => 0, 'file' => 0];

        $db->transaction(function () use ($db, $bahasa, $kelas, $modulIds, $videoIds, $soalIds, $termasukPeserta, &$hasil) {
            if ($termasukPeserta) {
                $this->hapusDataPeserta($bahasa, $kelas, $modulIds, $videoIds, $soalIds);
            }

            if ($soalIds->isNotEmpty()) {
                $hasil['jawaban'] = $db->table('kmd_answereds')->whereIn('questions_id', $soalIds)->delete();
            }
            if ($videoIds->isNotEmpty()) {
                $db->table('kmd_question_videos')->whereIn('videos_id', $videoIds)->delete();
            }
            if ($soalIds->isNotEmpty()) {
                // relasi soal ke video LAIN (di luar yang dihapus) juga dibersihkan
                $db->table('kmd_question_videos')->whereIn('questions_id', $soalIds)->delete();
                $hasil['soal'] = $db->table('kmd_questions')->whereIn('id', $soalIds)->delete();
            }
            if ($videoIds->isNotEmpty()) {
                $hasil['video'] = $db->table('kmd_videos')->whereIn('id', $videoIds)->delete();
            }
            if ($modulIds->isNotEmpty()) {
                $hasil['modul'] = $db->table('kmd_moduls')->whereIn('id', $modulIds)->delete();
            }
        });

        // File video: hapus HANYA jika tidak dipakai video lain (mis. versi Indonesia)
        $hasil['file'] = $this->hapusFileTakTerpakai($fileVideo);

        Log::warning('Konten bahasa dihapus', [
            'bahasa' => $bahasa, 'class_id' => $kelas?->id, 'termasuk_peserta' => $termasukPeserta,
            'hasil' => $hasil, 'oleh' => auth()->id(),
        ]);

        return $hasil;
    }

    // ------------------------------------------------------------------
    // Hapus bahasa sampai bersih
    // ------------------------------------------------------------------

    public function hapusBahasa(Language $language, bool $termasukPeserta = false): array
    {
        $bahasa = $language->code;
        $this->pastikanBoleh($bahasa, null, $termasukPeserta);

        $hasil = $this->hapusKonten($bahasa, null, $termasukPeserta);

        $db = DB::connection(self::KONEKSI);
        $db->transaction(function () use ($db, $bahasa, $language, &$hasil) {
            // Soal tanpa video (yatim) dalam bahasa ini
            $soalYatim = $db->table('kmd_questions')->where('language_code', $bahasa)->pluck('id');
            if ($soalYatim->isNotEmpty()) {
                $db->table('kmd_answereds')->whereIn('questions_id', $soalYatim)->delete();
                $db->table('kmd_question_videos')->whereIn('questions_id', $soalYatim)->delete();
                $hasil['soal'] += $db->table('kmd_questions')->whereIn('id', $soalYatim)->delete();
            }

            $hasil['nama_kelas'] = $this->tabelAda('kmd_class_translations')
                ? $db->table('kmd_class_translations')->where('language_code', $bahasa)->delete() : 0;
            $hasil['teks_tampilan'] = $this->tabelAda('ui_translations')
                ? $db->table('ui_translations')->where('language_code', $bahasa)->delete() : 0;

            // Peserta yang memilih bahasa ini akan diminta memilih bahasa lagi
            $hasil['peserta_direset'] = $db->table('kmd_users')->where('language_code', $bahasa)->update(['language_code' => null]);

            $language->delete();
        });

        Cache::forget(UiTranslation::cacheKey($bahasa));
        Cache::forget(Language::CACHE_KEY_ACTIVE);
        Cache::forget(Language::CACHE_KEY_DEFAULT);

        if (BahasaKelolaService::current() === $bahasa) {
            BahasaKelolaService::set(Language::defaultCode());
        }

        Log::warning('Bahasa dihapus', ['bahasa' => $bahasa, 'hasil' => $hasil, 'oleh' => auth()->id()]);

        return $hasil;
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    public function bahasaUtama(string $bahasa): bool
    {
        return $bahasa === 'id' || Language::where('code', $bahasa)->where('is_default', true)->exists();
    }

    protected function pastikanBoleh(string $bahasa, ?KmdClass $kelas, bool $termasukPeserta): void
    {
        if ($this->bahasaUtama($bahasa)) {
            throw new \RuntimeException(__('Bahasa utama tidak bisa dihapus.'));
        }

        $r = $this->ringkasan($bahasa, $kelas);

        if (! empty($r['penghalang'])) {
            $daftar = collect($r['penghalang'])->map(fn ($n, $k) => "$k ($n)")->implode(', ');
            throw new \RuntimeException(__('Tidak bisa dihapus karena masih ada: :daftar.', ['daftar' => $daftar]));
        }

        if ($r['ada_peserta'] && ! $termasukPeserta) {
            throw new \RuntimeException(__('Sudah ada data belajar peserta di bahasa ini. Centang "hapus juga data peserta" jika yakin.'));
        }
    }

    /**
     * @return array{0: Collection, 1: Collection, 2: Collection} [modulIds, videoIds, soalIds]
     */
    protected function idKonten(string $bahasa, ?KmdClass $kelas): array
    {
        $db = DB::connection(self::KONEKSI);

        $modulIds = $db->table('kmd_moduls')->where('language_code', $bahasa)
            ->when($kelas, fn ($q) => $q->where('kom_class_id', $kelas->id))
            ->pluck('id');

        $videoIds = $modulIds->isEmpty() ? collect() : $db->table('kmd_videos')->whereIn('modul_id', $modulIds)->pluck('id');

        // Soal milik video-video tsb, dan memang berbahasa ini (soal bahasa utama tidak tersentuh)
        $soalIds = $videoIds->isEmpty() ? collect() : $db->table('kmd_question_videos')
            ->join('kmd_questions', 'kmd_questions.id', '=', 'kmd_question_videos.questions_id')
            ->whereIn('kmd_question_videos.videos_id', $videoIds)
            ->where('kmd_questions.language_code', $bahasa)
            ->distinct()
            ->pluck('kmd_questions.id');

        return [$modulIds, $videoIds, $soalIds];
    }

    protected function hapusDataPeserta(string $bahasa, ?KmdClass $kelas, Collection $modulIds, Collection $videoIds, Collection $soalIds): void
    {
        $db = DB::connection(self::KONEKSI);

        $this->hapusWhereIn('kmd_user_progress_video', 'video_id', $videoIds);
        $this->hapusWhereIn('kmd_user_questions', 'question_id', $soalIds);
        $this->hapusWhereIn('kmd_progress_moduls', 'moduls_id', $modulIds);
        $this->hapusWhereIn('kmd_user_module', 'module_id', $modulIds);
        $this->hapusWhereIn('kmd_user_tutorial_status', 'module_id', $modulIds);

        if ($this->tabelAda('kmd_user_classes')) {
            $db->table('kmd_user_classes')->where('language_code', $bahasa)
                ->when($kelas, fn ($q) => $q->where('class_id', $kelas->id))
                ->delete();
        }
    }

    protected function hapusWhereIn(string $tabel, string $kolom, Collection $ids): void
    {
        if ($ids->isNotEmpty() && $this->tabelAda($tabel)) {
            DB::connection(self::KONEKSI)->table($tabel)->whereIn($kolom, $ids)->delete();
        }
    }

    protected function hitung(string $tabel, string $kolom, Collection $ids): int
    {
        if ($ids->isEmpty() || ! $this->tabelAda($tabel)) {
            return 0;
        }

        return DB::connection(self::KONEKSI)->table($tabel)->whereIn($kolom, $ids)->count();
    }

    protected function tabelAda(string $tabel): bool
    {
        return DB::connection(self::KONEKSI)->getSchemaBuilder()->hasTable($tabel);
    }

    protected function hapusFileTakTerpakai(Collection $files): int
    {
        $terhapus = 0;
        foreach ($files as $file) {
            $masihDipakai = DB::connection(self::KONEKSI)->table('kmd_videos')->where('video', $file)->exists();
            $path = public_path('videos/' . basename($file));
            if (! $masihDipakai && File::exists($path)) {
                File::delete($path);
                $terhapus++;
            }
        }

        return $terhapus;
    }
}
