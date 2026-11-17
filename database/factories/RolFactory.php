<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RolSistemaEnum;
use App\Models\Rol;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Rol> */
class RolFactory extends Factory
{
    protected $model = Rol::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement(RolSistemaEnum::cases()),
            'descripcion' => fake()->sentence(),
        ];
    }
}
