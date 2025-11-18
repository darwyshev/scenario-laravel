<?php

namespace App\Repositories;

use App\Models\Siswa;
use App\Models\Admin;

class SiswaRepository
{
    public function create(array $data)
    {
        $admin = Admin::create([
            'username' => $data['nama'],
            'password' => bcrypt($data['nama']),
            'role' => 'siswa',
        ]);

        $siswa = Siswa::create([
            'id' => $admin->id,
            'nama' => $data['nama'],
            'tb' => $data['tb'],
            'bb' => $data['bb'],
        ]);

        return $siswa;
    }

    public function findById($id)
    {
        return Siswa::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->update($data);
        return $siswa;
    }

    public function delete($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();
        return true;
    }
}
