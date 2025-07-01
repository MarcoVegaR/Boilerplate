<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeSaasResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:saas:resource {name : The name of the resource (singular)} 
                            {--tenant : Create a tenant resource (default is landlord/admin)} 
                            {--all : Create both model, migration, controller, repository, service, policy and form requests} 
                            {--model : Create a model} 
                            {--migration : Create a migration} 
                            {--controller : Create a controller} 
                            {--repository : Create a repository} 
                            {--service : Create a service} 
                            {--policy : Create a policy} 
                            {--requests : Create form requests}
                            {--react : Create React components for the resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a complete CRUD resource for SaaS application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $isTenant = $this->option('tenant');
        $connection = $isTenant ? 'tenant' : 'landlord';
        $connectionPath = $isTenant ? 'tenant' : 'landlord';
        
        // Prevenir crear recursos de gestión de tenants en contexto tenant
        if (strtolower($name) === 'tenant' && $isTenant) {
            $this->error("Error: No se puede crear un recurso Tenant en el contexto tenant.");
            $this->info("Los recursos Tenant solo deben ser administrados desde el contexto admin.");
            return 1;
        }
        
        $this->info("Generating {$name} CRUD resource for " . ($isTenant ? 'tenant' : 'admin') . " context...");
        
        // Determine which components to generate
        $generateAll = $this->option('all');
        $generateModel = $generateAll || $this->option('model');
        $generateMigration = $generateAll || $this->option('migration');
        $generateController = $generateAll || $this->option('controller');
        $generateRepository = $generateAll || $this->option('repository');
        $generateService = $generateAll || $this->option('service');
        $generatePolicy = $generateAll || $this->option('policy');
        $generateRequests = $generateAll || $this->option('requests');
        $generateReact = $generateAll || $this->option('react');
        
        // If no specific options are provided, generate everything
        if (!$generateModel && !$generateMigration && !$generateController && 
            !$generateRepository && !$generateService && !$generatePolicy && 
            !$generateRequests && !$generateReact) {
            $generateModel = $generateMigration = $generateController = 
            $generateRepository = $generateService = $generatePolicy = 
            $generateRequests = $generateReact = true;
        }
        
        // Generate components
        if ($generateModel) {
            $this->createModel($name, $connection);
        }
        
        if ($generateMigration) {
            $this->createMigration($name, $connectionPath);
        }
        
        if ($generateRepository) {
            $this->createRepositoryInterface($name, $isTenant);
            $this->createRepository($name, $isTenant);
        }
        
        if ($generateService) {
            $this->createServiceInterface($name, $isTenant);
            $this->createService($name, $isTenant);
        }
        
        if ($generatePolicy) {
            $this->createPolicy($name, $isTenant);
        }
        
        if ($generateRequests) {
            $this->createRequests($name, $isTenant);
        }
        
        if ($generateController) {
            $this->createController($name, $isTenant);
        }
        
        if ($generateReact) {
            $this->createReactComponents($name, $isTenant);
        }
        
        // Register the bindings in RepositoryServiceProvider
        if ($generateRepository && $generateService) {
            $this->registerBindings($name, $isTenant);
        }
        
        $this->info("\n✅ CRUD resource for {$name} has been generated successfully!");
    }
    
    /**
     * Create the model.
     */
    protected function createModel(string $name, string $connection): void
    {
        $modelName = Str::singular(Str::studly($name));
        $modelNamespace = 'App\\Models';
        $stubPath = base_path('stubs/saas/model/model.stub');
        $targetPath = app_path("Models/{$modelName}.php");
        
        $this->generateFileFromStub($stubPath, $targetPath, [
            '{{ namespace }}' => $modelNamespace,
            '{{ class }}' => $modelName,
            '{{ connection }}' => $connection
        ]);
        
        $this->info("✓ Model created: {$modelName}");
    }
    
    /**
     * Create the migration.
     */
    protected function createMigration(string $name, string $connectionPath): void
    {
        $tableName = Str::plural(Str::snake($name));
        $stubPath = base_path('stubs/saas/migration/migration.stub');
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_create_{$tableName}_table.php";
        $targetPath = database_path("migrations/{$connectionPath}/{$filename}");
        
        // Ensure directories exist
        File::ensureDirectoryExists(database_path("migrations/{$connectionPath}"));
        
        $this->generateFileFromStub($stubPath, $targetPath, [
            '{{ table }}' => $tableName
        ]);
        
        $this->info("✓ Migration created: {$filename}");
    }
    
    /**
     * Create the controller.
     */
    protected function createController(string $name, bool $isTenant): void
    {
        $modelName = Str::singular(Str::studly($name));
        $controllerName = "{$modelName}Controller";
        $namespace = $isTenant ? 'App\\Http\\Controllers\\Tenant' : 'App\\Http\\Controllers\\Admin';
        $modelNamespace = 'App\\Models';
        $requestNamespace = $isTenant ? 'App\\Http\\Requests\\Tenant' : 'App\\Http\\Requests\\Admin';
        $serviceNamespace = $isTenant ? 'Tenant\\' : 'Admin\\'; 
        $inertiaPath = $isTenant ? 'Tenant/' . $modelName : 'Admin/' . $modelName;
        $routePrefix = Str::plural(Str::kebab($name));
        $routePrefix = $isTenant ? 'tenant.' . $routePrefix : 'admin.' . $routePrefix;
        
        $stubPath = base_path('stubs/saas/controller/controller.stub');
        $targetDir = $isTenant ? app_path('Http/Controllers/Tenant') : app_path('Http/Controllers/Admin');
        $targetPath = "{$targetDir}/{$controllerName}.php";
        
        // Ensure directories exist
        File::ensureDirectoryExists($targetDir);
        
        $this->generateFileFromStub($stubPath, $targetPath, [
            '{{ namespace }}' => $namespace,
            '{{ modelNamespace }}' => $modelNamespace,
            '{{ requestNamespace }}' => $requestNamespace,
            '{{ serviceNamespace }}' => $serviceNamespace,
            '{{ class }}' => $modelName,
            '{{ inertiaPath }}' => $inertiaPath,
            '{{ routePrefix }}' => $routePrefix,
            '{{ variableName }}' => Str::camel($modelName)
        ]);
        
        $this->info("✓ Controller created: {$controllerName}");
    }
    
    /**
     * Create the repository.
     */
    protected function createRepository(string $name, bool $isTenant): void
    {
        $modelName = Str::singular(Str::studly($name));
        $repositoryName = "{$modelName}Repository";
        $namespace = $isTenant ? 'App\\Repositories\\Tenant' : 'App\\Repositories\\Admin';
        $modelNamespace = 'App\\Models';
        
        $stubPath = base_path('stubs/saas/repository/repository.stub');
        $targetDir = $isTenant ? app_path('Repositories/Tenant') : app_path('Repositories/Admin');
        $targetPath = "{$targetDir}/{$repositoryName}.php";
        
        // Ensure directories exist
        File::ensureDirectoryExists($targetDir);
        
        $this->generateFileFromStub($stubPath, $targetPath, [
            '{{ namespace }}' => $namespace,
            '{{ modelNamespace }}' => $modelNamespace,
            '{{ class }}' => $modelName,
            '{{ variableName }}' => Str::camel($modelName)
        ]);
        
        $this->info("✓ Repository created: {$repositoryName}");
    }
    
    /**
     * Create the service.
     */
    protected function createService(string $name, bool $isTenant): void
    {
        $modelName = Str::singular(Str::studly($name));
        $serviceName = "{$modelName}Service";
        $namespace = $isTenant ? 'App\\Services\\Tenant' : 'App\\Services\\Admin';
        $repositoryNamespace = $isTenant ? 'App\\Repositories\\Tenant' : 'App\\Repositories\\Admin';
        $modelNamespace = 'App\\Models';
        
        $stubPath = base_path('stubs/saas/service/service.stub');
        $targetDir = $isTenant ? app_path('Services/Tenant') : app_path('Services/Admin');
        $targetPath = "{$targetDir}/{$serviceName}.php";
        
        // Ensure directories exist
        File::ensureDirectoryExists($targetDir);
        
        $this->generateFileFromStub($stubPath, $targetPath, [
            '{{ namespace }}' => $namespace,
            '{{ modelNamespace }}' => $modelNamespace,
            '{{ repositoryNamespace }}' => $repositoryNamespace,
            '{{ class }}' => $modelName,
            '{{ variableName }}' => Str::camel($modelName)
        ]);
        
        $this->info("✓ Service created: {$serviceName}");
    }
    
    /**
     * Create the policy.
     */
    protected function createPolicy(string $name, bool $isTenant): void
    {
        $modelName = Str::singular(Str::studly($name));
        $policyName = "{$modelName}Policy";
        $namespace = 'App\\Policies';
        $modelNamespace = 'App\\Models';
        
        $stubPath = base_path('stubs/saas/policy/policy.stub');
        $targetDir = app_path('Policies');
        $targetPath = "{$targetDir}/{$policyName}.php";
        
        // Ensure directories exist
        File::ensureDirectoryExists($targetDir);
        
        $this->generateFileFromStub($stubPath, $targetPath, [
            '{{ namespace }}' => $namespace,
            '{{ modelNamespace }}' => $modelNamespace,
            '{{ class }}' => $modelName,
            '{{ variableName }}' => Str::camel($modelName)
        ]);
        
        $this->info("✓ Policy created: {$policyName}");
    }
    
    /**
     * Create the form requests.
     */
    protected function createRequests(string $name, bool $isTenant): void
    {
        $modelName = Str::singular(Str::studly($name));
        $namespace = $isTenant ? 'App\\Http\\Requests\\Tenant' : 'App\\Http\\Requests\\Admin';
        
        $storeRequestName = "Store{$modelName}Request";
        $updateRequestName = "Update{$modelName}Request";
        
        $storeStubPath = base_path('stubs/saas/requests/store-request.stub');
        $updateStubPath = base_path('stubs/saas/requests/update-request.stub');
        
        $targetDir = $isTenant ? app_path('Http/Requests/Tenant') : app_path('Http/Requests/Admin');
        $storeTargetPath = "{$targetDir}/{$storeRequestName}.php";
        $updateTargetPath = "{$targetDir}/{$updateRequestName}.php";
        
        // Ensure directories exist
        File::ensureDirectoryExists($targetDir);
        
        // Generate store request
        $this->generateFileFromStub($storeStubPath, $storeTargetPath, [
            '{{ namespace }}' => $namespace,
            '{{ class }}' => $modelName
        ]);
        
        // Generate update request
        $this->generateFileFromStub($updateStubPath, $updateTargetPath, [
            '{{ namespace }}' => $namespace,
            '{{ class }}' => $modelName
        ]);
        
        $this->info("✓ Requests created: {$storeRequestName}, {$updateRequestName}");
    }
    
    /**
     * Create React components.
     */
    protected function createReactComponents(string $name, bool $isTenant): void
    {
        $modelName = Str::singular(Str::studly($name));
        $pluralName = Str::plural($modelName);
        $routePrefix = Str::plural(Str::kebab($name));
        $routePrefix = $isTenant ? 'tenant.' . $routePrefix : 'admin.' . $routePrefix;
        
        $baseDir = $isTenant ? 'Tenant/' : 'Admin/';
        $targetDir = resource_path("js/Pages/{$baseDir}{$modelName}");
        
        // Ensure directories exist
        File::ensureDirectoryExists($targetDir);
        
        // Components to generate: Index, Create, Edit, Show
        $components = ['index', 'create', 'edit', 'show'];
        
        foreach ($components as $component) {
            $stubPath = base_path("stubs/saas/react/{$component}.tsx.stub");
            $targetPath = "{$targetDir}/" . ucfirst($component) . ".tsx";
            
            $this->generateFileFromStub($stubPath, $targetPath, [
                '{{ routePrefix }}' => $routePrefix,
                '{{ title }}' => $modelName,
                '{{ pluralTitle }}' => $pluralName
            ]);
        }
        
        $this->info("✓ React components created for {$modelName}");
    }
    
    /**
     * Create repository interface.
     */
    protected function createRepositoryInterface(string $name, bool $isTenant): void
    {
        $modelName = Str::singular(Str::studly($name));
        $interfaceName = "{$modelName}RepositoryInterface";
        $namespace = 'App\\Repositories\\Contracts';
        $modelNamespace = 'App\\Models';
        
        $stubPath = base_path('stubs/saas/repository/repository.interface.stub');
        $targetDir = app_path('Repositories/Contracts');
        $targetPath = "{$targetDir}/{$interfaceName}.php";
        
        // Ensure directories exist
        File::ensureDirectoryExists($targetDir);
        
        $this->generateFileFromStub($stubPath, $targetPath, [
            '{{ namespace }}' => $namespace,
            '{{ modelNamespace }}' => $modelNamespace,
            '{{ class }}' => $modelName,
        ]);
        
        $this->info("✓ Repository Interface created: {$interfaceName}");
    }
    
    /**
     * Create service interface.
     */
    protected function createServiceInterface(string $name, bool $isTenant): void
    {
        $modelName = Str::singular(Str::studly($name));
        $interfaceName = "{$modelName}ServiceInterface";
        $namespace = 'App\\Services\\Contracts';
        $modelNamespace = 'App\\Models';
        
        $stubPath = base_path('stubs/saas/service/service.interface.stub');
        $targetDir = app_path('Services/Contracts');
        $targetPath = "{$targetDir}/{$interfaceName}.php";
        
        // Ensure directories exist
        File::ensureDirectoryExists($targetDir);
        
        $this->generateFileFromStub($stubPath, $targetPath, [
            '{{ namespace }}' => $namespace,
            '{{ modelNamespace }}' => $modelNamespace,
            '{{ class }}' => $modelName,
        ]);
        
        $this->info("✓ Service Interface created: {$interfaceName}");
    }
    
    /**
     * Register bindings in RepositoryServiceProvider.
     */
    protected function registerBindings(string $name, bool $isTenant): void
    {
        $modelName = Str::singular(Str::studly($name));
        $repositoryInterfaceName = "\\App\\Repositories\\Contracts\\{$modelName}RepositoryInterface";
        $repositoryName = $isTenant 
            ? "\\App\\Repositories\\Tenant\\{$modelName}Repository" 
            : "\\App\\Repositories\\Admin\\{$modelName}Repository";
        
        $serviceInterfaceName = "\\App\\Services\\Contracts\\{$modelName}ServiceInterface";
        $serviceName = $isTenant 
            ? "\\App\\Services\\Tenant\\{$modelName}Service" 
            : "\\App\\Services\\Admin\\{$modelName}Service";
        
        // Path to RepositoryServiceProvider
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');
        
        if (!File::exists($providerPath)) {
            $this->error("RepositoryServiceProvider.php not found at {$providerPath}");
            return;
        }
        
        // Read the provider content
        $content = File::get($providerPath);
        
        // Find the register method
        $pattern = '/public function register\(\): void\s*{\s*(.*?)\s*}/s';
        if (preg_match($pattern, $content, $matches)) {
            $registerMethod = $matches[1];
            
            // Add new bindings
            $newBindings = "\n        // {$modelName} bindings\n";
            $newBindings .= "        \$this->app->bind({$repositoryInterfaceName}::class, {$repositoryName}::class);\n";
            $newBindings .= "        \$this->app->bind({$serviceInterfaceName}::class, {$serviceName}::class);";
            
            // Check if bindings already exist
            if (strpos($registerMethod, "{$modelName} bindings") === false) {
                // Replace old register method with new one
                $newRegisterMethod = str_replace($registerMethod, $registerMethod . $newBindings, $matches[0]);
                $content = str_replace($matches[0], $newRegisterMethod, $content);
                
                // Write back to file
                File::put($providerPath, $content);
                $this->info("✓ Bindings registered in RepositoryServiceProvider");
            } else {
                $this->info("✓ Bindings already exist in RepositoryServiceProvider");
            }
        } else {
            $this->error("Could not find register method in RepositoryServiceProvider");
        }
    }
    
    /**
     * Generate a file from a stub.
     */
    protected function generateFileFromStub(string $stubPath, string $targetPath, array $replacements): void
    {
        if (!File::exists($stubPath)) {
            $this->error("Stub file does not exist: {$stubPath}");
            return;
        }
        
        $content = File::get($stubPath);
        
        // Replace placeholders
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        // Create directory if it doesn't exist
        $directory = dirname($targetPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        
        // Write the file
        File::put($targetPath, $content);
    }
}
