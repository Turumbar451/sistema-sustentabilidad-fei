<?php

namespace Database\Factories;

use App\Models\UsuarioInstitucional;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UsuarioInstitucional> */
class UsuarioInstitucionalFactory extends Factory
{
    public function definition(): array
    {
        return ['identificadorInstitucional' => fake()->unique()->numerify('UV########'), 'nombreCompleto' => fake('es_MX')->name(), 'correoInstitucional' => fake()->unique()->safeEmail(), 'telefonoContacto' => fake()->optional()->numerify('228#######'), 'password' => bcrypt('password')];
    }
}
