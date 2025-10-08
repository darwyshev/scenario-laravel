<?php

namespace Database\Factories;

use App\Models\Guru;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalasFactory extends Factory
{
    public function definition()
    {
        return [
            'jenjang' => $this->faker->randomElement(['X', 'XI', 'XII']),
            'namakelas' => $this->faker->randomElement(['A', 'B', 'C']),
            'tahunajaran' => '2025/2026',
            'idguru' => Guru::factory(),
        ];
    }
}
