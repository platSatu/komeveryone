<?php

namespace App\Services\AdminCabang;

use App\Models\Kmd\KomUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Kmd\KmdBooks;
use App\Models\Kmd\KmdDonasi;
use App\Models\Kmd\KmdPertanyaanUser;
use App\Models\Kmd\KmdProgressModul;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KmdUserModul;
use App\Models\Kmd\KmdUserQuestions;
use App\Models\Kmd\KmdUserReview;
use App\Models\Kmd\KmdUserTutorialStatus;
use App\Models\Kmd\KmdUserVideoProgress;

class UserCabangService
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user(); // diasumsikan user sudah login
    }

    /**
     * Get all users for the current admin cabang
     */
    public function all(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $user = $this->user ?? auth()->user();

        if (!$user) {
            // Jika user tidak ditemukan, kembalikan data kosong
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
        }

        $query = KomUser::query();

        // Selalu exclude superadmin
        $query->where('role', '!=', 'superadmin');

        // Jika user role admin_cabang, filter berdasarkan cabang_id user yang login
        if ($user->role === 'admin_cabang') {
            if (empty($user->cabang_id)) {
                // Jika cabang_id kosong, kembalikan data kosong
                return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
            }
            // Filter user lain yang memiliki cabang_id sama dengan user yang login
            $query->where('cabang_id', $user->cabang_id);
        }

        // Filter pencarian
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter role
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Urutkan berdasarkan user terbaru (created_at desc)
        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }


    /**
     * Find user by ID and ensure it's in the same cabang
     */
    public function find(string $id)
    {
        return KomUser::findOrFail($id);
    }

    /**
     * Create new user in the same cabang
     */
    public function create(array $data)
    {
        $data['cabang_id'] = $this->user->cabang_id;
        $data['password'] = Hash::make($data['password']);
        $data['status'] = $data['status'] ?? 'aktif';
        $data['email_verification_sent_at'] = now();

        return KomUser::create($data);
    }

    /**
     * Update existing user in the same cabang
     */
    public function update(string $id, array $data)
    {
        $user = $this->find($id);

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user;
    }

    /**
     * Delete user in the same cabang
     */
    public function delete(string $id)
    {
        $user = $this->find($id); // pastikan find mengembalikan model
        return $user->delete();
    }

    public function getUserDetails(string $userId): array
    {
        return [
            'books' => KmdBooks::where('user_id', $userId)->get(),
            'donasi' => KmdDonasi::where('user_id', $userId)->get(),
            'pertanyaan_user' => KmdPertanyaanUser::where('user_id', $userId)->get(),
            'progress_modul' => KmdProgressModul::with('modul')->where('user_id', $userId)->get(),
            'user_class_tutorial' => KmdUserClassTutorial::where('user_id', $userId)->get(),
            'user_modul' => KmdUserModul::where('user_id', $userId)->get(),
            'user_questions' => KmdUserQuestions::where('user_id', $userId)->get(),
            'user_review' => KmdUserReview::where('user_id', $userId)->get(),
            'user_tutorial_status' => KmdUserTutorialStatus::where('user_id', $userId)->get(),
            'user_video_progress' => KmdUserVideoProgress::where('user_id', $userId)->get(),
        ];
    }

}
