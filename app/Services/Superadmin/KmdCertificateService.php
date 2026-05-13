<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdCertificates;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KmdCertificateService
{
    public function getAll(array $filters = []): AbstractPaginator
    {
        $query = KmdCertificates::with('user');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('no_certificate', 'like', "%{$filters['search']}%")
                  ->orWhereHas('user', function ($q2) use ($filters) {
                      $q2->where('name', 'like', "%{$filters['search']}%");
                  });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function find(string $id): KmdCertificates
    {
        return KmdCertificates::findOrFail($id);
    }

    public function store(array $data): KmdCertificates
    {
        return KmdCertificates::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $certificate = $this->find($id);
        return $certificate->update($data);
    }

    public function delete(string $id): bool
    {
        $certificate = $this->find($id);
        return $certificate->delete();
    }
}
