<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin;
use App\Models\siswa;
use App\Models\guru;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;


class adminController extends Controller
{
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function landing()
    {
        return view('landing');
    }

    public function formLogin()
    {
        return view('login');
    }

    public function prosesLogin(LoginRequest $request)
    {
        $admin = $this->service->login($request->validated());

        if (!$admin) {
            return back()->with('error', 'Username atau password salah.');
        }

        session([
            'admin_id' => $admin->id,
            'admin_username' => $admin->username,
            'admin_role' => $admin->role
        ]);

        return redirect()->route('home');
    }

    public function logout()
    {
        session()->forget(['admin_id', 'admin_username', 'admin_role']);
        return redirect()->route('landing');
    }

    public function formRegister()
    {
        return view('register');
    }

    public function prosesRegister(RegisterRequest $request)
    {
        $this->service->register($request->validated());
        return redirect()->route('formLogin')->with('success', 'Registrasi berhasil! Silakan login.');
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
