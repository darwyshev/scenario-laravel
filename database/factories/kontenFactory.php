<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class kontenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'judul' => $this->faker->unique()->sentence(rand(4, 8)),
            'isi'   => $this->faker->paragraphs(rand(2, 4), true),
            'detil' => $this->faker->paragraphs(rand(6, 8), true),
        ];
    }
}
