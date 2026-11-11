<?php

namespace Database\Factories;

use App\Models\AccionSustentabilidad;
use App\Models\Participacion;
use App\Models\UsuarioInstitucional;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Participacion> */
class ParticipacionFactory extends Factory
{
    public function definition(): array
    {
        return ['fechaInscripcion' => now(), 'idUsuario' => UsuarioInstitucional::factory(), 'idAccion' => AccionSustentabilidad::factory()];
    }
}
