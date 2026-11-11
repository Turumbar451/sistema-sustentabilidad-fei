<?php

namespace Database\Factories;

use App\Enums\EstadoDictamenEnum;
use App\Models\AccionSustentabilidad;
use App\Models\Dictamen;
use App\Models\UsuarioInstitucional;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Dictamen> */
class DictamenFactory extends Factory
{
    public function definition(): array
    {
        return ['estadoDictamen' => fake()->randomElement(EstadoDictamenEnum::cases()), 'comentario' => fake('es_MX')->paragraph(), 'fechaDictamen' => now(), 'idAccion' => AccionSustentabilidad::factory(), 'idUsuarioEmisor' => UsuarioInstitucional::factory()];
    }
}
