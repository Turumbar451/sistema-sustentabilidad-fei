<?php

namespace Database\Factories;

use App\Enums\TipoEvidenciaEnum;
use App\Models\AccionSustentabilidad;
use App\Models\Evidencia;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Evidencia> */
class EvidenciaFactory extends Factory
{
    public function definition(): array
    {
        return ['archivo' => 'evidencias/'.fake()->uuid().'.jpg', 'tipoArchivo' => fake()->randomElement(TipoEvidenciaEnum::cases()), 'descripcion' => fake('es_MX')->sentence(), 'idAccion' => AccionSustentabilidad::factory()];
    }
}
