<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin; 

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\konten>
 */
class guruFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Admin::factory()->create(['role' => 'guru'])->id,
            'nama' => $this->faker->name,
            'mapel' => $this->faker->randomElement(['Matematika','IPAS','Bahasa Indonesia','Informatika']),
        ];
    }
}
