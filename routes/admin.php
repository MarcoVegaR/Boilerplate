<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TenantController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group and "admin" prefix. Now create something great!
|
*/

// Rutas accesibles sólo para invitados del panel admin
Route::middleware('guest:admin')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
        
    // Tenant Resource Routes
    Route::resource('tenants', TenantController::class);
    
    // Custom Tenant Actions
    Route::post('tenants/{tenant}/extend-trial', [TenantController::class, 'extendTrial'])
        ->name('tenants.extend-trial');
        
    Route::post('tenants/{tenant}/change-plan', [TenantController::class, 'changePlan'])
        ->name('tenants.change-plan');
        
    Route::post('tenants/{tenant}/toggle-active', [TenantController::class, 'toggleActive'])
        ->name('tenants.toggle-active');
});
