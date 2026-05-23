<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    abstract protected function modelClass(): string;

    protected function resolveModel(): Model
    {
        return app($this->modelClass());
    }

    // =========================================================================
    // Basic CRUD
    // =========================================================================

    public function find(int|string $id, array $relations = []): ?Model
    {
        return $this->newQuery()->with($relations)->find($id);
    }

    public function findOrFail(int|string $id, array $relations = []): Model
    {
        return $this->newQuery()->with($relations)->findOrFail($id);
    }

    public function findBy(string $column, mixed $value, array $relations = []): ?Model
    {
        return $this->newQuery()->with($relations)->where($column, $value)->first();
    }

    public function findAllBy(string $column, mixed $value, array $relations = []): Collection
    {
        return $this->newQuery()->with($relations)->where($column, $value)->get();
    }

    public function all(array $relations = []): Collection
    {
        return $this->newQuery()->with($relations)->get();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function forceDelete(Model $model): bool
    {
        return $model->forceDelete();
    }

    // =========================================================================
    // Query Helpers
    // =========================================================================

    public function newQuery(): Builder
    {
        return $this->model->newQuery();
    }

    public function paginate(int $perPage = 25, array $relations = [], ?string $orderBy = null, string $direction = 'desc'): LengthAwarePaginator
    {
        $query = $this->newQuery()->with($relations);
        if ($orderBy) {
            $query->orderBy($orderBy, $direction);
        }
        return $query->paginate($perPage);
    }

    public function count(array $criteria = []): int
    {
        $query = $this->newQuery();
        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }
        return $query->count();
    }

    public function exists(array $criteria): bool
    {
        $query = $this->newQuery();
        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }
        return $query->exists();
    }

    public function upsert(array $values, array|string $uniqueBy, array $update = []): int
    {
        return $this->model->upsert($values, (array) $uniqueBy, $update);
    }

    public function chunk(int $size, callable $callback): void
    {
        $this->newQuery()->chunk($size, $callback);
    }
}
