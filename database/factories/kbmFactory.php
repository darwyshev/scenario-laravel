<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\guru;
use App\Models\Walas;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\kbm>
 */
class kbmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $guruIds = guru::pluck('idguru')->toArray();
        $kelasIds = Walas::pluck('idwalas')->toArray();

        return [
            'idguru' => $this->faker->randomElement($guruIds),
            'idwalas' => $this->faker->randomElement($kelasIds),
            'hari' => $this->faker->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']),
            'mulai' => $this->faker->randomElement(['07:00', '08:30', '10:00', '11:30', '13:00']),
            'selesai' => $this->faker->randomElement(['08:30', '10:00', '11:30', '13:00', '14:30']),
        ];
    }
}
