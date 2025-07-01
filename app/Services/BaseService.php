<?php

namespace App\Services;

use App\Repositories\Contracts\CrudRepositoryInterface;
use App\Services\Contracts\EntityServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

abstract class BaseService implements EntityServiceInterface
{
    /**
     * The repository instance.
     *
     * @var CrudRepositoryInterface
     */
    protected CrudRepositoryInterface $repository;

    /**
     * BaseService constructor.
     *
     * @param CrudRepositoryInterface $repository
     */
    public function __construct(CrudRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->filterAndPaginate($filters, $perPage);
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $with = []): Collection
    {
        return $this->repository->getAll($with);
    }

    /**
     * @inheritDoc
     */
    public function show(string|int $key, array $with = []): Model
    {
        return $this->repository->find($key, $with);
    }

    /**
     * @inheritDoc
     */
    public function store(array $data): Model
    {
        // Here you could add business logic, validations, events, etc.
        try {
            DB::beginTransaction();
            
            $model = $this->repository->create($data);
            
            // Additional logic or event dispatching could go here
            
            DB::commit();
            return $model;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function update(string|int $key, array $data): Model
    {
        // Here you could add business logic, validations, events, etc.
        try {
            DB::beginTransaction();
            
            $model = $this->repository->update($key, $data);
            
            // Additional logic or event dispatching could go here
            
            DB::commit();
            return $model;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function destroy(string|int $key, bool $force = false): bool
    {
        // Here you could add business logic, validations, events, etc.
        try {
            DB::beginTransaction();
            
            $result = $force ? 
                $this->repository->forceDelete($key) : 
                $this->repository->delete($key);
            
            // Additional logic or event dispatching could go here
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function restore(string|int $key): bool
    {
        try {
            DB::beginTransaction();
            
            $result = $this->repository->restore($key);
            
            // Additional logic or event dispatching could go here
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function filterBy(array $criteria, array $with = []): Collection
    {
        return $this->repository->filterBy($criteria, $with);
    }

    /**
     * @inheritDoc
     */
    public function filterAndPaginate(array $criteria, int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->repository->filterAndPaginate($criteria, $perPage, $with);
    }
    
    /**
     * @inheritDoc
     */
    public function search(string $term, array $columns = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->search($term, $columns, $perPage);
    }

    /**
     * @inheritDoc
     */
    public function searchAll(string $term, array $columns = []): Collection
    {
        return $this->repository->searchAll($term, $columns);
    }
    
    /**
     * Get paginated list with search and filters applied if provided.
     *
     * @param array $filters
     * @param string|null $search
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedList(array $filters = [], ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        if ($search) {
            return $this->search($search, $this->getSearchColumns(), $perPage);
        }
        
        return $this->list($filters, $perPage);
    }
    
    /**
     * Get columns that should be searched.
     *
     * @return array
     */
    protected function getSearchColumns(): array
    {
        return ['name', 'title', 'description'];
    }

    /**
     * @inheritDoc
     */
    public function count(array $criteria = []): int
    {
        return $this->repository->count($criteria);
    }

    /**
     * @inheritDoc
     */
    public function exists(array $criteria = []): bool
    {
        return $this->repository->exists($criteria);
    }

    /**
     * @inheritDoc
     */
    public function insertMany(array $data): bool
    {
        try {
            DB::beginTransaction();
            
            $result = $this->repository->insertMany($data);
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * @inheritDoc
     */
    public function findByUuid(string $uuid, array $with = []): ?Model
    {
        return $this->repository->findByUuid($uuid, $with);
    }

    /**
     * @inheritDoc
     */
    public function findOrCreate(array $attributes, array $values = []): Model
    {
        try {
            DB::beginTransaction();
            
            $model = $this->repository->firstOrCreate($attributes, $values);
            
            DB::commit();
            return $model;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        try {
            DB::beginTransaction();
            
            $model = $this->repository->updateOrCreate($attributes, $values);
            
            DB::commit();
            return $model;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
