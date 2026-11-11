<?php

namespace Database\Factories;

use App\Models\EjeTrabajo;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EjeTrabajo> */
class EjeTrabajoFactory extends Factory
{
    public function definition(): array
    {
        return ['nombre' => fake()->unique()->words(3, true), 'color_tema' => fake()->randomElement(['green', 'amber', 'blue'])];
    }
}
