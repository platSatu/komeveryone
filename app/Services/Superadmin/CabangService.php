<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\Cabang;
use Illuminate\Pagination\AbstractPaginator;

class CabangService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        $query = Cabang::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function find(string $id): Cabang
    {
        return Cabang::findOrFail($id);
    }

    public function store(array $data): Cabang
    {
        return Cabang::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $cabang = $this->find($id);
        return $cabang->update($data);
    }

    public function delete(string $id): bool
    {
        $cabang = $this->find($id);
        return $cabang->delete();
    }
}
