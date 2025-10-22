<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kbm;
use App\Models\guru;
use App\Models\Walas;
use App\Models\siswa;

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
        $jadwals = [];

        switch ($role) {
            case 'admin':
                $jadwals = kbm::with(['guru', 'walas'])->get();
                break;
            case 'guru':
                $jadwals = kbm::with(['guru', 'walas'])
                    ->where('idguru', request()->guru->idguru)
                    ->get();
                break;
            case 'siswa':
                $jadwals = kbm::with(['guru', 'walas'])
                    ->where('idwalas', request()->siswaLogin->kelas->idwalas)
                    ->get();
                break;
        }

        return view('kbm.index', [
            'jadwals' => $jadwals,
            'guru' => request()->guru ?? null,
            'siswaData' => request()->siswaLogin ?? null,
            'kelasData' => request()->siswaLogin->kelas->walas ?? null
        ]);
    }

    /**
     * Mengambil data jadwal KBM berdasarkan guru tertentu
     * Hanya admin yang bisa akses
     */
    public function showByGuru($idguru)
    {
        $guru = guru::with(['kbm.walas'])->findOrFail($idguru);
        return view('kbm.by-guru', compact('guru'));
    }

    /**
     * Mengambil data jadwal KBM berdasarkan kelas tertentu
     * Admin dan Guru bisa akses
     */
    public function showByKelas($idwalas)
    {
        $kelas = Walas::with(['kbm.guru'])->findOrFail($idwalas);
        return view('kbm.by-kelas', compact('kelas'));
    }

    public function getData()
    {
        $role = session('admin_role');
        $userId = session('admin_id');

        $query = kbm::with(['guru', 'walas' => function($query) {
            $query->select('idwalas', 'namakelas', 'jenjang');
        }]);

        switch ($role) {
            case 'guru':
                $guru = guru::where('id', $userId)->first();
                if ($guru) {
                    $query->where('idguru', $guru->idguru);
                }
                break;

            case 'siswa':
                $siswa = siswa::where('id', $userId)
                    ->with('kelas.walas')
                    ->first();
                if ($siswa && $siswa->kelas) {
                    $query->where('idwalas', $siswa->kelas->idwalas);
                }
                break;
        }

        return response()->json($query->get());
    }
}
