<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EstadoAccionEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EstadoAccion;
use App\Models\HistorialEstadoAccion;
use App\Models\UsuarioInstitucional;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<HistorialEstadoAccion> */
class HistorialEstadoAccionFactory extends Factory
{
    protected $model = HistorialEstadoAccion::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'idAccion' => AccionSustentabilidad::factory()->pendienteDeRevision(),
            'idEstadoAccion' => static fn (): int => EstadoAccion::idPara(
                EstadoAccionEnum::PendienteDeRevision,
            ),
            'idUsuarioResponsable' => UsuarioInstitucional::factory()->coordinadora(),
            'fechaCambio' => CarbonImmutable::now(),
            'motivo' => fake()->sentence(),
        ];
    }
}
