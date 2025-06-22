<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MakeSaasResourceCommandTest extends TestCase
{
    /**
     * Clean up test files after tests.
     */
    protected function tearDown(): void
    {
        // Clean up test files
        $this->cleanupTestFiles();
        
        parent::tearDown();
    }
    
    /**
     * Test basic resource creation for admin (landlord) context.
     */
    public function test_make_admin_resource_command(): void
    {
        // Run the command
        $this->artisan('make:saas:resource', [
            'name' => 'Product',
            '--all' => true
        ])
        ->expectsOutput('Generating Product CRUD resource for admin context...')
        ->expectsOutput('✓ Model created: Product')
        // La migración incluye un timestamp, así que solo verificamos que comienza con este texto
        ->expectsOutputToContain('✓ Migration created:')
        ->expectsOutput('✓ Repository created: ProductRepository')
        ->expectsOutput('✓ Service created: ProductService')
        ->expectsOutput('✓ Policy created: ProductPolicy')
        ->expectsOutput('✓ Requests created: StoreProductRequest, UpdateProductRequest')
        ->expectsOutput('✓ Controller created: ProductController')
        ->expectsOutput('✓ React components created for Product')
        ->assertExitCode(0);

        // Verify files were created
        $this->assertFileExists(app_path('Models/Product.php'));
        $this->assertFileExists(app_path('Repositories/Admin/ProductRepository.php'));
        $this->assertFileExists(app_path('Services/Admin/ProductService.php'));
        $this->assertFileExists(app_path('Policies/ProductPolicy.php'));
        $this->assertFileExists(app_path('Http/Requests/Admin/StoreProductRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/Admin/UpdateProductRequest.php'));
        $this->assertFileExists(app_path('Http/Controllers/Admin/ProductController.php'));
        $this->assertFileExists(resource_path('js/Pages/Admin/Product/Index.tsx'));
        $this->assertFileExists(resource_path('js/Pages/Admin/Product/Create.tsx'));
        $this->assertFileExists(resource_path('js/Pages/Admin/Product/Edit.tsx'));
        $this->assertFileExists(resource_path('js/Pages/Admin/Product/Show.tsx'));
        
        // Check for migration file (pattern-based since it has timestamp)
        $this->assertHasMigration('create_products_table', 'landlord');
    }
    
    /**
     * Test basic resource creation for tenant context.
     */
    public function test_make_tenant_resource_command(): void
    {
        // Run the command
        $this->artisan('make:saas:resource', [
            'name' => 'Order',
            '--tenant' => true,
            '--all' => true
        ])
        ->expectsOutput('Generating Order CRUD resource for tenant context...')
        ->expectsOutput('✓ Model created: Order')
        // La migración incluye un timestamp, así que solo verificamos que comienza con este texto
        ->expectsOutputToContain('✓ Migration created:')
        ->expectsOutput('✓ Repository created: OrderRepository')
        ->expectsOutput('✓ Service created: OrderService')
        ->expectsOutput('✓ Policy created: OrderPolicy')
        ->expectsOutput('✓ Requests created: StoreOrderRequest, UpdateOrderRequest')
        ->expectsOutput('✓ Controller created: OrderController')
        ->expectsOutput('✓ React components created for Order')
        ->assertExitCode(0);

        // Verify files were created
        $this->assertFileExists(app_path('Models/Order.php'));
        $this->assertFileExists(app_path('Repositories/Tenant/OrderRepository.php'));
        $this->assertFileExists(app_path('Services/Tenant/OrderService.php'));
        $this->assertFileExists(app_path('Policies/OrderPolicy.php'));
        $this->assertFileExists(app_path('Http/Requests/Tenant/StoreOrderRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/Tenant/UpdateOrderRequest.php'));
        $this->assertFileExists(app_path('Http/Controllers/Tenant/OrderController.php'));
        $this->assertFileExists(resource_path('js/Pages/Tenant/Order/Index.tsx'));
        $this->assertFileExists(resource_path('js/Pages/Tenant/Order/Create.tsx'));
        $this->assertFileExists(resource_path('js/Pages/Tenant/Order/Edit.tsx'));
        $this->assertFileExists(resource_path('js/Pages/Tenant/Order/Show.tsx'));
        
        // Check for migration file (pattern-based since it has timestamp)
        $this->assertHasMigration('create_orders_table', 'tenant');
    }
    
    /**
     * Test selective resource creation (only model and controller).
     */
    public function test_make_selective_resource(): void
    {
        // Run the command
        $this->artisan('make:saas:resource', [
            'name' => 'Category',
            '--model' => true,
            '--controller' => true
        ])
        ->expectsOutput('Generating Category CRUD resource for admin context...')
        ->expectsOutput('✓ Model created: Category')
        ->expectsOutput('✓ Controller created: CategoryController')
        ->assertExitCode(0);

        // Verify files were created
        $this->assertFileExists(app_path('Models/Category.php'));
        $this->assertFileExists(app_path('Http/Controllers/Admin/CategoryController.php'));
        
        // Verify files were NOT created
        $this->assertFileDoesNotExist(app_path('Repositories/Admin/CategoryRepository.php'));
        $this->assertFileDoesNotExist(app_path('Services/Admin/CategoryService.php'));
        $this->assertFileDoesNotExist(app_path('Policies/CategoryPolicy.php'));
        $this->assertFileDoesNotExist(app_path('Http/Requests/Admin/StoreCategoryRequest.php'));
        $this->assertFileDoesNotExist(app_path('Http/Requests/Admin/UpdateCategoryRequest.php'));
        $this->assertFileDoesNotExist(resource_path('js/Pages/Admin/Category/Index.tsx'));
    }
    
    /**
     * Helper to check for migrations that include a pattern in their filename.
     */
    protected function assertHasMigration(string $pattern, string $connectionPath): void
    {
        $migrationDir = database_path("migrations/{$connectionPath}");
        $files = File::glob("{$migrationDir}/*{$pattern}.php");
        
        $this->assertNotEmpty($files, "Migration for {$pattern} not found in {$connectionPath} directory.");
    }
    
    /**
     * Clean up test files created during tests.
     */
    protected function cleanupTestFiles(): void
    {
        // Test resources to clean
        $testResources = ['Product', 'Order', 'Category'];
        
        foreach ($testResources as $resource) {
            // Clean up models
            if (File::exists(app_path("Models/{$resource}.php"))) {
                File::delete(app_path("Models/{$resource}.php"));
            }
            
            // Clean up admin resources
            if (File::exists(app_path("Http/Controllers/Admin/{$resource}Controller.php"))) {
                File::delete(app_path("Http/Controllers/Admin/{$resource}Controller.php"));
            }
            
            if (File::exists(app_path("Repositories/Admin/{$resource}Repository.php"))) {
                File::delete(app_path("Repositories/Admin/{$resource}Repository.php"));
            }
            
            if (File::exists(app_path("Services/Admin/{$resource}Service.php"))) {
                File::delete(app_path("Services/Admin/{$resource}Service.php"));
            }
            
            if (File::exists(app_path("Http/Requests/Admin/Store{$resource}Request.php"))) {
                File::delete(app_path("Http/Requests/Admin/Store{$resource}Request.php"));
            }
            
            if (File::exists(app_path("Http/Requests/Admin/Update{$resource}Request.php"))) {
                File::delete(app_path("Http/Requests/Admin/Update{$resource}Request.php"));
            }
            
            // Clean up tenant resources
            if (File::exists(app_path("Http/Controllers/Tenant/{$resource}Controller.php"))) {
                File::delete(app_path("Http/Controllers/Tenant/{$resource}Controller.php"));
            }
            
            if (File::exists(app_path("Repositories/Tenant/{$resource}Repository.php"))) {
                File::delete(app_path("Repositories/Tenant/{$resource}Repository.php"));
            }
            
            if (File::exists(app_path("Services/Tenant/{$resource}Service.php"))) {
                File::delete(app_path("Services/Tenant/{$resource}Service.php"));
            }
            
            if (File::exists(app_path("Http/Requests/Tenant/Store{$resource}Request.php"))) {
                File::delete(app_path("Http/Requests/Tenant/Store{$resource}Request.php"));
            }
            
            if (File::exists(app_path("Http/Requests/Tenant/Update{$resource}Request.php"))) {
                File::delete(app_path("Http/Requests/Tenant/Update{$resource}Request.php"));
            }
            
            // Clean up policies
            if (File::exists(app_path("Policies/{$resource}Policy.php"))) {
                File::delete(app_path("Policies/{$resource}Policy.php"));
            }
            
            // Clean up React components
            $adminDir = resource_path("js/Pages/Admin/{$resource}");
            $tenantDir = resource_path("js/Pages/Tenant/{$resource}");
            
            if (File::isDirectory($adminDir)) {
                File::deleteDirectory($adminDir);
            }
            
            if (File::isDirectory($tenantDir)) {
                File::deleteDirectory($tenantDir);
            }
        }
        
        // Clean up migrations
        $migrationPatterns = [
            'create_products_table',
            'create_orders_table',
            'create_categories_table'
        ];
        
        foreach (['landlord', 'tenant'] as $connectionPath) {
            $migrationDir = database_path("migrations/{$connectionPath}");
            
            if (File::isDirectory($migrationDir)) {
                foreach ($migrationPatterns as $pattern) {
                    $files = File::glob("{$migrationDir}/*{$pattern}.php");
                    foreach ($files as $file) {
                        File::delete($file);
                    }
                }
            }
        }
    }
}
