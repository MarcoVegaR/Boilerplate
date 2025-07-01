<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

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
        'settings'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime'
    ];
    
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'landlord';
    
    /**
     * Get the plan that owns the tenant.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the domain URL
     * 
     * @return string
     */
    public function getDomainUrl(): string
    {
        return 'https://' . $this->domain;
    }
}
