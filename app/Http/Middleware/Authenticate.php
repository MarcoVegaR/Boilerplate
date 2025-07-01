<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            // Si la ruta comienza con 'admin/', redirigir al login de admin
            if (str_starts_with($request->path(), 'admin')) {
                return route('admin.login');
            }
            
            // Para todas las demás rutas, usar el login predeterminado
            return route('login');
        }
        
        return null;
    }
}
