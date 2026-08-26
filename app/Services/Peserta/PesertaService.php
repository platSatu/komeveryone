<?php

namespace App\Services\Peserta;

use App\Models\Kmd\KmdPertanyaanUser;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KmdDonasi;
use App\Models\Kmd\KmdBooks;
use App\Models\Kmd\KmdProgressModul;
use App\Models\Kmd\KmdUserVideoProgress;
use App\Models\Kmd\KmdUserTutorialStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PesertaService
{
    /**
     * Get pertanyaan user dengan validasi keamanan
     */
    public function getPertanyaanUser($perPage = 10)
    {
        $userId = Auth::id();
        
        if (!$userId) {
            throw new \Exception('User not authenticated');
        }

        try {
            return KmdPertanyaanUser::with(['modul', 'class', 'rayon'])
                ->where('user_id', $userId) // Explicit user filter
                ->orderBy('tgl_submit', 'desc')
                ->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Error getting user pertanyaan', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }


    /**
 * Get pertanyaan by ID
 */
public function getPertanyaanById($id)
{
    return KmdPertanyaanUser::with(['modul', 'class'])
        ->where('user_id', Auth::id())
        ->find($id);
}

/**
 * Update pertanyaan
 */
public function updatePertanyaan($id, array $data)
    {
        Log::info('=== START PesertaService.updatePertanyaan ===', [
            'id' => $id,
            'data' => $data
        ]);

        try {
            $pertanyaan = KmdPertanyaanUser::findOrFail($id);
            
            Log::info('Found pertanyaan', [
                'pertanyaan_id' => $pertanyaan->id,
                'current_data' => $pertanyaan->toArray()
            ]);

            $pertanyaan->update($data);

            Log::info('Update completed', [
                'pertanyaan_id' => $pertanyaan->id,
                'updated_data' => $pertanyaan->fresh()->toArray()
            ]);

            return $pertanyaan;
        } catch (\Exception $e) {
            Log::error('=== ERROR in PesertaService.updatePertanyaan ===', [
                'id' => $id,
                'data' => $data,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);
            throw $e;
        }
    }
    /**
     * Get kelas tutorial user dengan relasi
     */
    public function getUserClassTutorial()
    {
        $userId = Auth::id();
        
        if (!$userId) {
            throw new \Exception('User not authenticated');
        }

        try {
            return KmdUserClassTutorial::with([
                'classTutorial.kelas',
                'classTutorial.modul', 
                'classTutorial.pengajar',
                'classTutorial.rayon',
                'classTutorial.classTutorial',
                
            ])
            ->where('user_id', $userId) // Explicit user filter
            ->orderBy('date_booking', 'desc')
            ->get();
        } catch (\Exception $e) {
            Log::error('Error getting user class tutorial', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get donasi user dengan validasi status
     */
    public function getDonasiUser($perPage = 10)
    {
        $userId = Auth::id();
        
        if (!$userId) {
            throw new \Exception('User not authenticated');
        }

        try {
            return KmdDonasi::with(['modul', 'cabang', 'userClassTutorial'])
                ->where('user_id', $userId) // Explicit user filter
                ->whereIn('status', ['paid', 'pending']) // Hanya donasi yang berhasil
                ->orderBy('transaction_time', 'desc')
                ->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Error getting user donasi', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get books user
     */
    public function getBooksUser($perPage = 10)
    {
        $userId = Auth::id();
        
        if (!$userId) {
            throw new \Exception('User not authenticated');
        }

        try {
            return KmdBooks::where('user_id', $userId) // Explicit user filter
                ->orderBy('date_publish', 'desc')
                ->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Error getting user books', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get progress modul user dengan relasi
     */
    public function getProgressModulUser()
    {
        $userId = Auth::id();
        
        if (!$userId) {
            throw new \Exception('User not authenticated');
        }

        try {
            return KmdProgressModul::with(['modul', 'kelas'])
                ->where('user_id', $userId) // Explicit user filter
                ->orderBy('created_at', 'desc') // Lebih baik pakai timestamp
                ->get();
        } catch (\Exception $e) {
            Log::error('Error getting user progress modul', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get video progress user
     */
    public function getUserVideoProgress()
    {
        $userId = Auth::id();
        
        if (!$userId) {
            throw new \Exception('User not authenticated');
        }

        try {
            return KmdUserVideoProgress::with(['video', 'modul'])
                ->where('user_id', $userId) // Explicit user filter
                ->orderBy('last_watched_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error getting user video progress', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get tutorial status user
     */
    public function getUserTutorialStatus()
    {
        $userId = Auth::id();
        
        if (!$userId) {
            throw new \Exception('User not authenticated');
        }

        try {
            return KmdUserTutorialStatus::with(['classTutorial', 'modul'])
                ->where('user_id', $userId) // Explicit user filter
                ->orderBy('updated_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error getting user tutorial status', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
