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

        // Server-side: support space-separated tokens where each token must match at least one column.
        // This allows queries like "username senin" to match a row where the guru is 'username' AND hari is 'senin'.
        $search = request()->query('q');
        if ($search) {
            $searchRaw = trim($search);
            // Split on whitespace to get tokens
            $tokens = preg_split('/\s+/', $searchRaw);
            if ($tokens && count($tokens) > 0) {
                // collect probable name/mapel tokens to attempt combined phrase match
                $nameParts = [];
                $otherTokens = [];

                $classLetter = null;

                    // If last token is a single letter and there is at least one other token,
                    // treat it as an explicit class-letter (e.g. 'scot kautzer a') and remove it
                    // from the main token list so it's not mis-classified as part of the name.
                    if (count($tokens) > 1) {
                        $lastTok = strtolower(trim($tokens[count($tokens) - 1]));
                        if (preg_match('/^[a-z]$/', $lastTok)) {
                            $classLetter = $lastTok;
                            array_pop($tokens);
                        }
                    }

                    foreach ($tokens as $token) {
                        $tok = strtolower(trim($token));
                        if ($tok === '') continue;

                        $isSingleLetter = preg_match('/^[a-z]$/', $tok);
                        $isTimeLike = preg_match('/[0-9]/', $tok);
                        $isJenjang = $this->codeCAD($tok) !== null;
                        $days = ['senin','selasa','rabu','kamis','jumat','jum\'at','sabtu','minggu'];
                        $isDay = in_array($tok, $days, true);

                        // If token is not a class letter, not a time, not a jenjang code, and not a day,
                        // treat it as part of a name/mapel phrase. Otherwise treat as other token.
                        if (!$isSingleLetter && !$isTimeLike && !$isJenjang && !$isDay) {
                            $nameParts[] = $tok;
                        } else {
                            $otherTokens[] = $tok;
                        }
                    }

                // If we have name parts assemble phrase and require it matches guru.nama (AND)
                if (count($nameParts) > 0) {
                    $namePhrase = implode(' ', $nameParts);
                    $query->whereHas('guru', function($g) use ($namePhrase) {
                        $g->whereRaw('LOWER(nama) LIKE ?', ["%{$namePhrase}%"])
                          ->orWhereRaw('LOWER(mapel) LIKE ?', ["%{$namePhrase}%"]);
                    });
                }
                
                    // If we captured an explicit class letter at the end, require walas.namakelas to match it
                    if ($classLetter) {
                        $query->whereHas('walas', function($w) use ($classLetter) {
                            $w->where(function($ww) use ($classLetter) {
                                $ww->whereRaw('LOWER(namakelas) LIKE ?', ["% {$classLetter}%"]) 
                                   ->orWhereRaw('LOWER(namakelas) LIKE ?', ["%{$classLetter}-%"]) 
                                   ->orWhereRaw('LOWER(namakelas) LIKE ?', ["%{$classLetter}%"]);
                            });
                        });
                    }

                // Apply each other token as AND condition where it must match at least one column
                foreach ($otherTokens as $tok) {
                    $query->where(function($qtoken) use ($tok) {
                        $qtoken->whereHas('guru', function($g) use ($tok) {
                            $g->whereRaw('LOWER(nama) LIKE ?', ["%{$tok}%"]) 
                              ->orWhereRaw('LOWER(mapel) LIKE ?', ["%{$tok}%"]);
                        })
                        ->orWhereHas('walas', function($w) use ($tok) {
                            $code = $this->codeCAD($tok);
                            if (preg_match('/^[a-z]$/', $tok)) {
                                $w->where(function($ww) use ($tok, $code) {
                                    $ww->whereRaw('LOWER(namakelas) LIKE ?', ["% {$tok}%"]) 
                                       ->orWhereRaw('LOWER(namakelas) LIKE ?', ["%{$tok}-%"]) 
                                       ->orWhereRaw('LOWER(namakelas) LIKE ?', ["%{$tok}%"]);

                                    if ($code) {
                                        $ww->orWhere('jenjang', '=', $code);
                                    } else {
                                        $ww->orWhereRaw('LOWER(jenjang) LIKE ?', ["%{$tok}%"]);
                                    }
                                });
                            } else {
                                $w->whereRaw('LOWER(namakelas) LIKE ?', ["%{$tok}%"]);
                                if ($code) {
                                    $w->orWhere('jenjang', '=', $code);
                                } else {
                                    $w->orWhereRaw('LOWER(jenjang) LIKE ?', ["%{$tok}%"]);
                                }
                            }
                        })
                        ->orWhereRaw('LOWER(hari) LIKE ?', ["%{$tok}%"])
                        ->orWhere('mulai', 'like', "%{$tok}%")
                        ->orWhere('mulai', 'like', "%" . str_replace('.', ':', $tok) . "%")
                        ->orWhere('selesai', 'like', "%{$tok}%")
                        ->orWhere('selesai', 'like', "%" . str_replace('.', ':', $tok) . "%");
                    });
                }
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
