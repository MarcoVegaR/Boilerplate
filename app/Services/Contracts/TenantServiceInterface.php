<?php

namespace App\Services\Contracts;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface TenantServiceInterface
 * 
 * Service contract for Tenant entity.
 */
interface TenantServiceInterface extends EntityServiceInterface
{
    /**
     * Custom method example for domain-specific business logic.
     * This is just a template - add your domain logic methods here.
     *
     * @param string|int $key The ID of the Tenant
     * @param array $additionalData Any additional data needed for the operation
     * @return Model The Tenant model after processing
     */
    public function processBusinessLogic(string|int $key, array $additionalData = []): Model;
    
    // Add additional domain-specific methods as needed
}
