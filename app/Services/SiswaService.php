<?php

namespace App\Services;

use App\Repositories\SiswaRepository;

class SiswaService
{
    protected $repo;

    public function __construct(SiswaRepository $repo)
    {
        $this->repo = $repo;
    }

    public function createSiswa(array $data)
    {
        return $this->repo->create($data);
    }

    public function getSiswaById($id)
    {
        return $this->repo->findById($id);
    }

    public function updateSiswa($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteSiswa($id)
    {
        return $this->repo->delete($id);
    }
}
