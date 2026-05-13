<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdClass;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KomUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kmd\KmdUserModul;
use App\Models\Kmd\KmdPertanyaanUser;
use App\Models\Kmd\KmdQuestions;
use App\Models\Kmd\KmdAnswer;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KmdUserAbsensi;
use App\Models\Kmd\KmdVideo;
use App\Models\Kmd\KmdUserQuestions;
use App\Models\Kmd\KmdUserVideoProgress;
use App\Models\Kmd\KmdProgressModul;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Rules\CustomDbRule;



class InsertDataUserMassalController extends Controller
{
    public function indexBanyak()
    {
        return view('a_kom_digital_pusat.superadmin.user.indexMassal');
    }


    public function createBanyak()
    {
        // Ambil semua user, bisa ditambah paginate jika banyak data
        $users = KomUser::orderBy('created_at', 'asc')->get();
        $kelasKom = KmdClass::orderBy('created_at', 'asc')->get();
        $moduls = KmdModuls::orderBy('created_at', 'asc')->get();
        $KelasTutorial = KmdClassTutorial::orderBy('id', 'asc')->get();
        $videos = KmdVideo::orderBy('created_at', 'asc')->get();
        $questions = KmdQuestions::where('status', 'active')
            ->whereHas('answers', function ($query) {
                $query->where('is_true', true);
            })
            ->with([
                'answers' => function ($query) {
                    $query->where('is_true', true);
                }
            ])
            ->orderBy('created_at', 'asc') // urutkan desc berdasarkan id
            ->get();


        // Kirim data user ke view
        return view('a_kom_digital_pusat.superadmin.user.createMassal', compact('users', 'kelasKom', 'moduls', 'KelasTutorial', 'videos', 'questions'));
    }



    public function storeMassal(Request $request)
    {
        $request->validate([
            // Validasi user_id harus ada di tabel kmd_users pada koneksi mysql2
            'user_id' => ['required', new CustomDbRule('mysql2', 'kmd_users', 'id', 'exists')],

            // kelas_tutorial wajib array
            'kelas_tutorial' => 'sometimes|array',

            // Setiap elemen kelas_tutorial harus ada di tabel kmd_class_tutorial pada koneksi mysql2
            'kelas_tutorial.*' => [new CustomDbRule('mysql2', 'kmd_class_tutorial', 'id', 'exists')],

            // Tanggal booking wajib dan valid date
            'date_booking' => 'nullable|date',

            'videos' => 'nullable|array',
            'videos.*.checked' => 'sometimes|in:1',
            'videos.*.status' => 'sometimes|in:completed,in_progress,unlocked',

            // Moduls opsional array
            'moduls' => 'nullable|array',

            // Pertanyaan opsional array string max 1000 karakter
            'pertanyaan' => 'nullable|array',
            'pertanyaan.*' => 'nullable|string|max:1000',

            // User answers opsional array
            'user_answers' => 'nullable|array',

            // Setiap elemen user_answers harus array
            'user_answers.*' => 'array',

            // Setiap elemen dalam user_answers.* harus ada di tabel kmd_answereds pada koneksi mysql2
            'user_answers.*.*' => [new CustomDbRule('mysql2', 'kmd_answereds', 'id', 'exists')],
        ]);




        $userId = $request->user_id;
        $dateBooking = $request->date_booking;
        $pertanyaanArray = $request->pertanyaan ?? [];

        foreach ($request->input('kelas_tutorial', []) as $tutorialId) {

            $tutorial = KmdClassTutorial::find($tutorialId);

            if ($tutorial) {
                // Simpan booking kelas tutorial
                KmdUserClassTutorial::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'class_tutorial_id' => $tutorial->id,
                    ],
                    [
                        'modul_id' => $tutorial->modul_id,
                        'class_id' => $tutorial->class_id,
                        'rayon_id' => $tutorial->rayon_id,
                        'date_booking' => $dateBooking,
                        'status' => 'booked',
                    ]
                );

                // Simpan pertanyaan user (max 2 pertanyaan)
                foreach ($pertanyaanArray as $pertanyaanText) {
                    if (trim($pertanyaanText) !== '') {
                        KmdPertanyaanUser::create([
                            'user_id' => $userId,
                            'class_id' => $tutorial->class_id,
                            'modul_id' => $tutorial->modul_id,
                            'kelas_tutorial_id' => $tutorial->id,
                            'pengajar_id' => $tutorial->pengajar_id,
                            'rayon_id' => $tutorial->rayon_id,
                            'pertanyaan' => $pertanyaanText,
                            'status' => 'active',
                            'tgl_submit' => now(),
                        ]);
                    }
                }
            }
        }

        // Simpan ke KmdUserVideoProgress (jika ada video yang dicentang)
        if ($request->has('videos')) {
            foreach ($request->videos as $videoId => $videoData) {
                if (isset($videoData['checked']) && $videoData['checked'] == 1) {
                    KmdUserVideoProgress::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'video_id' => $videoId,
                        ],
                        [
                            'status' => $videoData['status'] ?? 'completed',
                            'date_publish' => now(),
                            'is_watched' => true,
                            'answered_question' => 1,
                            'is_correct' => true,
                            'watched_at' => now(),
                            'last_time' => 0,
                        ]
                    );
                }
            }
        }

        // Simpan ke KmdProgressModul (jika ada modul yang dicentang)
        if ($request->has('moduls') && is_array($request->moduls)) {
            foreach ($request->moduls as $modulId => $modulData) {
                if (isset($modulData['checked']) && $modulData['checked'] == 1) {
                    $status = $modulData['status'] ?? 'locked';

                    KmdProgressModul::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'moduls_id' => $modulId,
                        ],
                        [
                            'status' => $status,
                        ]
                    );
                }
            }
        }

        if ($request->has('user_answers')) {
            // Hapus dulu jawaban lama untuk user ini supaya tidak double insert (optional, tergantung kebutuhan)
            KmdUserQuestions::where('user_id', $userId)->delete();

            foreach ($request->user_answers as $questionId => $answerIds) {
                foreach ($answerIds as $answerId) {
                    // Cari data jawaban untuk cek is_true
                    $answer = KmdAnswer::find($answerId);
                    if ($answer) {
                        KmdUserQuestions::create([
                            'id' => (string) Str::uuid(),
                            'user_id' => $userId,
                            'question_id' => $questionId,
                            'answer_id' => $answerId,
                            'is_true' => $answer->is_true,
                            'status' => 'active', // bisa disesuaikan
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Booking kelas tutorial, progress video, dan pertanyaan berhasil disimpan!');
    }

}
