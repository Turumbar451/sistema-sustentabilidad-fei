<?php

declare(strict_types=1);

use App\Enums\EstadoAccionEnum;
use App\Enums\FormaTrabajoEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EstadoAccion;
use App\Models\Participacion;
use App\Models\UsuarioInstitucional;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(LazilyRefreshDatabase::class);

it('actualiza el estado y registra fecha y motivo con responsable opcional', function (bool $conResponsable) {
    $this->travelTo('2026-11-12 10:00:00');
    $usuario = UsuarioInstitucional::factory()->create();
    $accion = AccionSustentabilidad::factory()->pendienteDeRevision()->create([
        'idUsuarioSolicitante' => $usuario->idUsuario,
    ]);
    $accion->load('estadoAccion');
    $idAprobada = EstadoAccion::idPara(EstadoAccionEnum::Aprobada);
    $idResponsable = $conResponsable ? $usuario->idUsuario : null;

    $accion->registrarCambioEstado($idAprobada, $idResponsable, 'Requisitos completos.');

    $this->assertDatabaseHas('AccionSustentabilidad', [
        'idAccion' => $accion->idAccion,
        'idEstadoAccion' => $idAprobada,
    ]);
    $this->assertDatabaseCount('HistorialEstadoAccion', 1);
    $this->assertDatabaseHas('HistorialEstadoAccion', [
        'idAccion' => $accion->idAccion,
        'idEstadoAccion' => $idAprobada,
        'idUsuarioResponsable' => $idResponsable,
        'fechaCambio' => '2026-11-12 10:00:00',
        'motivo' => 'Requisitos completos.',
    ]);
    expect($accion->idEstadoAccion)->toBe($idAprobada);
    expect($accion->estadoAccion->nombre)->toBe(EstadoAccionEnum::Aprobada);
})->with([
    'responsable identificado' => [true],
    'responsable nulo' => [false],
]);

it('conserva el estado anterior y no deja historial cuando falla la referencia del responsable', function () {
    $accion = AccionSustentabilidad::factory()->pendienteDeRevision()->create();
    $idAnterior = $accion->idEstadoAccion;
    $idAprobada = EstadoAccion::idPara(EstadoAccionEnum::Aprobada);

    expect(fn () => $accion->registrarCambioEstado($idAprobada, 2147483647, 'No debe persistirse.'))
        ->toThrow(QueryException::class);

    $this->assertDatabaseHas('AccionSustentabilidad', [
        'idAccion' => $accion->idAccion,
        'idEstadoAccion' => $idAnterior,
    ]);
    $this->assertDatabaseCount('HistorialEstadoAccion', 0);
    expect($accion->idEstadoAccion)->toBe($idAnterior);
});

it('mantiene el historial y el último estado al cambiar desde dos instancias previamente cargadas', function () {
    $this->travelTo('2026-11-12 10:00:00');
    $primera = AccionSustentabilidad::factory()->pendienteDeRevision()->create();
    $segunda = AccionSustentabilidad::buscarPorId($primera->idAccion);
    $idAprobada = EstadoAccion::idPara(EstadoAccionEnum::Aprobada);
    $idEnEjecucion = EstadoAccion::idPara(EstadoAccionEnum::EnEjecucion);

    $primera->registrarCambioEstado($idAprobada, null, 'Autorización.');
    $this->travelTo('2026-11-12 11:00:00');
    $segunda->registrarCambioEstado($idEnEjecucion, null, 'Inicio de actividad.');

    $this->assertDatabaseHas('AccionSustentabilidad', [
        'idAccion' => $primera->idAccion,
        'idEstadoAccion' => $idEnEjecucion,
    ]);
    expect($segunda->historialEstados()->pluck('idEstadoAccion')->all())
        ->toBe([$idAprobada, $idEnEjecucion]);
    expect($segunda->idEstadoAccion)->toBe($idEnEjecucion);
});

it('publica únicamente acciones aprobadas en ejecución o finalizadas', function () {
    $solicitante = UsuarioInstitucional::factory()->create();
    $base = AccionSustentabilidad::factory()->recycle($solicitante);
    $aprobada = $base->aprobada()->create();
    $enEjecucion = $base->enEjecucion()->create();
    $finalizada = $base->finalizada()->create();
    $base->pendienteDeRevision()->create();
    $base->correccionesSolicitadas()->create();
    $base->rechazada()->create();

    $publicadas = AccionSustentabilidad::obtenerAccionesPublicadas();

    expect($publicadas->modelKeys())->toEqualCanonicalizing([
        $aprobada->idAccion,
        $enEjecucion->idAccion,
        $finalizada->idAccion,
    ]);
});

it('filtra publicaciones por búsqueda modalidad forma de trabajo o estado', function (string $campo, mixed $valor) {
    $solicitante = UsuarioInstitucional::factory()->create();
    $base = AccionSustentabilidad::factory()->recycle($solicitante);
    $objetivo = $base->finalizada()->create([
        'nombre' => 'Monitoreo de agua',
        'modalidad' => 'Presencial',
        'formaTrabajo' => FormaTrabajoEnum::Individual,
    ]);
    $base->aprobada()->create([
        'nombre' => 'Ahorro de energía',
        'modalidad' => 'Virtual',
        'formaTrabajo' => FormaTrabajoEnum::EquipoConformado,
    ]);

    $resultado = AccionSustentabilidad::obtenerAccionesPublicadas([$campo => $valor]);

    expect($resultado->modelKeys())->toBe([$objetivo->idAccion]);
})->with([
    'búsqueda q' => ['q', 'agua'],
    'alias buscar' => ['buscar', 'agua'],
    'modalidad individual' => ['modalidad', 'Presencial'],
    'lista de modalidades' => ['modalidad', ['Presencial']],
    'forma de trabajo texto' => ['formaTrabajo', 'Individual'],
    'forma de trabajo enum' => ['formaTrabajo', FormaTrabajoEnum::Individual],
    'estado texto' => ['estado', 'Finalizada'],
    'estado enum' => ['estado', EstadoAccionEnum::Finalizada],
]);

it('interpreta las comillas de la búsqueda como texto y no como instrucciones SQL', function () {
    $accion = AccionSustentabilidad::factory()->aprobada()->create([
        'nombre' => 'Monitoreo de agua',
    ]);

    $resultado = AccionSustentabilidad::obtenerAccionesPublicadas(['q' => "' OR 1=1 --"]);

    expect($resultado)->toHaveCount(0);
    $this->assertModelExists($accion);
});

it('mantiene acotadas las consultas al crecer el catálogo y entrega sus relaciones y participaciones', function () {
    $solicitante = UsuarioInstitucional::factory()->create();
    $base = AccionSustentabilidad::factory()->recycle($solicitante)->aprobada();
    $accion = $base->create();
    Participacion::factory()->create(['idAccion' => $accion->idAccion]);
    $prevencionAnterior = Model::preventsLazyLoading();
    Model::preventLazyLoading();
    DB::enableQueryLog();
    $consultar = static function (): array {
        DB::flushQueryLog();
        $acciones = AccionSustentabilidad::obtenerAccionesPublicadas();

        foreach ($acciones as $publicada) {
            $publicada->ejeTrabajo->nombre;
            $publicada->estadoAccion->nombre;
            $publicada->solicitante->nombreCompleto;
            $publicada->participaciones_count;
        }

        return [$acciones, count(DB::getQueryLog())];
    };

    try {
        [$iniciales, $consultasIniciales] = $consultar();
        DB::disableQueryLog();
        $base->count(5)->create();
        DB::enableQueryLog();
        [$ampliadas, $consultasAmpliadas] = $consultar();

        expect($iniciales->find($accion->idAccion)->participaciones_count)->toBe(1);
        expect($ampliadas)->toHaveCount(6);
        expect($consultasIniciales)->toBeLessThanOrEqual(4);
        expect($consultasAmpliadas)->toBe($consultasIniciales);
    } finally {
        DB::disableQueryLog();
        DB::flushQueryLog();
        Model::preventLazyLoading($prevencionAnterior);
    }
});
