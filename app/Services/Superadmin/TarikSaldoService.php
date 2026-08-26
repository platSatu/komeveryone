<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdTarikSaldo;
use Illuminate\Pagination\AbstractPaginator;

class TarikSaldoService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        $query = KmdTarikSaldo::with(['user', 'cabang', 'donasi', 'userClassTutorial.classTutorial']);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['cabang_id'])) {
            $query->where('cabang_id', $filters['cabang_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['tanggal_penarikan'])) {
            $query->whereDate('tanggal_penarikan', $filters['tanggal_penarikan']);
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function find(string $id): KmdTarikSaldo
    {
        return KmdTarikSaldo::findOrFail($id);
    }

    public function store(array $data): KmdTarikSaldo
    {
        return KmdTarikSaldo::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $tarikSaldo = $this->find($id);
        return $tarikSaldo->update($data);
    }

    public function delete(string $id): bool
    {
        $tarikSaldo = $this->find($id);
        return $tarikSaldo->delete();
    }
}
