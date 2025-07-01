<?php

namespace App\Services\Admin;

use App\Models\Tenant;
use App\Events\TenantCreated;
use App\Jobs\ProvisionTenantDatabase;

use App\Models\Plan;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Services\BaseService;
use App\Services\Contracts\TenantServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantService extends BaseService implements TenantServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param TenantRepositoryInterface $repository
     */
    public function __construct(TenantRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Custom method example for domain-specific business logic.
     * This is just a template - add your domain logic methods here.
     *
     * @param string|int $key The ID of the Tenant
     * @param array $additionalData Any additional data needed for the operation
     * @return Model The Tenant model after processing
     */
    public function processBusinessLogic(string|int $key, array $additionalData = []): Model
    {
        try {
            DB::beginTransaction();
            
            // Get the model
            $model = $this->show($key);
            
            // Process domain-specific logic based on action type
            if (isset($additionalData['action'])) {
                switch ($additionalData['action']) {
                    case 'activate':
                        $model->is_active = true;
                        $model->save();
                        break;
                        
                    case 'deactivate':
                        $model->is_active = false;
                        $model->save();
                        break;
                        
                    case 'change_plan':
                        if (isset($additionalData['plan_id'])) {
                            $plan = Plan::findOrFail($additionalData['plan_id']);
                            $model->plan_id = $plan->id;
                            $model->save();
                        }
                        break;
                        
                    case 'extend_trial':
                        if (isset($additionalData['days'])) {
                            $days = intval($additionalData['days']);
                            $model->trial_ends_at = now()->addDays($days);
                            $model->save();
                        }
                        break;
                }
            }
            
            // Update the model with new data if provided
            if (!empty($additionalData['updateData'])) {
                $model = $this->update($key, $additionalData['updateData']);
            }
            
            DB::commit();
            return $model;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Create a new tenant with domain and database
     *
     * @param array $data Tenant data including domain
     * @param bool $shouldSeed Whether to seed the tenant database
     * @return Model The created tenant
     */
    public function createTenant(array $data, bool $shouldSeed = false): Model
    {
        try {
            DB::beginTransaction();
            
            // Generate database name if not provided
            if (!isset($data['database'])) {
                $data['database'] = 'tenant_' . Str::slug($data['name']) . '_' . Str::random(5);
            }
            
            // Create tenant
            $tenant = $this->store($data);
            
            // Domain is already set in the tenant data
            
            // Dispatch job to create database and run migrations
            event(new TenantCreated($tenant));
            ProvisionTenantDatabase::dispatch($tenant, $shouldSeed);
            
            DB::commit();
            return $tenant;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Override search functionality if needed with domain-specific logic.
     * This is optional - the base implementation is usually sufficient.
     *
     * @param string $term
     * @param array $columns
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    /*
    public function search(string $term, array $columns, int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        // Add any domain-specific search logic here
        return parent::search($term, $columns, $perPage);
    }
    */
}
