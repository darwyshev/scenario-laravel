<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\admin>
 */
class adminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName,
            'password' => Hash::make('123'),
            'role'     => 'siswa',
        ];
    }
    public function dataadmin1()
    {
        return $this->state([
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'admin',
        ]);
    }
    public function dataadmin2()
    {
        return $this->state([
            'username' => 'guru',
            'password' => Hash::make('guru'),
            'role' => 'guru',
        ]);
    }
}
