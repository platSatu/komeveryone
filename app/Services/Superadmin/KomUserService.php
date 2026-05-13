<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KomUser;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class KomUserService
{
    /**
     * Mendapatkan semua pengguna dengan filter.
     *
     * @param array $filters
     * @return AbstractPaginator
     */
    public function getAll(array $filters = []): AbstractPaginator
    {
        $query = KomUser::with('cabang');

        // Search global
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%")
                    ->orWhere('phone', 'like', "%{$filters['search']}%");
            });
        }

        // Filter by role
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by cabang_id
        if (!empty($filters['cabang_id'])) {
            $query->where('cabang_id', $filters['cabang_id']);
        }

        if (!empty($filters['asal_gereja'])) {
            $query->where('asal_gereja', $filters['asal_gereja']);
        }

        return $query->latest()->paginate(10)->withQueryString();
    }


    /**
     * Mencari pengguna berdasarkan ID.
     *
     * @param string $id
     * @return KomUser 
     * @throws ModelNotFoundException
     */
    public function find(string $id): KomUser
    {
        return KomUser::findOrFail($id);
    }

    /**
     * Menyimpan pengguna baru.
     *
     * @param array $data
     * @return KomUser 
     */
    public function store(array $data): KomUser
    {
        try {
            $data['password'] = Hash::make($data['password']);
            $data['status'] = 'aktif';

            if (!isset($data['email_verification_sent_at'])) {
                $data['email_verification_sent_at'] = null; // atau now() sesuai kebutuhan
            }

            return KomUser::create($data);
        } catch (QueryException $e) {
            Log::error('Database error saat menyimpan KomUser ', [
                'message' => $e->getMessage(),
                'input' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error umum saat menyimpan KomUser ', [
                'message' => $e->getMessage(),
                'input' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Memperbarui pengguna yang ada.
     *
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update(string $id, array $data): bool
    {
        $user = $this->find($id);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $user->update($data);
    }

    /**
     * Menghapus pengguna berdasarkan ID.
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        $user = $this->find($id);
        return $user->delete();
    }


    public function import(array $usersData): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($usersData as $index => $data) {
            try {
                // Validasi minimal: email dan name wajib ada
                if (empty($data['email']) || empty($data['name'])) {
                    $results['failed']++;
                    $results['errors'][] = "Baris $index: Email dan Nama wajib diisi.";
                    continue;
                }

                // Cari user berdasarkan email
                $user = KomUser::where('email', $data['email'])->first();

                // Jika password ada dan tidak kosong, hash password
                if (!empty($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                } else {
                    unset($data['password']);
                }

                // Jika user sudah ada, update
                if ($user) {
                    $user->update($data);
                } else {
                    // Buat user baru
                    KomUser::create($data);
                }

                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Baris $index: " . $e->getMessage();
            }
        }

        return $results;
    }

}
