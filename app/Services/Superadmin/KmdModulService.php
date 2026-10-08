<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdModuls;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class KmdModulService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        // kom_v02: hanya modul dalam bahasa yang sedang dikelola
        $query = KmdModuls::with('kelas')->where('language_code', \App\Services\Kmd\BahasaKelolaService::current());

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function find(string $id): KmdModuls
    {
        return KmdModuls::findOrFail($id);
    }

    public function store(array $data): KmdModuls
    {
        $data['language_code'] = $data['language_code'] ?? \App\Services\Kmd\BahasaKelolaService::current(); // kom_v02

        return KmdModuls::create($data);
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
