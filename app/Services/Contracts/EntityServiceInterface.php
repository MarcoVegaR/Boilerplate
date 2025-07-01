<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * EntityServiceInterface - Define el contrato base para los servicios de entidades
 * 
 * Nomenclatura de métodos:
 * - La capa de servicio usa convenciones comunes de API de Laravel (store, show, etc)
 * - La correspondencia con los métodos de CrudRepositoryInterface es:
 *   · list() -> paginate() en repositorio
 *   · show() -> find() en repositorio
 *   · store() -> create() en repositorio
 *   · destroy() -> delete() en repositorio
 *   · findOrCreate() -> firstOrCreate() en repositorio
 *   · search() -> searchAndPaginate() en repositorio
 *   · searchAll() -> search() en repositorio
 */
interface EntityServiceInterface
{
    /**
     * List all resources or filtered by criteria.
     * [Corresponde a paginate() en CrudRepositoryInterface]
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get all resources.
     * [Corresponde a getAll() en CrudRepositoryInterface]
     *
     * @param array $with Relationships to eager load
     * @return Collection
     */
    public function getAll(array $with = []): Collection;
    
    /**
     * Show a specific resource.
     * [Corresponde a find() en CrudRepositoryInterface]
     *
     * @param string|int $key Primary key (supports both integer and UUID/string keys)
     * @param array $with Relationships to eager load
     * @return Model
     */
    public function show(string|int $key, array $with = []): Model;
    
    /**
     * Create a new resource.
     * [Corresponde a create() en CrudRepositoryInterface]
     *
     * @param array $data
     * @return Model
     */
    public function store(array $data): Model;
    
    /**
     * Update a resource.
     * [Corresponde a update() en CrudRepositoryInterface]
     *
     * @param string|int $key Primary key (supports both integer and UUID/string keys)
     * @param array $data
     * @return Model
     */
    public function update(string|int $key, array $data): Model;
    
    /**
     * Delete a resource.
     * [Corresponde a delete()/forceDelete() en CrudRepositoryInterface]
     *
     * @param string|int $key Primary key (supports both integer and UUID/string keys)
     * @param bool $force Whether to force delete the resource
     * @return bool
     */
    public function destroy(string|int $key, bool $force = false): bool;
    
    /**
     * Restore a soft-deleted resource.
     * [Corresponde a restore() en CrudRepositoryInterface]
     *
     * @param string|int $key Primary key (supports both integer and UUID/string keys)
     * @return bool
     */
    public function restore(string|int $key): bool;
    
    /**
     * Filter records by criteria.
     * [Corresponde a filterBy() en CrudRepositoryInterface]
     *
     * @param array $criteria
     * @param array $with Relationships to eager load
     * @return Collection
     */
    public function filterBy(array $criteria, array $with = []): Collection;
    
    /**
     * Filter and paginate records.
     * [Corresponde a filterAndPaginate() en CrudRepositoryInterface]
     *
     * @param array $criteria
     * @param int $perPage
     * @param array $with Relationships to eager load
     * @return LengthAwarePaginator
     */
    public function filterAndPaginate(array $criteria, int $perPage = 15, array $with = []): LengthAwarePaginator;
    
    /**
     * Search for resources based on a search term.
     * [Corresponde a searchAndPaginate() en CrudRepositoryInterface]
     *
     * @param string $term Search term
     * @param array $columns Columns to search in
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $term, array $columns, int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Search for resources and return collection (no pagination).
     * [Corresponde a search() en CrudRepositoryInterface]
     *
     * @param string $term Search term
     * @param array $columns Columns to search in
     * @param array $with Relationships to eager load
     * @return Collection
     */
    public function searchAll(string $term, array $columns, array $with = []): Collection;
    
    /**
     * Find or create a resource.
     * [Corresponde a firstOrCreate() en CrudRepositoryInterface]
     *
     * @param array $attributes
     * @param array $values
     * @return Model
     */
    public function findOrCreate(array $attributes, array $values = []): Model;
    
    /**
     * Update an existing resource or create a new one if none exists.
     * [Corresponde a updateOrCreate() en CrudRepositoryInterface]
     *
     * @param array $attributes
     * @param array $values
     * @return Model
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;
    
    /**
     * Count resources matching given criteria.
     * [Corresponde a count() en CrudRepositoryInterface]
     *
     * @param array $criteria
     * @return int
     */
    public function count(array $criteria = []): int;
    
    /**
     * Check if any resources exist matching given criteria.
     * [Corresponde a exists() en CrudRepositoryInterface]
     *
     * @param array $criteria
     * @return bool
     */
    public function exists(array $criteria = []): bool;
    
    /**
     * Insert multiple records in a single operation.
     * [Corresponde a insertMany() en CrudRepositoryInterface]
     *
     * @param array $data Array of data arrays to insert
     * @return bool
     */
    public function insertMany(array $data): bool;
    
    /**
     * Find a resource by UUID.
     * [Corresponde a findByUuid() en CrudRepositoryInterface]
     *
     * @param string $uuid
     * @param array $with
     * @return Model|null
     */
    public function findByUuid(string $uuid, array $with = []): ?Model;
}
