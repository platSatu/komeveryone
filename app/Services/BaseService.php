<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseService
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();
        
        $query = $this->applyFilters($query, $filters);
        
        return $query->paginate($perPage);
    }

    public function getAllWithoutPagination(array $filters = []): Collection
    {
        $query = $this->model->newQuery();
        
        $query = $this->applyFilters($query, $filters);
        
        return $query->get();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function findById(string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function findByIdOrNull(string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function update(string $id, array $data): Model
    {
        $model = $this->findById($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(string $id): bool
    {
        $model = $this->findById($id);
        return $model->delete();
    }

    public function forceDelete(string $id): bool
    {
        $model = $this->findById($id);
        return $model->forceDelete();
    }

    public function restore(string $id): bool
    {
        $model = $this->model->withTrashed()->findOrFail($id);
        return $model->restore();
    }

    public function exists(string $id): bool
    {
        return $this->model->where($this->model->getKeyName(), $id)->exists();
    }

    abstract protected function applyFilters($query, array $filters);
}
