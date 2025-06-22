<?php

use App\Http\Controllers\Tenant\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here is where you can register tenant-specific routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group and "tenant" prefix. Now create something great!
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('tenant.dashboard');
});
