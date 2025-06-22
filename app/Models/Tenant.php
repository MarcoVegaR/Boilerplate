<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant;

class Tenant extends SpatieTenant
{
    use HasFactory, SoftDeletes;
    
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'landlord';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'domain',
        'database',
        'plan_id',
        'trial_ends_at',
        'subscription_ends_at',
        'is_active',
        'settings',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];
    
    /**
     * Get the plan associated with this tenant.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
    
    /**
     * Check if the tenant has a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        if (!$this->plan) {
            return false;
        }
        
        $features = $this->plan->features ?? [];
        
        return in_array($feature, $features);
    }
    
    /**
     * Check if the tenant is on trial.
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && now()->lt($this->trial_ends_at);
    }
    
    /**
     * Check if the tenant's subscription is active.
     */
    public function subscriptionActive(): bool
    {
        if (!$this->subscription_ends_at) {
            return false;
        }
        
        return now()->lt($this->subscription_ends_at);
    }
}
