<?php

namespace App\Repositories\Admin;

use App\Models\Tenant;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    /**
     * TenantRepository constructor.
     *
     * @param Tenant $model
     */
    public function __construct(Tenant $model)
    {
        parent::__construct($model);
    }

    /**
     * Define searchable columns for this model.
     * Override this method to customize search behavior.
     *
     * @return array
     */
    protected function getSearchableColumns(): array
    {
        return [
            'name',
            'domain',
            'database'
        ];
    }

    /**
     * Find by a specific field.
     *
     * @param string $field
     * @param mixed $value
     * @param array $with
     * @return Model|null
     */
    public function findBy(string $field, mixed $value, array $with = []): ?Model
    {
        return $this->model->with($with)->where($field, $value)->first();
    }

    /**
     * Apply domain-specific filtering logic.
     * This method can be extended to add custom filtering for this specific model.
     *
     * @param array $criteria
     * @param array $with
     * @return Collection
     */
    public function applyDomainFilters(array $criteria, array $with = []): Collection
    {
        // Start with base filtering logic
        $query = $this->getFilterQuery($criteria, $with);
        
        // Filter by active status if specified
        if (isset($criteria['status'])) {
            if ($criteria['status'] === 'active') {
                $query->where('is_active', true);
            } else if ($criteria['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Filter by plan if specified
        if (isset($criteria['plan_id'])) {
            $query->where('plan_id', $criteria['plan_id']);
        }
        
        // Filter by trial status
        if (isset($criteria['trial'])) {
            if ($criteria['trial'] === 'active') {
                $query->whereNotNull('trial_ends_at')
                      ->where('trial_ends_at', '>', now());
            } else if ($criteria['trial'] === 'expired') {
                $query->where(function($q) {
                    $q->whereNotNull('trial_ends_at')
                      ->where('trial_ends_at', '<=', now());
                });
            } else if ($criteria['trial'] === 'none') {
                $query->whereNull('trial_ends_at');
            }
        }
        
        return $query->get();
    }
}
