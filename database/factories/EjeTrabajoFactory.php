<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EjeTrabajo;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EjeTrabajo> */
class EjeTrabajoFactory extends Factory
{
    protected $model = EjeTrabajo::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->words(3, true),
            'descripcion' => fake()->optional()->sentence(),
        ];
    }
}
