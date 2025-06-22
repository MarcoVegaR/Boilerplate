<?php

namespace App\Console\Commands\Tenants;

use App\Events\TenantCreated;
use App\Jobs\ProvisionTenantDatabase;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTenantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:create {name : The name of the tenant} {domain : The domain of the tenant} {--with-demo-data : Seed the tenant with demo data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');
        $withDemoData = $this->option('with-demo-data');
        
        $this->info("Creating tenant: {$name} for domain: {$domain}");
        
        try {
            DB::beginTransaction();
            
            $tenant = Tenant::create([
                'name' => $name,
                'domain' => $domain,
                'database' => 'tenant_' . Str::slug($name),
            ]);
            
            DB::commit();
            
            $this->info("Tenant created successfully!");
            $this->info("Database name: {$tenant->database}");
            
            // Dispatch job to provision the tenant database
            $this->info("Provisioning database for tenant...");
            ProvisionTenantDatabase::dispatch($tenant, $withDemoData);
            
            // Fire event for other tenant setup processes
            event(new TenantCreated($tenant));
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to create tenant: {$e->getMessage()}");
            
            return Command::FAILURE;
        }
    }
}
