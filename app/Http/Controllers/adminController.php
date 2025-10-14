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
        
        // Validate credentials using guard or attempt method
        $validCredentials = $admin && Hash::check($request->password, $admin->password);
        
        if (!$validCredentials) {
            return back()->with('error', 'Username atau password salah.');
        }
        
        // Set session data
        session([
            'admin_id' => $admin->id,
            'admin_username' => $admin->username,
            'admin_role' => $admin->role
        ]);
        
        return redirect()->route('home');
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

            // Validate and create role-specific data
            match($request->role) {
                'siswa' => $this->createSiswaData($request, $admin->id),
                'guru' => $this->createGuruData($request, $admin->id),
                default => null,
            };

            return redirect()->route('formLogin')->with('success', 'Registrasi berhasil! Silakan login.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Registrasi gagal: ' . $e->getMessage());
        }
    }
public function home()
{
    $role = session('admin_role');
    $userId = session('admin_id');
    $data = [];
    
    // Load role-specific data
    switch($role) {
        case 'admin':
            $data['siswa'] = \App\Models\Siswa::with(['kelas.walas.guru'])->get();
            $data['jadwals'] = \App\Models\kbm::with(['guru', 'walas'])->get();
            break;
            
        case 'guru':
            $guru = \App\Models\guru::where('id', $userId)
                ->with(['walas.kelas.siswa'])
                ->first();
                
            $data['guru'] = $guru;
            if ($guru) {
                $data['jadwals'] = \App\Models\kbm::with(['guru', 'walas'])
                    ->where('idguru', $guru->idguru)
                    ->get();
            }
            break;
            
        case 'siswa':
            $siswa = \App\Models\Siswa::where('id', $userId)
                ->with(['kelas.walas.guru'])
                ->first();
                
            $data['siswaLogin'] = $siswa;
            if ($siswa && $siswa->kelas) {
                $data['jadwals'] = \App\Models\kbm::with(['guru', 'walas'])
                    ->where('idwalas', $siswa->kelas->idwalas)
                    ->get();
                $data['kelasData'] = $siswa->kelas->walas;
                $data['waliKelas'] = $siswa->kelas->walas->guru;
            }
            break;
    }

    return view('home', $data);
}

private function createSiswaData(Request $request, $adminId)
{
    $request->validate([
        'nama' => 'required|string|max:100',
        'tb'   => 'required|numeric',
        'bb'   => 'required|numeric',
    ]);

    return siswa::create([
        'id'   => $adminId,
        'nama' => $request->nama,
        'tb'   => $request->tb,
        'bb'   => $request->bb,
    ]);
}

private function createGuruData(Request $request, $adminId)
{
    $request->validate([
        'nama'  => 'required|string|max:100',
        'mapel' => 'required|string|max:100',
    ]);

    return guru::create([
        'id'    => $adminId,
        'nama'  => $request->nama,
        'mapel' => $request->mapel,
    ]);
}

}
