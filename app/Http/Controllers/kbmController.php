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

        // Server-side search support: accept a single 'q' parameter and filter across
        // related guru (nama, mapel), walas (namakelas, jenjang) and hari.
        $search = request()->query('q');
        if ($search) {
            $searchRaw = trim(strtolower($search));
            $code = $this->codeCAD($searchRaw); // normalize jenjang search to X/XI/XII if possible

            $query->where(function($q) use ($searchRaw, $code) {
                $q->whereHas('guru', function($qg) use ($searchRaw) {
                    $qg->where('nama', 'like', "%{$searchRaw}%")
                       ->orWhere('mapel', 'like', "%{$searchRaw}%");
                })
                ->orWhereHas('walas', function($qw) use ($searchRaw, $code) {
                    $qw->where('namakelas', 'like', "%{$searchRaw}%");
                    if ($code) {
                        // match normalized jenjang exactly (data stores X/XI/XII)
                        $qw->orWhere('jenjang', '=', $code);
                    } else {
                        $qw->orWhere('jenjang', 'like', "%{$searchRaw}%");
                    }
                })
                ->orWhere('hari', 'like', "%{$searchRaw}%");
            });
        }

        return response()->json($query->get());
    }

    /**
     * Normalize various jenjang representations to canonical values used in DB.
     * Examples: '10', 'kelas 10', 'x', 'X', 'xi', '11' -> returns 'X', 'XI', 'XII' etc.
     */
    private function codeCAD($value)
    {
        if (!$value) return null;
        $s = strtolower(trim($value));

        // If numeric 10/11/12 present
        if (preg_match('/\b10\b/', $s)) return 'X';
        if (preg_match('/\b11\b/', $s)) return 'XI';
        if (preg_match('/\b12\b/', $s)) return 'XII';

        // roman/letter forms
        if (strpos($s, 'xii') !== false) return 'XII';
        if (strpos($s, 'xi') !== false) return 'XI';
        if (strpos($s, 'x') !== false) return 'X';

        // fallback: if first token matches one of X/XI/XII
        $first = strtoupper(strtok($s, " \t\n\r\0\x0B"));
        if (in_array($first, ['X', 'XI', 'XII'])) return $first;

        return null;
    }
}
