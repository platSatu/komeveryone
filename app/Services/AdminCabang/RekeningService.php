<?php

namespace App\Services\AdminCabang;

use App\Models\Kmd\KmdUserRekening;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RekeningService
{
    /**
     * Get rekening user berdasarkan user_id
     */
    public function getUserRekening(string $userId)
    {
        try {
            return KmdUserRekening::with(['user:id,name,email', 'cabang:id,name'])
                ->where('user_id', $userId)
                ->first();
        } catch (\Exception $e) {
            Log::error('Error getting user rekening', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create rekening baru
     */
    public function createRekening(array $data)
    {
        try {
            return KmdUserRekening::create([
                'id' => Str::uuid(),
                'user_id' => $data['user_id'],
                'cabang_id' => $data['cabang_id'],
                'nama_rek' => $data['nama_rek'],
                'no_rek' => $data['no_rek'],
                'nama_bank' => $data['nama_bank'],
                'cabang_bank' => $data['cabang_bank']
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating rekening', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get list bank populer di Indonesia
     */
    public function getListBank()
    {
        return [
            'BCA' => 'Bank Central Asia (BCA)',
            'BRI' => 'Bank Rakyat Indonesia (BRI)',
            'BNI' => 'Bank Negara Indonesia (BNI)',
            'Mandiri' => 'Bank Mandiri',
            'CIMB' => 'CIMB Niaga',
            'Danamon' => 'Bank Danamon',
            'Permata' => 'Bank Permata',
            'OCBC' => 'OCBC NISP',
            'Maybank' => 'Maybank Indonesia',
            'BTPN' => 'Bank BTPN',
            'Jenius' => 'Jenius (BTPN)',
            'BSI' => 'Bank Syariah Indonesia (BSI)',
            'Muamalat' => 'Bank Muamalat',
            'BCA Syariah' => 'BCA Syariah',
            'BRI Syariah' => 'BRI Syariah',
            'BNI Syariah' => 'BNI Syariah',
            'Mandiri Syariah' => 'Mandiri Syariah',
            'Mega' => 'Bank Mega',
            'Bukopin' => 'Bank Bukopin',
            'HSBC' => 'HSBC Indonesia',
            'Citibank' => 'Citibank',
            'Standard Chartered' => 'Standard Chartered',
            'UOB' => 'United Overseas Bank (UOB)',
            'Panin' => 'Bank Panin',
            'Sinarmas' => 'Bank Sinarmas',
            'Lainnya' => 'Bank Lainnya'
        ];
    }
}
