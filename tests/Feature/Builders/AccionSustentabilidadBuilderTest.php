<?php

declare(strict_types=1);

use App\Enums\EstadoAccionEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use App\Models\EstadoAccion;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('conserva los filtros del catálogo por identificador de eje y de estado', function (string $campo) {
    $eje = EjeTrabajo::factory()->create();
    $otroEje = EjeTrabajo::factory()->create();
    $objetivo = AccionSustentabilidad::factory()->aprobada()->create(['idEjeTrabajo' => $eje->getKey()]);
    AccionSustentabilidad::factory()->finalizada()->create(['idEjeTrabajo' => $otroEje->getKey()]);
    $valor = $campo === 'idEjeTrabajo' ? $eje->getKey() : EstadoAccion::idPara(EstadoAccionEnum::Aprobada);

    $acciones = AccionSustentabilidad::obtenerAccionesPublicadas([$campo => $valor]);

    expect($acciones->modelKeys())->toBe([$objetivo->getKey()]);
})->with(['idEjeTrabajo', 'idEstadoAccion']);

it('conserva el alias busqueda en las consultas del modelo', function () {
    $objetivo = AccionSustentabilidad::factory()->aprobada()->create(['nombre' => 'Monitoreo de agua']);
    AccionSustentabilidad::factory()->aprobada()->create(['nombre' => 'Consumo de energía']);

    $acciones = AccionSustentabilidad::obtenerAccionesPublicadas(['busqueda' => 'agua']);

    expect($acciones->modelKeys())->toBe([$objetivo->getKey()]);
});

it('incluye todo el día solicitado en los límites del intervalo de registro', function () {
    $acciones = AccionSustentabilidad::factory()->aprobada()->count(4)->sequence(
        ['fechaRegistro' => '2026-11-01 23:59:59'],
        ['fechaRegistro' => '2026-11-02 00:00:00'],
        ['fechaRegistro' => '2026-11-02 23:59:59'],
        ['fechaRegistro' => '2026-11-03 00:00:00'],
    )->create();

    $resultado = AccionSustentabilidad::obtenerAccionesPublicadas([
        'fechaRegistroDesde' => '2026-11-02',
        'fechaRegistroHasta' => '2026-11-02',
    ]);

    expect($resultado->modelKeys())->toBe([$acciones[2]->getKey(), $acciones[1]->getKey()]);
});

it('conserva el rechazo de criterios inválidos antes de ejecutar la consulta', function (array $filtros, string $mensaje) {
    $accion = AccionSustentabilidad::factory()->aprobada()->create();

    expect(fn () => AccionSustentabilidad::obtenerAccionesPublicadas($filtros))
        ->toThrow(InvalidArgumentException::class, $mensaje);

    $this->assertModelExists($accion);
    $this->assertDatabaseCount('AccionSustentabilidad', 1);
})->with([
    'criterio desconocido' => [['desconocido' => true], 'El catálogo recibió un filtro desconocido.'],
    'fecha inexistente' => [['fechaRegistroDesde' => '2026-02-30'], 'Las fechas del filtro deben usar el formato YYYY-MM-DD y ser válidas.'],
    'intervalo invertido' => [[
        'fechaRegistroDesde' => '2026-12-02',
        'fechaRegistroHasta' => '2026-12-01',
    ], 'La fecha inicial del filtro excede la fecha final.'],
    'estado negativo' => [['idEstadoAccion' => -1], 'Los identificadores deben ser enteros positivos.'],
    'modalidad vacía' => [['modalidad' => ['']], 'Las modalidades deben ser cadenas no vacías.'],
]);
