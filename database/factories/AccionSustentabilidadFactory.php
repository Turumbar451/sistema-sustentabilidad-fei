<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EstadoAccionEnum;
use App\Enums\FormaTrabajoEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use App\Models\EstadoAccion;
use App\Models\UsuarioInstitucional;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AccionSustentabilidad> */
class AccionSustentabilidadFactory extends Factory
{
    protected $model = AccionSustentabilidad::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $registro = CarbonImmutable::now()->subDays(30)->startOfDay()->setHour(9);

        return [
            'folio' => 'FEI-'.$registro->year.'-'.fake()->unique()->numerify('########'),
            'nombre' => fake()->sentence(5),
            'descripcion' => fake()->paragraph(),
            'modalidad' => fake()->randomElement(['Presencial', 'Virtual', 'Híbrida']),
            'formaTrabajo' => fake()->randomElement(FormaTrabajoEnum::cases()),
            'fechaRegistro' => $registro,
            'fechaInicio' => $registro->addDays(14)->toDateString(),
            'fechaTermino' => $registro->addDays(16)->toDateString(),
            'requerimientos' => fake()->optional()->sentence(),
            'idEjeTrabajo' => static fn (): int => EjeTrabajo::idPara(
                'Gestion integrada de agua y residuos',
            ),
            'idUsuarioSolicitante' => UsuarioInstitucional::factory()->maestro(),
            'idEstadoAccion' => static fn (): int => EstadoAccion::idPara(
                EstadoAccionEnum::PendienteDeRevision,
            ),
        ];
    }

    public function enEstado(EstadoAccionEnum $estado): static
    {
        return $this->state(fn (): array => [
            'idEstadoAccion' => static fn (): int => EstadoAccion::idPara($estado),
        ]);
    }

    public function pendienteDeRevision(): static
    {
        return $this->enEstado(EstadoAccionEnum::PendienteDeRevision);
    }

    public function correccionesSolicitadas(): static
    {
        return $this->enEstado(EstadoAccionEnum::CorreccionesSolicitadas);
    }

    public function aprobada(): static
    {
        return $this->enEstado(EstadoAccionEnum::Aprobada);
    }

    public function rechazada(): static
    {
        return $this->enEstado(EstadoAccionEnum::Rechazada);
    }

    public function enEjecucion(): static
    {
        return $this->enEstado(EstadoAccionEnum::EnEjecucion);
    }

    public function finalizada(): static
    {
        return $this->enEstado(EstadoAccionEnum::Finalizada);
    }
}
