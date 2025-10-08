<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (session('admin_role') !== 'admin') {
            return redirect()->route('home')->with('error', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
        
        return $next($request);
    }
}