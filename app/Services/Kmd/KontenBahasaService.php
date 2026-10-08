<?php

namespace App\Services\Kmd;

use App\Models\Kmd\KmdAnswer;
use App\Models\Kmd\KmdClass;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KmdQuestions;
use App\Models\Kmd\KmdQuestionVideos;
use App\Models\Kmd\KmdVideo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * kom_v02 — Konten belajar per bahasa:
 *  - salinKelas()  : menyalin seluruh modul → video → soal → jawaban (beserta kunci
 *                    jawaban & relasi soal-video) dari satu bahasa ke bahasa lain.
 *                    Hasil salinan masih berisi teks asli; superadmin tinggal
 *                    menerjemahkan teks & mengganti file video.
 *  - kelengkapan() : ringkasan apa saja yang belum siap sebelum bahasa diaktifkan.
 */
class KontenBahasaService
{
    /**
     * @return array{modul:int, video:int, soal:int, jawaban:int, dilewati:int}
     */
    public function salinKelas(KmdClass $kelas, string $dari, string $ke): array
    {
        $hasil = ['modul' => 0, 'video' => 0, 'soal' => 0, 'jawaban' => 0, 'dilewati' => 0];

        $moduls = KmdModuls::where('kom_class_id', $kelas->id)
            ->where('language_code', $dari)
            ->orderBy('order')
            ->get();

        DB::connection('mysql')->transaction(function () use ($moduls, $ke, &$hasil) {
            foreach ($moduls as $modul) {
                $sudahAda = KmdModuls::where('source_modul_id', $modul->id)->where('language_code', $ke)->exists();
                if ($sudahAda) {
                    $hasil['dilewati']++;
                    continue;
                }

                $this->salinModul($modul, $ke, $hasil);
            }
        });

        Log::info('Konten disalin ke bahasa lain', ['class_id' => $kelas->id, 'dari' => $dari, 'ke' => $ke, 'hasil' => $hasil]);

        return $hasil;
    }

    protected function salinModul(KmdModuls $modul, string $ke, array &$hasil): void
    {
        $modulBaru = KmdModuls::create([
            'name' => $modul->name,
            'description' => $modul->description,
            'order' => $modul->order,
            'kom_class_id' => $modul->kom_class_id,
            'status' => $modul->status,
            'language_code' => $ke,
            'source_modul_id' => $modul->id,
        ]);
        $hasil['modul']++;

        $petaSoal = []; // id soal asal → id soal baru (1 soal bisa dipakai di beberapa video)

        $videos = KmdVideo::where('modul_id', $modul->id)->orderBy('sort_video')->get();
        foreach ($videos as $video) {
            $videoBaru = KmdVideo::create([
                'name' => $video->name,
                'description' => $video->description,
                'durations' => $video->durations,
                'sort_video' => $video->sort_video,
                'url_video' => $video->url_video,
                'video' => $video->video, // sementara memakai file yang sama; superadmin mengganti versi bahasa baru
                'modul_id' => $modulBaru->id,
                'status' => $video->status,
                'source_video_id' => $video->id,
            ]);
            $hasil['video']++;

            $relasi = KmdQuestionVideos::where('videos_id', $video->id)->get();
            foreach ($relasi as $qv) {
                if (! isset($petaSoal[$qv->questions_id])) {
                    $soal = KmdQuestions::find($qv->questions_id);
                    if (! $soal) {
                        continue;
                    }

                    $soalBaru = KmdQuestions::create([
                        'questions' => $soal->questions,
                        'status' => $soal->status,
                        'language_code' => $ke,
                        'source_question_id' => $soal->id,
                    ]);
                    $hasil['soal']++;

                    foreach (KmdAnswer::where('questions_id', $soal->id)->get() as $jawaban) {
                        KmdAnswer::create([
                            'questions_id' => $soalBaru->id,
                            'answer' => $jawaban->answer,
                            'is_true' => $jawaban->is_true,
                            'status' => $jawaban->status,
                            'source_answer_id' => $jawaban->id,
                        ]);
                        $hasil['jawaban']++;
                    }

                    $petaSoal[$qv->questions_id] = $soalBaru->id;
                }

                KmdQuestionVideos::create([
                    'videos_id' => $videoBaru->id,
                    'questions_id' => $petaSoal[$qv->questions_id],
                    'status' => $qv->status,
                ]);
            }
        }
    }

    /**
     * Ringkasan kelengkapan konten satu kelas untuk satu bahasa.
     */
    public function kelengkapan(KmdClass $kelas, string $bahasa): array
    {
        $modulIds = KmdModuls::where('kom_class_id', $kelas->id)->where('language_code', $bahasa)->pluck('id');
        $videos = KmdVideo::whereIn('modul_id', $modulIds)->get(['id', 'name', 'video', 'url_video', 'source_video_id']);
        $videoIds = $videos->pluck('id');

        $soalIds = KmdQuestionVideos::whereIn('videos_id', $videoIds)->pluck('questions_id')->unique();
        $videoDenganSoal = KmdQuestionVideos::whereIn('videos_id', $videoIds)->where('status', 'active')->pluck('videos_id')->unique();

        $soalDenganKunci = KmdAnswer::whereIn('questions_id', $soalIds)->where('is_true', 1)->where('status', 'active')
            ->pluck('questions_id')->unique();

        // Teks yang masih sama persis dengan bahasa asal = belum diterjemahkan
        $belumTerjemah = 0;
        if ($bahasa !== \App\Models\Kmd\Language::defaultCode()) {
            $belumTerjemah += KmdModuls::whereIn('kmd_moduls.id', $modulIds)
                ->join('kmd_moduls as asal', 'asal.id', '=', 'kmd_moduls.source_modul_id')
                ->whereColumn('asal.name', 'kmd_moduls.name')->count();
            $belumTerjemah += KmdVideo::whereIn('kmd_videos.id', $videoIds)
                ->join('kmd_videos as asal', 'asal.id', '=', 'kmd_videos.source_video_id')
                ->whereColumn('asal.name', 'kmd_videos.name')->count();
            $belumTerjemah += KmdQuestions::whereIn('kmd_questions.id', $soalIds)
                ->join('kmd_questions as asal', 'asal.id', '=', 'kmd_questions.source_question_id')
                ->whereColumn('asal.questions', 'kmd_questions.questions')->count();
        }

        $videoSamaDenganAsal = 0;
        if ($bahasa !== \App\Models\Kmd\Language::defaultCode()) {
            $videoSamaDenganAsal = KmdVideo::whereIn('kmd_videos.id', $videoIds)
                ->join('kmd_videos as asal', 'asal.id', '=', 'kmd_videos.source_video_id')
                ->whereColumn('asal.video', 'kmd_videos.video')
                ->whereNotNull('kmd_videos.video')->count();
        }

        $masalah = [];
        if ($modulIds->isEmpty()) {
            $masalah[] = __('Belum ada modul.');
        }
        if (($n = $videos->filter(fn ($v) => empty($v->video) && empty($v->url_video))->count()) > 0) {
            $masalah[] = __(':n video belum ada file / link.', ['n' => $n]);
        }
        if ($videoSamaDenganAsal > 0) {
            $masalah[] = __(':n video masih memakai file bahasa asal.', ['n' => $videoSamaDenganAsal]);
        }
        if (($n = $videoIds->diff($videoDenganSoal)->count()) > 0) {
            $masalah[] = __(':n video belum punya pertanyaan.', ['n' => $n]);
        }
        if (($n = $soalIds->diff($soalDenganKunci)->count()) > 0) {
            $masalah[] = __(':n pertanyaan belum punya jawaban benar.', ['n' => $n]);
        }
        if ($belumTerjemah > 0) {
            $masalah[] = __(':n teks masih sama dengan bahasa asal (belum diterjemahkan).', ['n' => $belumTerjemah]);
        }

        return [
            'modul' => $modulIds->count(),
            'video' => $videos->count(),
            'soal' => $soalIds->count(),
            'masalah' => $masalah,
            'siap' => $modulIds->isNotEmpty() && empty($masalah),
        ];
    }
}
