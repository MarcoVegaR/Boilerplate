<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements CrudRepositoryInterface
{
    /**
     * The model instance.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * UUID column name.
     *
     * @var string
     */
    protected string $uuidColumn = 'uuid';

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /* ==== BÁSICO ==== */

    /**
     * @inheritDoc
     */
    public function getAll(array $with = []): Collection
    {
        return $this->model->with($with)->get();
    }

    /**
     * @inheritDoc
     */
    public function find(string|int $key, array $with = []): Model
    {
        return $this->model->with($with)->findOrFail($key);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @inheritDoc
     */
    public function update(string|int $key, array $data): Model
    {
        $model = $this->find($key);
        $model->update($data);
        
        return $model->fresh();
    }

    /**
     * @inheritDoc
     */
    public function delete(string|int $key): bool
    {
        return $this->find($key)->delete();
    }

    /**
     * @inheritDoc
     */
    public function paginate(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->model->with($with)->paginate($perPage);
    }

    /* ==== VARIANTES DE PK ==== */
    
    /**
     * @inheritDoc
     */
    public function findByUuid(string $uuid, array $with = []): ?Model
    {
        try {
            return $this->model->with($with)->where($this->uuidColumn, $uuid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /* ==== SOFT DELETE ==== */

    /**
     * @inheritDoc
     */
    public function restore(string|int $key): bool
    {
        // Check if model uses SoftDeletes trait
        if (!method_exists($this->model, 'restore')) {
            return false;
        }
        
        $model = $this->model->withTrashed()->findOrFail($key);
        return $model->restore();
    }

    /**
     * @inheritDoc
     */
    public function forceDelete(string|int $key): bool
    {
        // Check if model uses SoftDeletes trait
        if (!method_exists($this->model, 'forceDelete')) {
            return false;
        }
        
        $model = $this->model->withTrashed()->findOrFail($key);
        return $model->forceDelete();
    }

    /* ==== CONSULTAS DINÁMICAS ==== */

    /**
     * @inheritDoc
     */
    public function filterBy(array $criteria, array $with = []): Collection
    {
        $query = $this->model->with($with);
        
        foreach ($criteria as $column => $value) {
            if (is_array($value)) {
                $operator = $value[0] ?? '=';
                $val = $value[1] ?? null;
                
                if (isset($value[0]) && isset($value[1])) {
                    $query->where($column, $operator, $val);
                }
            } else {
                $query->where($column, $value);
            }
        }
        
        return $query->get();
    }

    /**
     * @inheritDoc
     */
    public function filterAndPaginate(array $criteria, int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        $query = $this->model->with($with);
        
        foreach ($criteria as $column => $value) {
            if (is_array($value)) {
                $operator = $value[0] ?? '=';
                $val = $value[1] ?? null;
                
                if (isset($value[0]) && isset($value[1])) {
                    $query->where($column, $operator, $val);
                }
            } else {
                $query->where($column, $value);
            }
        }
        
        return $query->paginate($perPage);
    }

    /* ==== UTILIDAD ==== */

    /**
     * @inheritDoc
     */
    public function firstOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model->firstOrCreate($attributes, $values);
    }

    /**
     * @inheritDoc
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * @inheritDoc
     */
    public function count(array $criteria = []): int
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $column => $value) {
            if (is_array($value)) {
                $operator = $value[0] ?? '=';
                $val = $value[1] ?? null;
                
                if (isset($value[0]) && isset($value[1])) {
                    $query->where($column, $operator, $val);
                }
            } else {
                $query->where($column, $value);
            }
        }
        
        return $query->count();
    }

    /**
     * @inheritDoc
     */
    public function exists(array $criteria = []): bool
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $column => $value) {
            if (is_array($value)) {
                $operator = $value[0] ?? '=';
                $val = $value[1] ?? null;
                
                if (isset($value[0]) && isset($value[1])) {
                    $query->where($column, $operator, $val);
                }
            } else {
                $query->where($column, $value);
            }
        }
        
        return $query->exists();
    }

    /**
     * @inheritDoc
     */
    public function search(string $term, array $columns, array $with = []): Collection
    {
        $query = $this->model->with($with);
        
        return $query->where(function($q) use ($term, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', "%{$term}%");
            }
        })->get();
    }

    /**
     * @inheritDoc
     */
    public function searchAndPaginate(string $term, array $columns, int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        $query = $this->model->with($with);
        
        return $query->where(function($q) use ($term, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', "%{$term}%");
            }
        })->paginate($perPage);
    }

    /**
     * @inheritDoc
     */
    public function insertMany(array $data): bool
    {
        return $this->model->insert($data);
    }
}
