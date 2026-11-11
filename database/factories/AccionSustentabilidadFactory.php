<?php

namespace Database\Factories;

use App\Enums\EstadoAccionEnum;
use App\Enums\ModalidadAccionEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use App\Models\UsuarioInstitucional;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AccionSustentabilidad> */
class AccionSustentabilidadFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 week', '+3 months');

        return ['folio' => 'FEI-'.now()->year.'-'.fake()->unique()->numerify('###'), 'nombre' => fake('es_MX')->sentence(4),
            'descripcion' => fake('es_MX')->paragraph(),
            'modalidad' => fake()->randomElement(ModalidadAccionEnum::cases()),
            'fechaRegistro' => now(),
            'fechaInicio' => $start->format('Y-m-d'),
            'fechaTermino' => (clone $start)->modify('+1 day')->format('Y-m-d'),
            'requerimientos' => fake()->optional()->sentence(),
            'estadoActual' => fake()->randomElement(EstadoAccionEnum::cases()),
            'idEjeTrabajo' => EjeTrabajo::factory(),
            'idUsuarioSolicitante' => UsuarioInstitucional::factory()];
    }
}
