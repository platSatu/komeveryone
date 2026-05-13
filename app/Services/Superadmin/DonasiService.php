<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdDonasi;
use Illuminate\Pagination\AbstractPaginator;

class DonasiService
{
    public function getAll(array $filters = []): AbstractPaginator
{
    $query = KmdDonasi::with(['user', 'userClassTutorial', 'modul', 'kelas', 'cabang']);

    if (!empty($filters['user_id'])) {
        $query->where('user_id', $filters['user_id']);
    }

    if (!empty($filters['modul_id'])) {
        $query->where('modul_id', $filters['modul_id']);
    }

    if (!empty($filters['class_id'])) {
        $query->where('class_id', $filters['class_id']);
    }

    if (!empty($filters['cabang_id'])) {
        $query->where('cabang_id', $filters['cabang_id']);
    }

    if (!empty($filters['transaction_status'])) {
        $query->where('transaction_status', $filters['transaction_status']);
    }

    // ✅ SEARCH: Cari berdasarkan nama user, modul, kelas, atau cabang
    if (!empty($filters['search'])) {
        $search = $filters['search'];
        $query->where(function ($q) use ($search) {
            $q->whereHas('user', fn($q2) => $q2->where('name', 'like', "%$search%"))
              ->orWhereHas('modul', fn($q2) => $q2->where('name', 'like', "%$search%"))
              ->orWhereHas('kelas', fn($q2) => $q2->where('name', 'like', "%$search%"))
              ->orWhereHas('cabang', fn($q2) => $q2->where('name', 'like', "%$search%"));
        });
    }

    return $query->latest()->paginate(10)->withQueryString();
}





    public function find(string $id): KmdDonasi
    {
        return KmdDonasi::findOrFail($id);
    }

    public function store(array $data): KmdDonasi
    {
        if (empty($data['order_id'])) {
        $data['order_id'] = uniqid('order_');
        }
        return KmdDonasi::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $donasi = $this->find($id);
        return $donasi->update($data);
    }

    public function delete(string $id): bool
    {
        $donasi = $this->find($id);
        return $donasi->delete();
    }
}
