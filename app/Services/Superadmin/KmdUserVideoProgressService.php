<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdUserVideoProgress;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Auth;

class KmdUserVideoProgressService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        $query = KmdUserVideoProgress::with(['user', 'video']);

        // Optional filter logic here

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function find(string $id): KmdUserVideoProgress
    {
        return KmdUserVideoProgress::findOrFail($id);
    }

    public function store(array $data): KmdUserVideoProgress
    {
        $data['user_id'] = Auth::id();
        return KmdUserVideoProgress::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $progress = $this->find($id);
        return $progress->update($data);
    }

    public function delete(string $id): bool
    {
        $progress = $this->find($id);
        return $progress->delete();
    }
}
