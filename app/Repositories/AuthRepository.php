<?php

namespace App\Repositories;

use App\Models\admin;
use App\Models\siswa;
use App\Models\guru;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function findByUsername(string $username)
    {
        return admin::where('username', $username)->first();
    }

    public function createAdmin(array $data)
    {
        return admin::create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);
    }

    public function createSiswa(array $data, $adminId)
    {
        return siswa::create([
            'id' => $adminId,
            'nama' => $data['nama'],
            'tb' => $data['tb'],
            'bb' => $data['bb'],
        ]);
    }

    public function createGuru(array $data, $adminId)
    {
        return guru::create([
            'id' => $adminId,
            'nama' => $data['nama'],
            'mapel' => $data['mapel'],
        ]);
    }
}
