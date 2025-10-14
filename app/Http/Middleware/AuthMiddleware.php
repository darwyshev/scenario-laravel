<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('admin_id') || !session('admin_role')) {
            return redirect()->route('formLogin')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Share common data with all views
        view()->share('admin_role', session('admin_role'));
        view()->share('admin_username', session('admin_username'));
        view()->share('admin_id', session('admin_id'));
        
        return $next($request);
    }
}