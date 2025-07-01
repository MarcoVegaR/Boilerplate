<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        // Repository bindings
        'App\Repositories\Contracts\TenantRepositoryInterface' => 'App\Repositories\Admin\TenantRepository',
        
        // Service bindings
        'App\Services\Contracts\TenantServiceInterface' => 'App\Services\Admin\TenantService',
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Register all bindings
        foreach ($this->bindings as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 
    }

    /**
     * Add a new binding to the service container.
     * 
     * @param string $interface
     * @param string $implementation
     * @return void
     */
    protected function addBinding(string $interface, string $implementation): void
    {
        $this->app->bind($interface, $implementation);
    }
}
