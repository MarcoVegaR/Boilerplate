<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a basic plan
        Plan::create([
            'name' => 'Basic Plan',
            'slug' => Str::slug('Basic Plan'),
            'description' => 'A starter plan for small businesses',
            'price' => 19.99,
            'billing_period' => 'monthly',
            'is_active' => true,
            'is_featured' => true,
            'trial_days' => 14,
            'features' => [
                'dashboard',
                'customers_management',
                'products_management',
                'orders_management',
                'basic_reporting',
            ],
        ]);
        
        // Create a pro plan
        Plan::create([
            'name' => 'Pro Plan',
            'slug' => Str::slug('Pro Plan'),
            'description' => 'Advanced features for growing businesses',
            'price' => 49.99,
            'billing_period' => 'monthly',
            'is_active' => true,
            'is_featured' => false,
            'trial_days' => 7,
            'features' => [
                'dashboard',
                'customers_management',
                'products_management',
                'orders_management',
                'basic_reporting',
                'advanced_reporting',
                'api_access',
                'custom_branding',
                'priority_support',
            ],
        ]);
    }
}
