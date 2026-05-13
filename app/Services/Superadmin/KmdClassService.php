<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KmdClassService
{
    protected KmdClass $model;

    public function __construct(KmdClass $model)
    {
        $this->model = $model;
    }

    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            $query = $this->model->newQuery();
            $query = $this->applyFilters($query, $filters);
            
            return $query->latest()->paginate($perPage);
            
        } catch (\Exception $e) {
            Log::error('Error fetching classes: ' . $e->getMessage());
            throw $e;
        }
    }

    public function create(array $data): KmdClass
    {
        try {
            return DB::transaction(function () use ($data) {
                $class = $this->model->create($data);
                
                Log::info('Class created successfully', [
                    'id' => $class->id, 
                    'name' => $class->name
                ]);
                
                return $class;
            });
        } catch (\Exception $e) {
            Log::error('Error creating class: ' . $e->getMessage(), ['data' => $data]);
            throw $e;
        }
    }

    public function show(string $encryptedId): KmdClass
    {
        try {
            // Log untuk debugging
            Log::info('Attempting to show class', ['encrypted_id' => $encryptedId]);
            
            // Coba decrypt ID terlebih dahulu
            $realId = KmdClass::decryptId($encryptedId);
            Log::info('Decrypted ID', ['real_id' => $realId]);
            
            // Cari data berdasarkan real ID
            $class = KmdClass::findOrFail($realId);
            
            Log::info('Class found successfully', [
                'id' => $class->id,
                'name' => $class->name
            ]);
            
            return $class;
            
        } catch (\InvalidArgumentException $e) {
            // Error saat decrypt
            Log::error('Invalid encrypted ID', [
                'encrypted_id' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('ID tidak valid');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Data tidak ditemukan
            Log::error('Class not found in database', [
                'encrypted_id' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Data kelas tidak ditemukan');
            
        } catch (\Exception $e) {
            // Error lainnya
            Log::error('Unexpected error in show class', [
                'encrypted_id' => $encryptedId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Terjadi kesalahan saat mengambil data');
        }
    }

    public function update(string $encryptedId, array $data): KmdClass
    {
        try {
            return DB::transaction(function () use ($encryptedId, $data) {
                // Gunakan method show untuk mendapatkan data
                $class = $this->show($encryptedId);
                
                $class->update($data);
                
                Log::info('Class updated successfully', [
                    'id' => $class->id, 
                    'name' => $class->name,
                    'updated_data' => $data
                ]);
                
                return $class->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Error updating class: ' . $e->getMessage(), [
                'encrypted_id' => $encryptedId,
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function delete(string $encryptedId): bool
    {
        try {
            return DB::transaction(function () use ($encryptedId) {
                // Gunakan method show untuk mendapatkan data
                $class = $this->show($encryptedId);
                
                $className = $class->name;
                $classId = $class->id;
                
                $result = $class->delete();
                
                Log::info('Class deleted successfully', [
                    'id' => $classId,
                    'name' => $className
                ]);
                
                return $result;
            });
        } catch (\Exception $e) {
            Log::error('Error deleting class: ' . $e->getMessage(), [
                'encrypted_id' => $encryptedId
            ]);
            throw $e;
        }
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $status = filter_var($filters['status'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($status !== null) {
                $query->byStatus($status);
            }
        }

        return $query;
    }
}
