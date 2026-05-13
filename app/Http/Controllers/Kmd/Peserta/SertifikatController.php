<?php

namespace App\Http\Controllers\Kmd\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Models\Kmd\KmdCertificates;

class SertifikatController extends Controller
{
    // public function verifikasi($token)
    // {
    //     try {
    //         $id = Crypt::decryptString($token);

    //         $sertifikat = KmdCertificates::with('user')->where('id', $id)->firstOrFail();

    //         return view('a_kom_digital_pusat.peserta.verifikasi', [
    //             'certificate_number' => $sertifikat->no_certificate,
    //             'nama' => $sertifikat->user->name,
    //             'dibuatOleh' => 'Sub Divisi KOM DIGITAL',
    //             'tanggalPelaksanaan' => Carbon::parse($sertifikat->date_publish)->format('d-m-Y'),
    //             'tanggalCetak' => now()->format('d-m-Y'),
    //         ]);

    //     } catch (\Exception $e) {
    //         return abort(404, 'Sertifikat tidak ditemukan atau link tidak valid.');
    //     }
    // }
    public function verifikasi($id)
    {
        try {
            $sertifikat = KmdCertificates::with('user')->where('id', $id)->firstOrFail();

            return view('a_kom_digital_pusat.peserta.verifikasi', [
                'certificate_number' => $sertifikat->no_certificate,
                'nama' => $sertifikat->user->name,
                'dibuatOleh' => 'Sub Divisi KOM DIGITAL',
                'tanggalPelaksanaan' => Carbon::parse($sertifikat->date_publish)->format('d-m-Y'),
                'tanggalCetak' => now()->format('d-m-Y'),
            ]);
        } catch (\Exception $e) {
            return abort(404, 'Sertifikat tidak ditemukan atau link tidak valid.');
        }
    }

}
