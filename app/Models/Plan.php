<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
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
        'slug',
        'description',
        'price',
        'billing_period',
        'is_active',
        'is_featured',
        'trial_days',
        'features',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'trial_days' => 'integer',
        'features' => 'array',
    ];
    
    /**
     * Get the tenants associated with this plan.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }
}
