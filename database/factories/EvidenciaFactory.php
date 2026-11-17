<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AccionSustentabilidad;
use App\Models\Evidencia;
use App\Models\UsuarioInstitucional;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Evidencia> */
class EvidenciaFactory extends Factory
{
    protected $model = Evidencia::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'nombreArchivo' => 'evidencia-'.fake()->uuid().'.pdf',
            'ubicacionArchivo' => static fn (array $atributos): string => 'demo/evidencias/'.$atributos['nombreArchivo'],
            'tipoArchivo' => 'application/pdf',
            'descripcion' => 'Evidencia de ejemplo; la ruta no representa un archivo físico.',
            'fechaCarga' => CarbonImmutable::now(),
            'idAccion' => AccionSustentabilidad::factory()->finalizada(),
            'idUsuarioCarga' => UsuarioInstitucional::factory()->maestro(),
        ];
    }
}
