<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdClassTutorial;
use Illuminate\Pagination\AbstractPaginator;

class KmdClassTutorialService
{


    public function getAll(array $filters = []): AbstractPaginator
    {
        $query = KmdClassTutorial::with(['user', 'pengajar', 'kelas', 'modul']);

        // Filter by rayon_id jika ada
        if (!empty($filters['rayon_id'])) {
            $query->where('rayon_id', $filters['rayon_id']);
        }

        // Pencarian nama user
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        return $query->latest()->paginate(10)->withQueryString();
    }


    public function find(string $id): KmdClassTutorial
    {
        return KmdClassTutorial::with(['user', 'pengajar', 'kelas', 'modul'])->findOrFail($id);
    }

    public function store(array $data): KmdClassTutorial
    {
        return KmdClassTutorial::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $tutorial = $this->find($id);
        return $tutorial->update($data);
    }

    public function delete(string $id): bool
    {
        $tutorial = $this->find($id);
        return $tutorial->delete();
    }
}
