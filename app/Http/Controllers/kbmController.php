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

        // Server-side search support: accept a single 'q' parameter.
        // If the 'q' string contains commas we'll treat it as a formatted search:
        // 0: guru (nama), 1: mapel, 2: kelas (e.g. A/B/C), 3: jenjang, 4: hari, 5: time (mulai/selesai)
        // Tokens are applied as AND filters. If there are no commas we fall back to a broad search.
        $search = request()->query('q');
        if ($search) {
            $searchRaw = trim($search);
            // detect formatted (comma-separated) search
            if (strpos($searchRaw, ',') !== false) {
                $tokens = array_map('trim', explode(',', $searchRaw));
                // apply each token as an AND filter according to position
                if (!empty($tokens)) {
                    // Lowercase tokens for comparisons where appropriate
                    $t = array_map(function($s){ return strtolower($s); }, $tokens);

                    $query->where(function($q) use ($t) {
                        // token 0: guru nama
                        if (isset($t[0]) && $t[0] !== '') {
                            $q->whereHas('guru', function($g) use ($t) {
                                $g->where('nama', 'like', "%{$t[0]}%");
                            });
                        }

                        // token 1: mapel
                        if (isset($t[1]) && $t[1] !== '') {
                            $q->whereHas('guru', function($g) use ($t) {
                                $g->where('mapel', 'like', "%{$t[1]}%");
                            });
                        }

                        // token 2: kelas (allow matching by letter or any substring)
                        if (isset($t[2]) && $t[2] !== '') {
                            $kelasToken = $t[2];
                            $q->whereHas('walas', function($w) use ($kelasToken) {
                                $w->where('namakelas', 'like', "%{$kelasToken}%")
                                  ->orWhereRaw('LOWER(namakelas) LIKE ?', ["% {$kelasToken}%"]);
                            });
                        }

                        // token 3: jenjang (use codeCAD normalization)
                        if (isset($t[3]) && $t[3] !== '') {
                            $jenjangToken = $t[3];
                            $code = $this->codeCAD($jenjangToken);
                            $q->whereHas('walas', function($w) use ($jenjangToken, $code) {
                                if ($code) {
                                    $w->where('jenjang', '=', $code);
                                } else {
                                    $w->where('jenjang', 'like', "%{$jenjangToken}%");
                                }
                            });
                        }

                        // token 4: hari
                        if (isset($t[4]) && $t[4] !== '') {
                            $hariToken = $t[4];
                            $q->where('hari', 'like', "%{$hariToken}%");
                        }

                        // token 5: time (match mulai or selesai)
                        if (isset($t[5]) && $t[5] !== '') {
                            $timeToken = $t[5];
                            // allow both dot and colon formats
                            $timeAlt = str_replace('.', ':', $timeToken);
                            $q->where(function($qt) use ($timeToken, $timeAlt) {
                                $qt->where('mulai', 'like', "%{$timeToken}%")
                                   ->orWhere('mulai', 'like', "%{$timeAlt}%")
                                   ->orWhere('selesai', 'like', "%{$timeToken}%")
                                   ->orWhere('selesai', 'like', "%{$timeAlt}%");
                            });
                        }
                    });
                }
            } else {
                // Fallback: broad search across guru (nama,mapel), walas (namakelas, jenjang) and hari
                $searchLower = strtolower($searchRaw);
                $code = $this->codeCAD($searchLower);
                $query->where(function($q) use ($searchLower, $code) {
                    $q->whereHas('guru', function($qg) use ($searchLower) {
                        $qg->where('nama', 'like', "%{$searchLower}%")
                           ->orWhere('mapel', 'like', "%{$searchLower}%");
                    })
                    ->orWhereHas('walas', function($qw) use ($searchLower, $code) {
                        $qw->where('namakelas', 'like', "%{$searchLower}%");
                        if ($code) {
                            $qw->orWhere('jenjang', '=', $code);
                        } else {
                            $qw->orWhere('jenjang', 'like', "%{$searchLower}%");
                        }
                    })
                    ->orWhere('hari', 'like', "%{$searchLower}%");
                });
            }
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
