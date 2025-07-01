<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Seed the plans first (requirements for tenants)
            PlanSeeder::class,
            
            // Seed admin user
            AdminSeeder::class,
            
            // Seed regular users
            UserSeeder::class,
            
            // Seed demo tenant (must be after plans)
            TenantSeeder::class,
        ]);
    }
}
