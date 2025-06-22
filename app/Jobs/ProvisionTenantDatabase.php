<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ProvisionTenantDatabase implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The tenant instance.
     *
     * @var \App\Models\Tenant
     */
    protected $tenant;

    /**
     * Whether to seed the tenant database with demo data.
     *
     * @var bool
     */
    protected $withDemoData;

    /**
     * Create a new job instance.
     */
    public function __construct(Tenant $tenant, bool $withDemoData = false)
    {
        $this->tenant = $tenant;
        $this->withDemoData = $withDemoData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Create the tenant database if it doesn't exist
        $this->createTenantDatabase();

        // Configure the tenant (this will make the tenant the current tenant in the app)
        $this->tenant->makeCurrent();

        // Run the migrations for the tenant
        $this->runTenantMigrations();

        // Seed the tenant database if requested
        if ($this->withDemoData) {
            $this->seedTenantDatabase();
        }

        // Forget the current tenant
        $this->tenant->forgetCurrent();
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return $this->tenant->id;
    }

    /**
     * Create the tenant database.
     */
    protected function createTenantDatabase(): void
    {
        $databaseName = $this->tenant->database;

        // Check if the database already exists
        $databaseExists = DB::select(
            "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?",
            [$databaseName]
        );

        // If the database doesn't exist, create it
        if (empty($databaseExists)) {
            DB::statement("CREATE DATABASE `{$databaseName}`");
        }
    }

    /**
     * Run the migrations for the tenant.
     */
    protected function runTenantMigrations(): void
    {
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);
    }

    /**
     * Seed the tenant database.
     */
    protected function seedTenantDatabase(): void
    {
        Artisan::call('db:seed', [
            '--database' => 'tenant',
            '--class' => 'Database\\Seeders\\Tenant\\TenantDatabaseSeeder',
            '--force' => true,
        ]);
    }
}
