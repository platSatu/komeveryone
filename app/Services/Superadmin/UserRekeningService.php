<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdUserRekening;
use Illuminate\Pagination\AbstractPaginator;

class UserRekeningService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        $query = KmdUserRekening::with(['user', 'cabang']);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['cabang_id'])) {
            $query->where('cabang_id', $filters['cabang_id']);
        }

        if (!empty($filters['nama_rek'])) {
            $query->where('nama_rek', 'like', '%' . $filters['nama_rek'] . '%');
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function find(string $id): KmdUserRekening
    {
        return KmdUserRekening::findOrFail($id);
    }

    public function store(array $data): KmdUserRekening
    {
        return KmdUserRekening::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $rekening = $this->find($id);
        $rekening->fill($data);

        // Nomor / bank / nama berubah → wajib verifikasi ulang ke Bizbos sebelum bisa menerima dana
        if ($rekening->isDirty(['no_rek', 'nama_bank', 'nama_rek'])) {
            $rekening->forceFill(array_fill_keys(KmdUserRekening::KOLOM_VERIFIKASI, null));
        }

        return $rekening->save();
    }

    public function delete(string $id): bool
    {
        $rekening = $this->find($id);
        return $rekening->delete();
    }
}
