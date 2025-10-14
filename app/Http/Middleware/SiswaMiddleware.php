<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SiswaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('admin_id') || !session('admin_role')) {
            return redirect()->route('formLogin')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (session('admin_role') !== 'siswa') {
            return redirect()->route('home')->with('error', 'Akses ditolak. Hanya siswa yang dapat mengakses halaman ini.');
        }

        // Get siswa data
        $siswa = \App\Models\Siswa::where('id', session('admin_id'))
            ->with(['kelas.walas.guru'])
            ->first();
        
        if (!$siswa) {
            return redirect()->route('home')->with('error', 'Data siswa tidak ditemukan.');
        }

        if (!$siswa->kelas) {
            return redirect()->route('home')->with('error', 'Anda belum terdaftar di kelas manapun.');
        }

        // Share siswa data with all views
        view()->share('siswaLogin', $siswa);
        view()->share('kelasData', $siswa->kelas->walas);
        view()->share('kelasSaya', $siswa->kelas);
        view()->share('waliKelas', $siswa->kelas->walas->guru);
        
        $request->siswaLogin = $siswa;
        return $next($request);
    }
}