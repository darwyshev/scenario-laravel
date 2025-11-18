<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $repo;

    public function __construct(AuthRepository $repo)
    {
        $this->repo = $repo;
    }

    public function login(array $credentials)
    {
        $admin = $this->repo->findByUsername($credentials['username']);

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return null;
        }

        return $admin;
    }

    public function register(array $data)
    {
        $admin = $this->repo->createAdmin($data);

        // Buat data tambahan sesuai role
        switch ($data['role']) {
            case 'siswa':
                $this->repo->createSiswa($data, $admin->id);
                break;
            case 'guru':
                $this->repo->createGuru($data, $admin->id);
                break;
        }

        return $admin;
    }
}
