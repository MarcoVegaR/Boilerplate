<?php

namespace Database\Seeders;

use App\Jobs\ProvisionTenantDatabase;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first plan (Basic Plan)
        $plan = Plan::where('slug', 'basic-plan')->first();

        if (!$plan) {
            $this->command->error('Plan not found, please run the PlanSeeder first.');
            return;
        }
        
        // Create a demo tenant
        $tenant = Tenant::create([
            'name' => 'Acme Corporation',
            'domain' => 'acme.localhost',
            'database' => 'tenant_acme',
            'plan_id' => $plan->id,
            'trial_ends_at' => now()->addDays($plan->trial_days),
            'subscription_ends_at' => now()->addYear(),
            'is_active' => true,
            'settings' => [
                'theme' => 'light',
                'logo' => null,
                'primary_color' => '#4f46e5',
                'secondary_color' => '#9333ea',
            ],
        ]);
        
        $this->command->info("Demo tenant created: {$tenant->name}");
        
        // Provision the tenant database with demo data
        ProvisionTenantDatabase::dispatch($tenant, true);
        $this->command->info("Database provisioning job dispatched for tenant: {$tenant->name}");
    }
}
