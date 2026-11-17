<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EstadoAccionEnum;
use App\Models\EstadoAccion;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EstadoAccion> */
class EstadoAccionFactory extends Factory
{
    protected $model = EstadoAccion::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement(EstadoAccionEnum::cases()),
            'descripcion' => fake()->optional()->sentence(),
        ];
    }
}
