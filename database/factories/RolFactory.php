<?php

namespace Database\Factories;

use App\Enums\RolSistemaEnum;
use App\Models\Rol;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Rol> */
class RolFactory extends Factory
{
    public function definition(): array
    {
        return ['nombre' => fake()->unique()->randomElement(RolSistemaEnum::cases()), 'descripcion' => fake()->sentence()];
    }
}
