<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KmdVideo;
use App\Models\Kmd\KmdQuestions;
use App\Models\Kmd\KmdAnswer;
use App\Models\Kmd\KmdQuestionVideos;
use App\Models\Kmd\KmdUserVideoProgress;
use App\Models\Kmd\KmdUserModul;
use App\Models\Kmd\KmdUserQuestions;
use App\Models\Kmd\KmdProgressModul;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KmdVideoLearningController extends Controller
{
    /**
     * Display video learning page
     */
    public function index()
    {
        try {
            $userId = auth()->id();

            // Get all modules with videos
            $moduls = KmdModuls::with([
                'videos' => function ($query) {
                    $query->where('status', 'active')->orderBy('sort_video');
                }
            ])
                ->where('status', 'active')
                ->orderBy('order')
                ->get();

            // Get user video progress
            $userVideoProgress = KmdUserVideoProgress::where('user_id', $userId)
                ->get()
                ->keyBy('video_id');

            // Get user answered questions
            $userAnsweredQuestions = KmdUserQuestions::where('user_id', $userId)
                ->where('is_true', 1)
                ->pluck('question_id')
                ->toArray();

            // Get completed modules
            $completedModules = KmdProgressModul::where('user_id', $userId)
                ->where('status', 'completed')
                ->pluck('moduls_id')
                ->toArray();

            // Get user module progress
            $userModuleProgress = KmdUserModul::where('user_id', $userId)
                ->get()
                ->keyBy('module_id');

            // Get last watched video
            $lastWatchedVideo = KmdUserVideoProgress::where('user_id', $userId)
                ->whereNotNull('last_time')
                ->orderBy('watched_at', 'desc')
                ->first();

            Log::info('Video learning page loaded', [
                'user_id' => $userId,
                'modules_count' => $moduls->count()
            ]);

            return view('a_kom_digital_pusat.dashboard.index', compact(
                'moduls',
                'userVideoProgress',
                'userAnsweredQuestions',
                'completedModules',
                'userModuleProgress',
                'lastWatchedVideo'
            ));

        } catch (\Exception $e) {
            Log::error('Index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat halaman pembelajaran.');
        }
    }

    /**
     * Save user watch time progress
     */
    // public function saveWatchTime(Request $request)
    // {
    //     try {
    //         $userId = auth()->id();
    //         $videoId = $request->video_id;
    //         $time = (int) $request->time;
    //         $completed = $request->completed === true || $request->completed === 'true';

    //         $status = 'in_progress';
    //         if ($completed) {
    //             $status = 'completed';
    //         } elseif ($time > 0) {
    //             $status = 'in_progress';
    //         } else {
    //             $status = 'unlocked';
    //         }

    //         KmdUserVideoProgress::updateOrCreate(
    //             [
    //                 'user_id' => $userId,
    //                 'video_id' => $videoId
    //             ],
    //             [
    //                 'last_time' => $time,
    //                 'watched_at' => now(),
    //                 'is_watched' => $completed,
    //                 'status' => $status,
    //                 'date_publish' => now()
    //             ]
    //         );

    //         Log::info('Watch time saved', [
    //             'video_id' => $videoId,
    //             'time' => $time,
    //             'completed' => $completed,
    //             'status' => $status
    //         ]);

    //         return response()->json(['success' => true]);

    //     } catch (\Exception $e) {
    //         Log::error('Save watch time error: ' . $e->getMessage());
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }
    public function saveWatchTime(Request $request)
    {
        try {
            $userId = auth()->id();
            $videoId = $request->input('video_id');
            $time = (int) $request->input('time', 0);
            $completed = filter_var($request->input('completed'), FILTER_VALIDATE_BOOLEAN);

            // Ambil progress video user yang sudah ada
            $existingProgress = KmdUserVideoProgress::where('user_id', $userId)
                ->where('video_id', $videoId)
                ->first();

            // Tentukan status baru berdasarkan kondisi dan status lama
            if ($existingProgress && $existingProgress->status === 'completed') {
                // Jika sudah completed, jangan turunkan status
                $status = 'completed';
            } else {
                if ($completed) {
                    $status = 'completed';
                } elseif ($time > 0) {
                    $status = 'in_progress';
                } else {
                    $status = 'unlocked';
                }
            }

            // Simpan atau update progress video user
            KmdUserVideoProgress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'video_id' => $videoId,
                ],
                [
                    'last_time' => $time,
                    'watched_at' => now(),
                    'is_watched' => $completed,
                    'status' => $status,
                    'date_publish' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Watch time saved successfully',
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save watch time', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save watch time',
            ], 500);
        }
    }


    /**
     * Get questions for specific video
     */
    public function getVideoQuestions($videoId)
    {
        try {
            $userId = auth()->id();

            Log::info('Getting questions for video', [
                'user_id' => $userId,
                'video_id' => $videoId
            ]);

            // Check if user already answered question for this video
            $videoProgress = KmdUserVideoProgress::where('user_id', $userId)
                ->where('video_id', $videoId)
                ->first();

            if ($videoProgress && $videoProgress->answered_question) {
                Log::info('User already answered questions for this video');
                return response()->json([
                    'has_answered' => true,
                    'message' => 'Anda sudah menjawab pertanyaan untuk video ini.'
                ]);
            }

            // Get questions for this video
            $questionVideos = KmdQuestionVideos::where('videos_id', $videoId)
                ->where('status', 'active')
                ->get();

            Log::info('Found question videos', [
                'count' => $questionVideos->count(),
                'video_id' => $videoId
            ]);

            if ($questionVideos->isEmpty()) {
                Log::info('No questions found for video');
                return response()->json([
                    'has_questions' => false,
                    'message' => 'Video ini tidak memiliki pertanyaan.'
                ]);
            }

            $questions = [];
            foreach ($questionVideos as $qv) {
                // Get question
                $question = KmdQuestions::find($qv->questions_id);
                if (!$question) {
                    Log::warning('Question not found', ['question_id' => $qv->questions_id]);
                    continue;
                }

                // Get answers for this question
                $answers = KmdAnswer::where('questions_id', $question->id)
                    ->where('status', 'active')
                    ->get();

                Log::info('Processing question', [
                    'question_id' => $question->id,
                    'question_text' => $question->questions,
                    'answers_count' => $answers->count()
                ]);

                $questions[] = [
                    'id' => $question->id,
                    'question' => $question->questions,
                    'answers' => $answers->map(function ($answer) {
                        return [
                            'id' => $answer->id,
                            'answer' => $answer->answer
                        ];
                    })
                ];
            }

            return response()->json([
                'has_answered' => false,
                'has_questions' => true,
                'questions' => $questions
            ]);

        } catch (\Exception $e) {
            Log::error('Get video questions error: ' . $e->getMessage(), [
                'video_id' => $videoId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat pertanyaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit answer for question
     */
    public function submitAnswer(Request $request)
{
    try {
        $userId = auth()->id();
        $videoId = $request->video_id;
        $answers = $request->answers; // Expect array of ['question_id' => ..., 'answer_id' => ...]

        if (!$videoId || !is_array($answers) || count($answers) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak lengkap. Silakan coba lagi.'
            ]);
        }

        DB::beginTransaction();

        foreach ($answers as $answer) {
            $questionId = $answer['question_id'] ?? null;
            $answerId = $answer['answer_id'] ?? null;

            if (!$questionId || !$answerId) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Data jawaban tidak lengkap.'
                ]);
            }

            // Cek apakah user sudah menjawab pertanyaan ini
            $existingAnswer = KmdUserQuestions::where('user_id', $userId)
                ->where('question_id', $questionId)
                ->first();

            if ($existingAnswer) {
                // Jika sudah pernah jawab, skip atau bisa juga return error tergantung kebutuhan
                continue;
            }

            // Ambil jawaban benar
            $correctAnswer = KmdAnswer::where('questions_id', $questionId)
                ->where('is_true', 1)
                ->where('status', 'active')
                ->first();

            if (!$correctAnswer) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Tidak ada jawaban benar untuk pertanyaan ID $questionId."
                ]);
            }

            // Cek jawaban benar atau salah
            if ((string)$correctAnswer->id !== (string)$answerId) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "❌ Jawaban salah untuk pertanyaan ID $questionId. Silakan coba lagi."
                ]);
            }

            // Simpan jawaban benar user
            KmdUserQuestions::create([
                'user_id' => $userId,
                'question_id' => $questionId,
                'answer_id' => $answerId,
                'is_true' => 1,
                'status' => 'active'
            ]);
        }

        // Update progress video setelah semua jawaban benar
        KmdUserVideoProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'video_id' => $videoId
            ],
            [
                'answered_question' => true,
                'is_correct' => true,
                'status' => 'completed',
                'is_watched' => true,
                'watched_at' => now()
            ]
        );

        // Cek apakah modul selesai
        $moduleCompleted = $this->checkModuleCompletion($userId, $videoId);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => '🎉 Selamat! Semua jawaban Anda benar!',
            'module_completed' => $moduleCompleted
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Submit answer error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Gagal menyimpan jawaban: ' . $e->getMessage()
        ], 500);
    }
}


    /**
     * Check if module is completed
     */
    private function checkModuleCompletion($userId, $videoId)
    {
        try {
            Log::info('checkModuleCompletion called', ['user_id' => $userId, 'video_id' => $videoId]);

            $video = KmdVideo::find($videoId);
            if (!$video) {
                Log::warning('Video not found', ['video_id' => $videoId]);
                return false;
            }

            $moduleId = $video->modul_id;

            $moduleVideos = KmdVideo::where('modul_id', $moduleId)
                ->where('status', 'active')
                ->orderBy('sort_video')
                ->get();

            if ($moduleVideos->isEmpty()) {
                Log::warning('No active videos in module', ['module_id' => $moduleId]);
                return false;
            }

            $completedVideosCount = KmdUserVideoProgress::where('user_id', $userId)
                ->whereIn('video_id', $moduleVideos->pluck('id'))
                ->where('status', 'completed')
                ->where('answered_question', true)
                ->where('is_correct', true)
                ->count();

            $totalVideosCount = $moduleVideos->count();
            $moduleCompleted = $completedVideosCount >= $totalVideosCount;

            Log::info('Module completion status', [
                'completedVideosCount' => $completedVideosCount,
                'totalVideosCount' => $totalVideosCount,
                'moduleCompleted' => $moduleCompleted
            ]);



            if ($moduleCompleted) {
                // Cek apakah record sudah ada
                $userModule = KmdUserModul::where('user_id', $userId)
                    ->where('module_id', $moduleId)
                    ->first();

                if (!$userModule) {
                    // Insert baru dengan UUID
                    KmdUserModul::create([
                        'id' => (string) Str::uuid(),
                        'user_id' => $userId,
                        'module_id' => $moduleId,
                        'is_completed' => true,
                        'completed_at' => now(),
                        'tutorial_unlocked' => true,
                    ]);
                } else {
                    // Update record yang sudah ada
                    $userModule->update([
                        'is_completed' => true,
                        'completed_at' => now(),
                        'tutorial_unlocked' => true,
                    ]);
                }

                // Update progress modul
                KmdProgressModul::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'moduls_id' => $moduleId,  // sesuaikan dengan nama kolom di DB
                    ],
                    [
                        'status' => 'completed',
                    ]
                );


                Log::info('Module marked as completed');
            }


            return $moduleCompleted;

        } catch (\Exception $e) {
            Log::error('Check module completion error: ' . $e->getMessage());
            return false;
        }
    }


    // private function checkModuleCompletion($userId, $videoId)
    // {
    //     try {
    //         // Get video and module info
    //         $video = KmdVideo::find($videoId);
    //         if (!$video) {
    //             return false;
    //         }

    //         $moduleId = $video->modul_id;

    //         Log::info('Checking module completion', [
    //             'user_id' => $userId,
    //             'module_id' => $moduleId
    //         ]);

    //         // Get all videos in this module
    //         $moduleVideos = KmdVideo::where('modul_id', $moduleId)
    //             ->where('status', 'active')
    //             ->orderBy('sort_video')
    //             ->get();

    //         if ($moduleVideos->isEmpty()) {
    //             return false;
    //         }

    //         // Count completed videos with answered questions
    //         $completedVideosCount = KmdUserVideoProgress::where('user_id', $userId)
    //             ->whereIn('video_id', $moduleVideos->pluck('id'))
    //             ->where('status', 'completed')
    //             ->where('answered_question', true)
    //             ->where('is_correct', true)
    //             ->count();

    //         $totalVideosCount = $moduleVideos->count();
    //         $moduleCompleted = $completedVideosCount >= $totalVideosCount;

    //         Log::info('Module completion result', [
    //             'completed_videos' => $completedVideosCount,
    //             'total_videos' => $totalVideosCount,
    //             'module_completed' => $moduleCompleted
    //         ]);

    //         if ($moduleCompleted) {
    //             // Save module completion
    //             KmdUserModul::updateOrCreate(
    //                 [
    //                     'user_id' => $userId,
    //                     'module_id' => $moduleId
    //                 ],
    //                 [
    //                     'is_completed' => true,
    //                     'completed_at' => now(),
    //                     'tutorial_unlocked' => true
    //                 ]
    //             );

    //             KmdProgressModul::updateOrCreate(
    //                 [
    //                     'user_id' => $userId,
    //                     'moduls_id' => $moduleId
    //                 ],
    //                 [
    //                     'status' => 'completed'
    //                 ]
    //             );

    //             Log::info('Module marked as completed');
    //         }

    //         return $moduleCompleted;

    //     } catch (\Exception $e) {
    //         Log::error('Check module completion error: ' . $e->getMessage());
    //         return false;
    //     }
    // }

    /**
     * Get next unlocked video for user
     */
    public function getNextVideo(Request $request)
    {
        try {
            $userId = auth()->id();
            $currentVideoId = $request->current_video_id;

            $currentVideo = KmdVideo::find($currentVideoId);
            if (!$currentVideo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Video tidak ditemukan'
                ]);
            }

            // Find next video in same module
            $nextVideo = KmdVideo::where('modul_id', $currentVideo->modul_id)
                ->where('sort_video', '>', $currentVideo->sort_video)
                ->where('status', 'active')
                ->orderBy('sort_video')
                ->first();

            // If no next video in module, find first video in next module
            if (!$nextVideo) {
                $currentModule = KmdModuls::find($currentVideo->modul_id);
                $nextModule = KmdModuls::where('order', '>', $currentModule->order)
                    ->where('status', 'active')
                    ->orderBy('order')
                    ->first();

                if ($nextModule) {
                    $nextVideo = KmdVideo::where('modul_id', $nextModule->id)
                        ->where('status', 'active')
                        ->orderBy('sort_video')
                        ->first();
                }
            }

            if (!$nextVideo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada video selanjutnya'
                ]);
            }

            return response()->json([
                'success' => true,
                'next_video' => [
                    'id' => $nextVideo->id,
                    'name' => $nextVideo->name,
                    'module_name' => $nextVideo->modul->name ?? 'Unknown Module'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get next video error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user learning statistics
     */
    public function getUserStats()
    {
        try {
            $userId = auth()->id();

            // Total videos
            $totalVideos = KmdVideo::where('status', 'active')->count();

            // Completed videos
            $completedVideos = KmdUserVideoProgress::where('user_id', $userId)
                ->where('status', 'completed')
                ->where('answered_question', true)
                ->count();

            // Total modules
            $totalModules = KmdModuls::where('status', 'active')->count();

            // Completed modules
            $completedModules = KmdProgressModul::where('user_id', $userId)
                ->where('status', 'completed')
                ->count();

            // Calculate progress percentage
            $videoProgress = $totalVideos > 0 ? round(($completedVideos / $totalVideos) * 100) : 0;
            $moduleProgress = $totalModules > 0 ? round(($completedModules / $totalModules) * 100) : 0;

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_videos' => $totalVideos,
                    'completed_videos' => $completedVideos,
                    'video_progress' => $videoProgress,
                    'total_modules' => $totalModules,
                    'completed_modules' => $completedModules,
                    'module_progress' => $moduleProgress
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get user stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user progress (for testing purposes)
     */
    public function resetProgress(Request $request)
    {
        try {
            $userId = auth()->id();

            // Only allow in debug mode
            if (!config('app.debug')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            DB::beginTransaction();

            // Delete user progress
            KmdUserVideoProgress::where('user_id', $userId)->delete();
            KmdUserQuestions::where('user_id', $userId)->delete();
            KmdUserModul::where('user_id', $userId)->delete();
            KmdProgressModul::where('user_id', $userId)->delete();

            DB::commit();

            Log::info('User progress reset', ['user_id' => $userId]);

            return response()->json([
                'success' => true,
                'message' => 'Progress berhasil direset'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Reset progress error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get certificate eligibility
     */
    public function getCertificateEligibility()
    {
        try {
            $userId = auth()->id();

            // Check if all modules completed
            $totalModules = KmdModuls::where('status', 'active')->count();
            $completedModules = KmdProgressModul::where('user_id', $userId)
                ->where('status', 'completed')
                ->count();

            $isEligible = $totalModules > 0 && $completedModules >= $totalModules;

            return response()->json([
                'success' => true,
                'eligible' => $isEligible,
                'completed_modules' => $completedModules,
                'total_modules' => $totalModules,
                'message' => $isEligible ?
                    'Selamat! Anda berhak mendapatkan sertifikat.' :
                    'Selesaikan semua modul untuk mendapatkan sertifikat.'
            ]);

        } catch (\Exception $e) {
            Log::error('Get certificate eligibility error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug method - get user progress (simple)
     */
    public function debugUserProgress()
    {
        try {
            $userId = auth()->id();

            // Get all user video progress
            $videoProgress = KmdUserVideoProgress::where('user_id', $userId)->get();

            // Get all modules with videos
            $modules = KmdModuls::with([
                'videos' => function ($query) {
                    $query->where('status', 'active')->orderBy('sort_video');
                }
            ])->where('status', 'active')->orderBy('order')->get();

            $moduleAnalysis = [];

            foreach ($modules as $module) {
                $moduleVideos = $module->videos;
                $completedVideos = 0;
                $videoDetails = [];

                foreach ($moduleVideos as $video) {
                    $progress = $videoProgress->where('video_id', $video->id)->first();

                    $isCompleted = $progress &&
                        $progress->status === 'completed' &&
                        $progress->answered_question == true &&
                        $progress->is_correct == true;

                    if ($isCompleted) {
                        $completedVideos++;
                    }

                    $videoDetails[] = [
                        'video_id' => $video->id,
                        'video_name' => $video->name,
                        'sort_video' => $video->sort_video,
                        'status' => $progress->status ?? 'not_started',
                        'answered_question' => $progress->answered_question ?? false,
                        'is_correct' => $progress->is_correct ?? false,
                        'is_completed' => $isCompleted
                    ];
                }

                $moduleAnalysis[] = [
                    'module_id' => $module->id,
                    'module_name' => $module->name,
                    'module_order' => $module->order,
                    'total_videos' => $moduleVideos->count(),
                    'completed_videos' => $completedVideos,
                    'is_module_completed' => $completedVideos >= $moduleVideos->count(),
                    'completion_percentage' => $moduleVideos->count() > 0 ? round(($completedVideos / $moduleVideos->count()) * 100, 2) : 0,
                    'videos' => $videoDetails
                ];
            }

            return response()->json([
                'success' => true,
                'user_id' => $userId,
                'modules_analysis' => $moduleAnalysis,
                'total_modules' => $modules->count(),
                'completed_modules' => collect($moduleAnalysis)->where('is_module_completed', true)->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function kelasTutorial()
    {
        return view('a_kom_digital_pusat.dashboard.kelasTutorial');
    }
}
