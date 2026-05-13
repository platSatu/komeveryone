<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdProgressModul;
use Illuminate\Pagination\AbstractPaginator;

class KmdProgressModulService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        $query = KmdProgressModul::with(['user', 'modul']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function find(string $id): KmdProgressModul
    {
        return KmdProgressModul::findOrFail($id);
    }

    public function store(array $data): KmdProgressModul
    {
        return KmdProgressModul::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $modul = $this->find($id);
        return $modul->update($data);
    }

    public function delete(string $id): bool
    {
        $modul = $this->find($id);
        return $modul->delete();
    }
}
