<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AccionSustentabilidad;
use App\Models\Participacion;
use App\Models\UsuarioInstitucional;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Participacion> */
class ParticipacionFactory extends Factory
{
    protected $model = Participacion::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'fechaInscripcion' => CarbonImmutable::now(),
            'idUsuario' => UsuarioInstitucional::factory()->estudiante(),
            'idAccion' => AccionSustentabilidad::factory()->aprobada(),
        ];
    }
}
