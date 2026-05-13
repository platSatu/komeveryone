<?php

namespace App\Services\Superadmin;

use App\Models\Kmd\KmdBooks;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KomBookService
{
    public function getAll(array $filters = []): AbstractPaginator
{
    $query = KmdBooks::query();

    if (!empty($filters['search'])) {
        $search = $filters['search'];

        $query->where(function ($q) use ($search) {
            $q->where('no_books', 'like', "%{$search}%")
              ->orWhereHas('user', function ($q2) use ($search) {
                  $q2->where('email', 'like', "%{$search}%")
                     ->orWhere('name', 'like', "%{$search}%");
              });
        });
    }

    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }

    return $query->latest()->paginate(10)->withQueryString();
}


    public function find(string $id): KmdBooks
    {
        return KmdBooks::findOrFail($id);
    }

    public function store(array $data): KmdBooks
    {
        return KmdBooks::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $book = $this->find($id);
        return $book->update($data);
    }

    public function delete(string $id): bool
    {
        $book = $this->find($id);
        return $book->delete();
    }
}
