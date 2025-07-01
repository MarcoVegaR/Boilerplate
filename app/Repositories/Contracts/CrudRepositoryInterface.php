<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface CrudRepositoryInterface
{
    /* ==== BÁSICO ==== */

    /**
     * Get all records.
     *
     * @param array $with Relationships to eager load
     * @return Collection
     */
    public function getAll(array $with = []): Collection;

    /**
     * Find a record by its primary key.
     *
     * @param string|int $key Primary key (supports both integer and UUID/string keys)
     * @param array $with Relationships to eager load
     * @return Model
     */
    public function find(string|int $key, array $with = []): Model;

    /**
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update an existing record.
     *
     * @param string|int $key Primary key (supports both integer and UUID/string keys)
     * @param array $data
     * @return Model
     */
    public function update(string|int $key, array $data): Model;

    /**
     * Delete a record.
     *
     * @param string|int $key Primary key (supports both integer and UUID/string keys)
     * @return bool
     */
    public function delete(string|int $key): bool;

    /**
     * Get paginated records.
     *
     * @param int $perPage
     * @param array $with Relationships to eager load
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $with = []): LengthAwarePaginator;

    /* ==== VARIANTES DE PK ==== */

    /**
     * Find a record by its UUID.
     *
     * @param string $uuid
     * @param array $with Relationships to eager load
     * @return Model|null
     */
    public function findByUuid(string $uuid, array $with = []): ?Model;

    /* ==== SOFT DELETE ==== */

    /**
     * Restore a soft-deleted record.
     *
     * @param string|int $key Primary key (supports both integer and UUID/string keys)
     * @return bool
     */
    public function restore(string|int $key): bool;

    /**
     * Permanently delete a soft-deleted record.
     *
     * @param string|int $key Primary key (supports both integer and UUID/string keys)
     * @return bool
     */
    public function forceDelete(string|int $key): bool;

    /* ==== CONSULTAS DINÁMICAS ==== */

    /**
     * Filter records by criteria.
     *
     * @param array $criteria
     * @param array $with Relationships to eager load
     * @return Collection
     */
    public function filterBy(array $criteria, array $with = []): Collection;

    /**
     * Filter and paginate records.
     *
     * @param array $criteria
     * @param int $perPage
     * @param array $with Relationships to eager load
     * @return LengthAwarePaginator
     */
    public function filterAndPaginate(array $criteria, int $perPage = 15, array $with = []): LengthAwarePaginator;

    /* ==== UTILIDAD ==== */

    /**
     * Get the first record matching the attributes or create it.
     *
     * @param array $attributes
     * @param array $values
     * @return Model
     */
    public function firstOrCreate(array $attributes, array $values = []): Model;

    /**
     * Update a record with the given attributes, or create it if no matching record exists.
     *
     * @param array $attributes
     * @param array $values
     * @return Model
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;

    /**
     * Count records matching the given criteria.
     *
     * @param array $criteria
     * @return int
     */
    public function count(array $criteria = []): int;

    /**
     * Check if any records matching the given criteria exist.
     *
     * @param array $criteria
     * @return bool
     */
    public function exists(array $criteria = []): bool;

    /**
     * Search records by a term across specified columns.
     *
     * @param string $term Search term
     * @param array $columns Columns to search in
     * @param array $with Relationships to eager load
     * @return Collection
     */
    public function search(string $term, array $columns, array $with = []): Collection;

    /**
     * Search and paginate records by a term across specified columns.
     *
     * @param string $term Search term
     * @param array $columns Columns to search in
     * @param int $perPage
     * @param array $with Relationships to eager load
     * @return LengthAwarePaginator
     */
    public function searchAndPaginate(string $term, array $columns, int $perPage = 15, array $with = []): LengthAwarePaginator;

    /**
     * Insert multiple records in a single query.
     *
     * @param array $data
     * @return bool
     */
    public function insertMany(array $data): bool;
}
