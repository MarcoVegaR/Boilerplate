<?php

namespace App\Repositories\Contracts;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface TenantRepositoryInterface
 * 
 * Repository contract for Tenant entity.
 */
interface TenantRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * Find by a specific field.
     *
     * @param string $field
     * @param mixed $value
     * @param array $with
     * @return Model|null
     */
    public function findBy(string $field, mixed $value, array $with = []): ?Model;

    /**
     * Apply domain-specific filtering logic.
     * This method can be extended to add custom filtering for this specific model.
     *
     * @param array $criteria
     * @param array $with
     * @return Collection
     */
    public function applyDomainFilters(array $criteria, array $with = []): Collection;
}
