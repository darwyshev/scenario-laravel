<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kbm;
use App\Models\guru;
use App\Models\Walas;

class kbmController extends Controller
{
    /**
     * Mengambil semua data jadwal KBM
     * Admin: Lihat semua jadwal
     * Guru: Lihat jadwal mengajar sendiri
     * Siswa: Lihat jadwal kelas sendiri
     */
    public function index()
    {
        $role = session('admin_role');
        $adminId = session('admin_id');

        // Cek apakah user sudah login
        if (!$role || !$adminId) {
            return redirect()->route('formLogin')->with('error', 'Silakan login terlebih dahulu.');
        }

        $guru = null;
        $siswaData = null;
        $kelasData = null;

        if ($role === 'admin') {
            // Admin melihat semua jadwal
            $jadwals = kbm::with(['guru', 'walas'])->get();
        } elseif ($role === 'guru') {
            // Guru hanya melihat jadwal mengajar sendiri
            $guru = guru::where('id', $adminId)->first();
            
            if (!$guru) {
                return redirect()->route('home')->with('error', 'Data guru tidak ditemukan.');
            }
            
            $jadwals = kbm::with(['guru', 'walas'])
                ->where('idguru', $guru->idguru)
                ->get();
        } elseif ($role === 'siswa') {
            // Siswa melihat jadwal kelas sendiri
            $siswaData = \App\Models\Siswa::where('id', $adminId)
                ->with(['kelas.walas'])
                ->first();
            
            if (!$siswaData || !$siswaData->kelas) {
                return redirect()->route('home')->with('error', 'Anda belum terdaftar di kelas manapun.');
            }
            
            $kelasData = $siswaData->kelas->walas;
            $jadwals = kbm::with(['guru', 'walas'])
                ->where('idwalas', $siswaData->kelas->idwalas)
                ->get();
        } else {
            return redirect()->route('home')->with('error', 'Akses ditolak.');
        }

        return view('kbm.index', compact('jadwals', 'guru', 'siswaData', 'kelasData'));
    }

    /**
     * Mengambil data jadwal KBM berdasarkan guru tertentu
     * Hanya admin yang bisa akses
     */
    public function showByGuru($idguru)
    {
        $role = session('admin_role');

        if ($role !== 'admin') {
            return redirect()->route('kbm.index')->with('error', 'Akses ditolak. Hanya admin yang dapat melihat jadwal guru lain.');
        }

        $guru = guru::with(['kbm.walas'])->findOrFail($idguru);
        return view('kbm.by-guru', compact('guru'));
    }

    /**
     * Mengambil data jadwal KBM berdasarkan kelas tertentu
     * Admin dan Guru bisa akses
     */
    public function showByKelas($idwalas)
    {
        $role = session('admin_role');

        if (!$role) {
            return redirect()->route('formLogin')->with('error', 'Silakan login terlebih dahulu.');
        }

        $kelas = Walas::with(['kbm.guru'])->findOrFail($idwalas);
        return view('kbm.by-kelas', compact('kelas'));
    }
}
