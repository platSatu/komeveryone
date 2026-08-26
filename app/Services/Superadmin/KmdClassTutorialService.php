<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdClassTutorial;
use Illuminate\Pagination\AbstractPaginator;
use Carbon\Carbon;

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
        // Samakan dengan alur Admin Cabang: normalisasi start_time dari format
        // datetime-local ("Y-m-d\TH:i") ke format DATETIME MySQL agar tidak
        // gagal saat disimpan, dan pastikan field non-nullable punya default.
        if (!empty($data['start_time'])) {
            $data['start_time'] = Carbon::parse($data['start_time'], 'Asia/Jakarta')->format('Y-m-d H:i:s');
        }

        $data['jml_ikut'] = $data['jml_ikut'] ?? 0;
        $data['sisa_quota'] = $data['sisa_quota'] ?? ($data['quota'] ?? 0);
        $data['notifikasi'] = $data['notifikasi'] ?? false;

        return KmdClassTutorial::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $tutorial = $this->find($id);

        if (!empty($data['start_time'])) {
            $data['start_time'] = Carbon::parse($data['start_time'], 'Asia/Jakarta')->format('Y-m-d H:i:s');
        }

        $data['notifikasi'] = $data['notifikasi'] ?? false;
        $data['sisa_quota'] = $data['sisa_quota'] ?? $tutorial->sisa_quota;

        return $tutorial->update($data);
    }

    public function delete(string $id): bool
    {
        $tutorial = $this->find($id);
        return $tutorial->delete();
    }
}
