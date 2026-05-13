<?php

namespace App\Services\AdminCabang;

use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KmdDonasi;
use App\Models\Kmd\KmdUserAbsensi;
use App\Models\Kmd\KmdUserReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KelasService
{
    // public function getAbsensiData(string $classTutorialId): array
    // {
    //     try {
    //         $classTutorial = KmdClassTutorial::with(['pengajar', 'kelas', 'modul', 'rayon'])
    //             ->findOrFail($classTutorialId);

    //         $userClassTutorials = KmdUserClassTutorial::with(['user'])
    //             ->where('class_tutorial_id', $classTutorialId)
    //             ->get();

    //         Log::info('Absensi data retrieved successfully', [
    //             'class_tutorial_id' => $classTutorialId,
    //             'participants_count' => $userClassTutorials->count()
    //         ]);

    //         return [
    //             'classTutorial' => $classTutorial,
    //             'userClassTutorials' => $userClassTutorials
    //         ];
    //     } catch (\Exception $e) {
    //         Log::error('Error retrieving absensi data', [
    //             'class_tutorial_id' => $classTutorialId,
    //             'error' => $e->getMessage()
    //         ]);
    //         throw $e;
    //     }
    // }
    public function getAbsensiData(string $classTutorialId): array
{
    try {
        $classTutorial = KmdClassTutorial::with(['pengajar', 'kelas', 'modul', 'rayon'])
            ->findOrFail($classTutorialId);

        // Ambil hanya userclasstutorial_id yang sudah paid
        $paidUserClassTutorialIds = KmdDonasi::where('status', 'paid')
            ->whereHas('userClassTutorial', function ($query) use ($classTutorialId) {
                $query->where('class_tutorial_id', $classTutorialId);
            })
            ->pluck('userclasstutorial_id')
            ->unique()
            ->toArray();

        $userClassTutorials = KmdUserClassTutorial::with(['user'])
            ->where('class_tutorial_id', $classTutorialId)
            ->whereIn('id', $paidUserClassTutorialIds)
            ->get();

        Log::info('Absensi data retrieved successfully', [
            'class_tutorial_id' => $classTutorialId,
            'participants_count' => $userClassTutorials->count()
        ]);

        return [
            'classTutorial' => $classTutorial,
            'userClassTutorials' => $userClassTutorials
        ];
    } catch (\Exception $e) {
        Log::error('Error retrieving absensi data', [
            'class_tutorial_id' => $classTutorialId,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}


   

public function storeAbsensi(string $classTutorialId, array $data): void
{
    if (empty($data['absensi']) || !is_array($data['absensi'])) {
        Log::warning('Data absensi kosong atau tidak valid', ['class_tutorial_id' => $classTutorialId]);
        return;
    }

    DB::connection('mysql2')->transaction(function () use ($data, $classTutorialId) {
        foreach ($data['absensi'] as $userClassTutorialId => $absensiData) {
            if (!isset($absensiData['userclasstutorial_id'], $absensiData['user_id'], $absensiData['kehadiran'], $absensiData['status'])) {
                Log::warning('Data absensi tidak lengkap', ['data' => $absensiData]);
                continue;
            }

            $absensiAttributes = [
                'hadir' => $absensiData['kehadiran'] === 'hadir',
                'tidak_hadir' => $absensiData['kehadiran'] === 'tidak_hadir',
                'status' => $absensiData['status'],
            ];

            $absensi = KmdUserAbsensi::where('userclasstutorial_id', $absensiData['userclasstutorial_id'])
                ->where('user_id', $absensiData['user_id'])
                ->first();

            if ($absensi) {
                $absensi->update($absensiAttributes);
            } else {
                KmdUserAbsensi::create(array_merge([
                    'id' => (string) Str::uuid(),
                    'userclasstutorial_id' => $absensiData['userclasstutorial_id'],
                    'user_id' => $absensiData['user_id'],
                ], $absensiAttributes));
            }
        }

        Log::info('Absensi data stored successfully', [
            'class_tutorial_id' => $classTutorialId,
            'records_count' => count($data['absensi']),
        ]);
    });
}





    public function getReviewData(string $classTutorialId): array
    {
        try {
            $classTutorial = KmdClassTutorial::with(['pengajar', 'kelas', 'modul', 'rayon'])
                ->findOrFail($classTutorialId);

            $userClassTutorials = KmdUserClassTutorial::with(['user'])
                ->where('class_tutorial_id', $classTutorialId)
                ->get();

            Log::info('Review data retrieved successfully', [
                'class_tutorial_id' => $classTutorialId,
                'participants_count' => $userClassTutorials->count()
            ]);

            return [
                'classTutorial' => $classTutorial,
                'userClassTutorials' => $userClassTutorials
            ];
        } catch (\Exception $e) {
            Log::error('Error retrieving review data', [
                'class_tutorial_id' => $classTutorialId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function storeReview(string $classTutorialId, array $data): void
    {
        try {
            DB::transaction(function () use ($data) {
                foreach ($data['reviews'] as $reviewData) {
                    KmdUserReview::updateOrCreate(
                        [
                            'userclasstutorial_id' => $reviewData['userclasstutorial_id'],
                            'user_id' => $reviewData['user_id'],
                            'pengajar_id' => $reviewData['pengajar_id']
                        ],
                        [
                            'id' => Str::uuid(),
                            'isi_pengajaran' => $reviewData['isi_pengajaran'],
                            'kebenaran_teologis' => $reviewData['kebenaran_teologis'],
                            'pengajar_sampaikan_seluruh_materi' => $reviewData['pengajar_sampaikan_seluruh_materi'],
                            'pengajar_sampaikan_materi_utama' => $reviewData['pengajar_sampaikan_materi_utama'],
                            'pembukaan_materi' => $reviewData['pembukaan_materi'],
                            'kesinambungan_materi' => $reviewData['kesinambungan_materi'],
                            'kesaksian_pribadi' => $reviewData['kesaksian_pribadi'],
                            'materi_dipahami' => $reviewData['materi_dipahami'],
                            'sesuai_dengan_materi' => $reviewData['sesuai_dengan_materi']
                        ]
                    );
                }
            });

            Log::info('Review data stored successfully', [
                'class_tutorial_id' => $classTutorialId,
                'records_count' => count($data['reviews'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing review data', [
                'class_tutorial_id' => $classTutorialId,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
