<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuruMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('admin_id') || !session('admin_role')) {
            return redirect()->route('formLogin')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (session('admin_role') !== 'guru') {
            return redirect()->route('home')->with('error', 'Akses ditolak. Hanya guru yang dapat mengakses halaman ini.');
        }

        // Get guru data
        $guru = \App\Models\Guru::where('id', session('admin_id'))->first();
        if (!$guru) {
            return redirect()->route('home')->with('error', 'Data guru tidak ditemukan.');
        }

        // Share guru data with all views
        view()->share('guru', $guru);
        
        // If guru is a wali kelas, share their class data
        if ($guru->walas) {
            view()->share('walas', $guru->walas);
            view()->share('siswaKelas', $guru->walas->kelas);
        }
        
        $request->guru = $guru;
        return $next($request);
    }
}