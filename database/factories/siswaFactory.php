<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\siswa>
 */
class siswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Admin::factory()->create(['role' => 'siswa'])->id,
            'nama' => $this->faker->name,
            'tb' => $this->faker->numberBetween(140, 180),
            'bb' => $this->faker->numberBetween(35, 80)
        ];
    }
}
