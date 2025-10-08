<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\siswa;
use App\Models\admin;

class siswaController extends Controller
{
    //
    public function home()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('login');
        }
        $siswa = siswa::all();
        return view('home', compact('siswa'));
    }

    public function create()
    {
        return view('siswa.create');
    }

    public function store(Request $request)
    {
        try {
            // validasi input
            $request->validate([
                'username' => 'required|string|max:50|unique:dataadmin,username',
                'password' => 'required|string|min:4',
                'nama' => 'required|string|max:100',
                'tb' => 'required|numeric',
                'bb' => 'required|numeric',
            ]);

            // buat akun admin dengan role siswa
            $admin = admin::create([
                'username' => $request->username,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => 'siswa',
            ]);

            // buat data siswa dengan foreign key ke admin
            siswa::create([
                'id' => $admin->id, // foreign key ke dataadmin
                'nama' => $request->nama,
                'tb' => $request->tb,
                'bb' => $request->bb,
            ]);
            
            return redirect()->route('home')->with('success', 'Siswa berhasil ditambahkan dengan akun login');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambah siswa: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $siswa = siswa::where('idsiswa', $id)->firstOrFail();
        return view('siswa.edit', compact('siswa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tb' => 'required|numeric',
            'bb' => 'required|numeric',
        ]);

        $siswa = siswa::where('idsiswa', $id)->firstOrFail();
        $siswa->update([
            'nama' => $request->nama,
            'tb' => $request->tb,
            'bb' => $request->bb,
        ]);
        
        return redirect()->route('home')->with('success', 'Data siswa berhasil diupdate');
    }

    public function destroy($id)
    {
        $siswa = siswa::where('idsiswa', $id)->firstOrFail();
        $siswa->delete();
        return redirect()->route('home')->with('success', 'Data siswa berhasil dihapus');
    }
}