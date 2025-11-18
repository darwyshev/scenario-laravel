<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSiswaRequest;
use Illuminate\Http\Request;
use App\Services\SiswaService;

class siswaController extends Controller
{
    protected $service;

    public function __construct(SiswaService $service)
    {
        $this->service = $service;
    }

    public function home()
    {
        return view('home');
    }

    public function getData()
    {
        $siswa = \App\Models\Siswa::all();
        return response()->json($siswa);
    }

    public function create()
    {
        return view('siswa.create');
    }

    public function store(StoreSiswaRequest $request)
    {
        $this->service->createSiswa($request->validated());
        return redirect()->route('home')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $siswa = $this->service->getSiswaById($id);
        return view('siswa.edit', compact('siswa'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'tb' => 'required|numeric|min:30|max:250',
            'bb' => 'required|numeric|min:10|max:200',
        ]);

        $this->service->updateSiswa($id, $validated);
        return redirect()->route('home')->with('success', 'Data siswa berhasil diupdate!');
    }

    public function destroy($id)
    {
        $this->service->deleteSiswa($id);
        return redirect()->route('home')->with('success', 'Data siswa berhasil dihapus!');
    }
}
