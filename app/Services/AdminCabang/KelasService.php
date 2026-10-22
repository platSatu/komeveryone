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

        // kom_v02: hanya peserta yang rangkaiannya sudah LUNAS (callback Midtrans),
        // termasuk jadwal pengganti karena tidak hadir (tanpa persembahan baru).
        // Eager load 'absensi' supaya form absensi bisa menampilkan status
        // hadir/tidak hadir yang sudah pernah diinput sebelumnya (untuk edit/koreksi)
        $userClassTutorials = KmdUserClassTutorial::with(['user', 'absensi'])
            ->where('class_tutorial_id', $classTutorialId)
            ->lunas()
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


   

public function storeAbsensi(string $classTutorialId, array $data): array
{
    if (empty($data['absensi']) || !is_array($data['absensi'])) {
        Log::warning('Data absensi kosong atau tidak valid', ['class_tutorial_id' => $classTutorialId]);
        return ['saved' => 0, 'locked' => 0];
    }

    $savedCount = 0;
    $lockedCount = 0;

    // NB: KmdUserAbsensi memakai connection 'mysql' (lihat $connection di model),
    // jadi transaksi harus dibuka di connection yang sama juga (sebelumnya salah
    // pakai 'mysql2' sehingga transaksinya tidak benar-benar membungkus query ini).
    DB::connection('mysql')->transaction(function () use ($data, $classTutorialId, &$savedCount, &$lockedCount) {
        foreach ($data['absensi'] as $userClassTutorialId => $absensiData) {
            if (!isset($absensiData['userclasstutorial_id'], $absensiData['user_id'], $absensiData['kehadiran'], $absensiData['status'])) {
                Log::warning('Data absensi tidak lengkap', ['data' => $absensiData]);
                continue;
            }

            $existing = KmdUserAbsensi::where('userclasstutorial_id', $absensiData['userclasstutorial_id'])
                ->where('user_id', $absensiData['user_id'])
                ->first();

            // Begitu peserta pernah ditandai HADIR, datanya terkunci permanen --
            // tidak bisa diubah lagi lewat form ini (baik status kehadiran maupun
            // keterangan). Ini mencegah admin bolak-balik toggle hadir <-> tidak
            // hadir, sekaligus menjaga integritas syarat sertifikat.
            if ($existing && $existing->hadir) {
                $lockedCount++;
                Log::info('Absensi dilewati karena sudah terkunci (hadir)', [
                    'user_id' => $absensiData['user_id'],
                    'userclasstutorial_id' => $absensiData['userclasstutorial_id'],
                ]);
                continue;
            }

            $absensiAttributes = [
                'hadir' => $absensiData['kehadiran'] === 'hadir',
                'tidak_hadir' => $absensiData['kehadiran'] === 'tidak_hadir',
                'status' => $absensiData['status'],
            ];

            // updateOrCreate: 1 peserta hanya boleh punya 1 baris absensi per
            // modul/kelas (kunci: userclasstutorial_id + user_id). Kalau baris
            // sudah ada (dan belum terkunci), DI-UPDATE -- bukan menambah baris
            // baru, walaupun admin submit form yang sama berkali-kali.
            KmdUserAbsensi::updateOrCreate(
                [
                    'userclasstutorial_id' => $absensiData['userclasstutorial_id'],
                    'user_id' => $absensiData['user_id'],
                ],
                $absensiAttributes
            );

            $savedCount++;
        }

        Log::info('Absensi data stored successfully', [
            'class_tutorial_id' => $classTutorialId,
            'saved_count' => $savedCount,
            'locked_skipped_count' => $lockedCount,
        ]);
    });

    return ['saved' => $savedCount, 'locked' => $lockedCount];
}





    public function getReviewData(string $classTutorialId): array
    {
        try {
            $classTutorial = KmdClassTutorial::with(['pengajar', 'kelas', 'modul', 'rayon'])
                ->findOrFail($classTutorialId);

            $userClassTutorials = KmdUserClassTutorial::with(['user'])
                ->where('class_tutorial_id', $classTutorialId)
                ->lunas() // kom_v02
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
