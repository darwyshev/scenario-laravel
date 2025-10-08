<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Walas;
use App\Models\Kelas;
use App\Models\Konten;
use App\Models\kbm;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Data Admin ---
        // username: admin, password: admin
        Admin::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'role'     => 'admin',
        ]);

        // username: guru, password: guru
        Admin::factory()->create([
            'username' => 'guru',
            'password' => bcrypt('guru'),
            'role'     => 'guru',
        ]);

        // Membuat 5 data konten dummy
        Konten::factory()->count(5)->create();

        // --- Tambahan dari skenario ---
        // Membuat 5 guru
        $gurus = Guru::factory(5)->create();

        // Membuat 25 siswa
        $siswas = Siswa::factory(25)->create();

        // Ambil 3 guru random jadi walas
        $guruRandom = $gurus->random(3);

        foreach ($guruRandom as $guru) {
            Walas::factory()->create([
                'idguru' => $guru->idguru
            ]);
        }

        // Ambil semua id walas
        $waliKelasIds = Walas::pluck('idwalas')->toArray();

        // Acak urutan siswa
        $randomSiswas = $siswas->shuffle();

        // Bagi siswa ke dalam kelompok sesuai jumlah walas
        $chunks = $randomSiswas->chunk(
            ceil($randomSiswas->count() / count($waliKelasIds))
        );

        // Distribusikan siswa ke tiap wali kelas
        foreach ($waliKelasIds as $index => $idwalas) {
            if (isset($chunks[$index])) {
                foreach ($chunks[$index] as $siswa) {
                    Kelas::create([
                        'idwalas' => $idwalas,
                        'idsiswa' => $siswa->idsiswa,
                    ]);
                }
            }
        }

        // Membuat 5 data KBM (jadwal pelajaran)
        kbm::factory(25)->create();
    }
}
