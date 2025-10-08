<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin;
use App\Models\siswa;
use App\Models\guru;
use Illuminate\Support\Facades\Hash;

class adminController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

    public function formLogin()
    {
        return view('login');
    }

    public function prosesLogin(Request $request)
    {
        $admin = admin::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            // simpan ke session
            session([
                'admin_id' => $admin->id,
                'admin_username' => $admin->username,
                'admin_role' => $admin->role
            ]);
            return redirect()->route('home');
        }
        return back()->with('error', 'Username atau password salah.');
    }

    public function logout()
    {
        //hapus session
        session()->forget(['admin_id', 'admin_username', 'admin_role']);
        return redirect()->route('landing');
    }

    public function formRegister()
    {
        return view('register');
    }

    public function prosesRegister(Request $request)
    {
        try {
            // validasi umum
            $request->validate([
                'username' => 'required|string|max:50|unique:dataadmin,username',
                'password' => 'required|string|min:4',
                'role' => 'required|string|in:admin,guru,siswa',
            ]);

            // simpan ke tabel dataadmin
            $admin = admin::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // jika role siswa → simpan detail ke tabel datasiswa
            if ($request->role === 'siswa') {
                $request->validate([
                    'nama' => 'required|string|max:100',
                    'tb'   => 'required|numeric',
                    'bb'   => 'required|numeric',
                ]);

                siswa::create([
                    'id'   => $admin->id,  // foreign key ke dataadmin
                    'nama' => $request->nama,
                    'tb'   => $request->tb,
                    'bb'   => $request->bb,
                ]);
            }

            // jika role guru → simpan detail ke tabel dataguru
            if ($request->role === 'guru') {
                $request->validate([
                    'nama'  => 'required|string|max:100',
                    'mapel' => 'required|string|max:100',
                ]);

                guru::create([
                    'id'    => $admin->id, // foreign key ke dataadmin
                    'nama'  => $request->nama,
                    'mapel' => $request->mapel,
                ]);
            }

            return redirect()->route('formLogin')->with('success', 'Registrasi berhasil! Silakan login.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Registrasi gagal: ' . $e->getMessage());
        }
    }
public function home()
{
    $role = session('admin_role');
    $id   = session('admin_id');

    $siswa = \App\Models\Siswa::with(['kelas.walas.guru'])->get();

    $guru = null;
    $siswaLogin = null;
    $jadwals = null;
    $kelasData = null;
    $siswaData = null;

    if ($role === 'admin') {
        // Admin melihat semua jadwal
        $jadwals = \App\Models\kbm::with(['guru', 'walas'])->get();
    } elseif ($role === 'guru') {
        // Data guru untuk tampilan profil
        $guru = \App\Models\Guru::where('id', $id)
                ->with(['walas.kelas.siswa'])
                ->first();
        
        // Jadwal mengajar guru
        if ($guru) {
            $jadwals = \App\Models\kbm::with(['guru', 'walas'])
                ->where('idguru', $guru->idguru)
                ->get();
        }
    } elseif ($role === 'siswa') {
        // Data siswa untuk tampilan profil
        $siswaLogin = \App\Models\Siswa::where('id', $id)
                       ->with(['kelas.walas.guru'])
                       ->first();
        
        // Data untuk jadwal KBM
        if ($siswaLogin && $siswaLogin->kelas) {
            $siswaData = $siswaLogin;
            $kelasData = $siswaLogin->kelas->walas;
            $jadwals = \App\Models\kbm::with(['guru', 'walas'])
                ->where('idwalas', $siswaLogin->kelas->idwalas)
                ->get();
        }
    }

    return view('home', compact('siswa', 'guru', 'siswaLogin', 'jadwals', 'siswaData', 'kelasData'));
}



}
