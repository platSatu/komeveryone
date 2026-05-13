<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use App\Models\Kom\KomVideoPertanyaan;
use Illuminate\Http\Request;
use App\Models\Kom\KomModul;
use App\Models\Kom\KomVideo;
use App\Models\Kom\KomJawaban;
use App\Models\Kom\KomUserJawabanPertanyaan;
use App\Models\Kom\KomUserVideoProgress;
use App\Models\Kom\KomUserModulProgress;
use Illuminate\Support\Facades\Auth;

class DashboardKomController extends Controller
{
    // Menampilkan dashboard utama
    public function index()
    {
        return view('kom_digital.dashboard.index');
    }

    // Menampilkan modul kom 100
    public function Kom100()
    {
        // Ambil data progres video pengguna (menggunakan auth kom)
        $userProgres = $this->getProgresVideoUser();

        // Ambil semua modul dan video beserta pertanyaan
        $komModuls = KomModul::with('videos.pertanyaan')->where('kom_class_id', 1)->get();

        // Ambil ID video yang sudah diselesaikan user
        $userVideoProgress = $userProgres['progres_video'];

        // Ambil ID modul yang punya setidaknya 1 video yang sudah ditonton user
        $userProgress = KomModul::whereHas('videos', function ($query) use ($userVideoProgress) {
            $query->whereIn('id', $userVideoProgress);
        })->pluck('id')->toArray();

        return view('kom_digital.dashboard.kom100', compact('komModuls', 'userVideoProgress', 'userProgress'));
    }


    // Menampilkan modul berdasarkan ID
    public function showModul($id)
    {
        $komModul = KomModul::with('videos')->find($id);

        // Jika modul tidak ditemukan, redirect ke halaman utama
        if (!$komModul) {
            return redirect()->route('kom.dashboard')->with('error', 'Modul tidak ditemukan');
        }

        // Ambil status progres video yang sudah diselesaikan oleh user
        $userProgress = KomUserVideoProgress::where('kom_user_id', auth()->user()->id)
            ->pluck('kom_video_id')
            ->toArray();

        return view('kom_digital.dashboard.kom100', compact('komModul', 'userProgress'));
    }

    // Mengambil pertanyaan berdasarkan ID video
    public function getPertanyaanByVideo($videoId)
    {
        // Mendapatkan pertanyaan yang terkait dengan video, termasuk jawaban dari setiap pertanyaan
        $pertanyaanList = KomVideoPertanyaan::where('kom_video_id', $videoId)
            ->with(['pertanyaan.jawaban']) // Pastikan kita memuat jawaban
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->pertanyaan->id,
                    'pertanyaan' => $item->pertanyaan->pertanyaan,
                    'jawaban' => $item->pertanyaan->jawaban->map(function ($j) {
                        return [
                            'id' => $j->id,
                            'jawaban' => $j->jawaban,
                        ];
                    }),
                ];
            });

        return response()->json($pertanyaanList);
    }


    public function validateMultipleAnswers(Request $request)
    {
        $userId = auth('web')->id(); // pakai guard 'kom'

        $jawabanUser = $request->input('jawaban');
        $semuaBenar = true;

        foreach ($jawabanUser as $pertanyaanId => $jawabanId) {
            $jawaban = KomJawaban::where('id', $jawabanId)
                ->where('pertanyaan_id', $pertanyaanId)
                ->first();

            $isBenar = $jawaban && $jawaban->is_benar == 1;

            KomUserJawabanPertanyaan::create([
                'kom_user_id' => $userId,
                'pertanyaan_id' => $pertanyaanId,
                'jawaban_id' => $jawabanId,
                'jawaban_benar' => $isBenar,
            ]);

            if (!$isBenar) {
                $semuaBenar = false;
            }
        }

        return response()->json([
            'success' => $semuaBenar,
        ]);
    }

    public function simpanJawaban(Request $request)
    {
        $userId = Auth::guard('web')->id();
        $jawabanInput = $request->input('jawaban'); // bentuk: [pertanyaan_id => jawaban_id]

        $semuaBenar = true; // Jika ada satu jawaban yang salah, ini jadi false

        foreach ($jawabanInput as $pertanyaanId => $jawabanId) {
            // Temukan jawaban yang dipilih oleh user
            $jawaban = KomJawaban::where('pertanyaan_id', $pertanyaanId)->where('id', $jawabanId)->first();

            // Debug: cek jawaban dan is_benar
            \Log::debug('Jawaban: ' . $jawaban->jawaban . ' | is_benar: ' . $jawaban->is_benar);

            // Pastikan jawaban ada dan periksa apakah benar
            if ($jawaban && $jawaban->is_benar == 1) {
                $isBenar = true;
            } else {
                $isBenar = false;
                $semuaBenar = false; // jika salah, set ke false
            }

            // Simpan jawaban user ke dalam database
            KomUserJawabanPertanyaan::create([
                'kom_user_id' => $userId,
                'pertanyaan_id' => $pertanyaanId,
                'jawaban_id' => $jawabanId,
                'jawaban_benar' => $isBenar,
            ]);
        }

        // Response status berdasarkan apakah semua jawaban benar atau tidak
        if ($semuaBenar) {
            // jika semua benar, return status 'benar'
            return response()->json([
                'status' => 'benar'
            ]);
        } else {
            // jika ada yang salah, return status 'salah'
            return response()->json([
                'status' => 'salah'
            ]);
        }
    }


    public function simpanProgressVideo(Request $request)
    {
        $user = auth('web')->user();

        if (!$user) {
            \Log::warning('User belum login saat simpan progres video');
            return response()->json(['error' => 'User belum login']);
        }

        $videoId = $request->input('video_id');

        if (!$videoId) {
            \Log::warning("Video ID kosong saat menyimpan progres video");
            return response()->json(['error' => 'Video ID tidak ditemukan']);
        }

        $userId = $user->id;

        KomUserVideoProgress::updateOrCreate(
            [
                'kom_user_id' => $userId,
                'kom_video_id' => $videoId,
            ],
            [
                'is_locked' => 0,
            ]
        );

        \Log::info("Progress video disimpan untuk user {$userId}, video {$videoId}");

        return response()->json(['success' => true]);
    }



    public function simpanProgresModul($modulId, $videoId)
    {
        $user = auth('web')->user();

        if (!$user) {
            \Log::warning('User belum login saat menyimpan progres modul');
            return; // Atau return response kalau dipanggil dari route
        }

        $userId = $user->id; // Kolom 'id', bukan 'kom_user_id' langsung

        // Cek apakah ada progres modul untuk user tersebut
        $progresModul = KomUserModulProgress::where('kom_user_id', $userId)
            ->where('kom_modul_id', $modulId)
            ->first();

        if ($progresModul) {
            $progresModul->update([
                'last_video_id' => $videoId,
                'video_terakhir' => now(),
            ]);

            \Log::info("Progress modul diupdate untuk user {$userId}, modul {$modulId}, video {$videoId}");
        } else {
            KomUserModulProgress::create([
                'kom_user_id' => $userId,
                'kom_modul_id' => $modulId,
                'last_video_id' => $videoId,
                'video_terakhir' => now(),
            ]);

            \Log::info("Progress modul dibuat untuk user {$userId}, modul {$modulId}, video {$videoId}");
        }
    }


    private function getProgresVideoUser()
    {
        $userId = auth('web')->user()->kom_user_id;

        // Ambil semua video ID yang sudah tidak terkunci (berarti sudah dibuka)
        $videoProgress = KomUserVideoProgress::where('kom_user_id', $userId)
            ->where('is_locked', 0) // hanya video yang sudah terbuka
            ->pluck('kom_video_id')
            ->toArray();

        // Periksa apakah progres video ada
        if (!$videoProgress) {
            \Log::warning("Tidak ada progres video untuk user {$userId}");
        }

        // Pastikan kembalikan array dengan key 'progres_video'
        return [
            'progres_video' => $videoProgress
        ];
    }





}
