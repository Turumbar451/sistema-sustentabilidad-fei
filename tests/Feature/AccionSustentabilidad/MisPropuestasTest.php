<?php

declare(strict_types=1);

use App\Enums\EstadoAccionEnum;
use App\Enums\RolSistemaEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use App\Models\EstadoAccion;
use App\Models\UsuarioInstitucional;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('permite al solicitante consultar su listado y detalle privados con los roles autorizados', function (RolSistemaEnum $rol) {
    $usuario = UsuarioInstitucional::factory()->create();
    $usuario->asignarRol($rol);
    $eje = EjeTrabajo::factory()->create(['nombre' => 'Cuidado del agua']);
    $accion = AccionSustentabilidad::factory()->create([
        'idUsuarioSolicitante' => $usuario->getKey(),
        'idEjeTrabajo' => $eje->getKey(),
        'idEstadoAccion' => EstadoAccion::idPara(EstadoAccionEnum::PendienteDeRevision),
        'fechaRegistro' => '2026-11-18 10:00:00',
    ]);

    $this->actingAs($usuario)->get(route('acciones.mis-propuestas'))
        ->assertOk()
        ->assertSee($accion->folio)
        ->assertSee($accion->nombre)
        ->assertSee('Cuidado del agua')
        ->assertSee('Pendiente de revisión')
        ->assertSee('18/11/2026')
        ->assertSee(route('acciones.mis-propuestas.detalle', $accion));

    $this->get(route('acciones.mis-propuestas.detalle', $accion))
        ->assertOk()
        ->assertSee($accion->folio)
        ->assertSee($accion->nombre)
        ->assertSee('Pendiente de revisión')
        ->assertSee(route('acciones.mis-propuestas'));
})->with([
    'estudiante' => [RolSistemaEnum::Estudiante],
    'maestro' => [RolSistemaEnum::Maestro],
    'coordinadora' => [RolSistemaEnum::CoordinadoraSustentabilidad],
]);

it('devuelve 403 a usuarios sin un rol autorizado aunque sean propietarios de la propuesta', function (?RolSistemaEnum $rol) {
    $usuario = UsuarioInstitucional::factory()->create();

    if ($rol !== null) {
        $usuario->asignarRol($rol);
    }

    $accion = AccionSustentabilidad::factory()->create(['idUsuarioSolicitante' => $usuario->getKey()]);

    $this->actingAs($usuario)->get(route('acciones.mis-propuestas'))->assertForbidden();
    $this->get(route('acciones.mis-propuestas.detalle', $accion))->assertForbidden();
})->with([
    'sin rol institucional' => [null],
    'director' => [RolSistemaEnum::Director],
]);

it('devuelve 403 sin sesión fuera del entorno local en las dos rutas privadas', function () {
    $accion = AccionSustentabilidad::factory()->create();

    $this->get(route('acciones.mis-propuestas'))->assertForbidden();
    $this->get(route('acciones.mis-propuestas.detalle', $accion))->assertForbidden();
});

it('muestra todos los estados de las propuestas propias y excluye propuestas ajenas del listado', function () {
    $usuario = UsuarioInstitucional::factory()->estudiante()->create();
    $otroUsuario = UsuarioInstitucional::factory()->estudiante()->create();

    foreach (EstadoAccionEnum::cases() as $estado) {
        AccionSustentabilidad::factory()->create([
            'idUsuarioSolicitante' => $usuario->getKey(),
            'idEstadoAccion' => EstadoAccion::idPara($estado),
            'nombre' => 'Propuesta propia '.$estado->value,
        ]);
    }

    $ajena = AccionSustentabilidad::factory()->create([
        'idUsuarioSolicitante' => $otroUsuario->getKey(),
        'nombre' => 'Propuesta ajena confidencial',
    ]);

    $this->actingAs($usuario)->get(route('acciones.mis-propuestas'))
        ->assertOk()
        ->assertSee('Pendiente de revisión')
        ->assertSee('Correcciones solicitadas')
        ->assertSee('Aprobada')
        ->assertSee('Rechazada')
        ->assertSee('En ejecución')
        ->assertSee('Finalizada')
        ->assertDontSee($ajena->nombre)
        ->assertDontSee($ajena->folio);
});

it('devuelve 403 al solicitar el detalle privado de otra persona incluso siendo Coordinadora', function (RolSistemaEnum $rol) {
    $usuario = UsuarioInstitucional::factory()->create();
    $usuario->asignarRol($rol);
    $propietario = UsuarioInstitucional::factory()->estudiante()->create();
    $accion = AccionSustentabilidad::factory()->create(['idUsuarioSolicitante' => $propietario->getKey()]);

    $this->actingAs($usuario)->get(route('acciones.mis-propuestas.detalle', $accion))
        ->assertForbidden()
        ->assertDontSee($accion->descripcion);
})->with([
    'estudiante' => [RolSistemaEnum::Estudiante],
    'maestro' => [RolSistemaEnum::Maestro],
    'coordinadora' => [RolSistemaEnum::CoordinadoraSustentabilidad],
]);

it('presenta estado actual requerimientos y observaciones del historial en orden cronológico', function () {
    $usuario = UsuarioInstitucional::factory()->estudiante()->create(['nombreCompleto' => 'Estudiante Responsable']);
    $coordinadora = UsuarioInstitucional::factory()->create(['nombreCompleto' => 'Coordinadora Responsable']);
    $accion = AccionSustentabilidad::factory()->create([
        'idUsuarioSolicitante' => $usuario->getKey(),
        'idEstadoAccion' => EstadoAccion::idPara(EstadoAccionEnum::CorreccionesSolicitadas),
        'requerimientos' => 'Herramientas y material reutilizable.',
    ]);
    $accion->historialEstados()->create([
        'idEstadoAccion' => EstadoAccion::idPara(EstadoAccionEnum::CorreccionesSolicitadas),
        'idUsuarioResponsable' => $coordinadora->getKey(),
        'fechaCambio' => '2026-11-20 12:30:00',
        'motivo' => 'Precisar los materiales y el presupuesto.',
    ]);
    $accion->historialEstados()->create([
        'idEstadoAccion' => EstadoAccion::idPara(EstadoAccionEnum::PendienteDeRevision),
        'idUsuarioResponsable' => $usuario->getKey(),
        'fechaCambio' => '2026-11-18 09:00:00',
        'motivo' => 'Propuesta recibida para revisión.',
    ]);

    $this->actingAs($usuario)->get(route('acciones.mis-propuestas.detalle', $accion))
        ->assertOk()
        ->assertSee($accion->descripcion)
        ->assertSee('Herramientas y material reutilizable.')
        ->assertSee('Correcciones solicitadas')
        ->assertSee('Pendiente de revisión')
        ->assertSee('Estudiante Responsable')
        ->assertSee('Coordinadora Responsable')
        ->assertSee('18/11/2026')
        ->assertSee('20/11/2026')
        ->assertSeeInOrder(['Propuesta recibida para revisión.', 'Precisar los materiales y el presupuesto.']);
});

it('redirige al solicitante no coordinador a sus propuestas y permite consultar la evidencia registrada', function (RolSistemaEnum $rol) {
    Storage::fake('public');
    $this->travelTo('2026-11-18 10:00:00');
    $usuario = UsuarioInstitucional::factory()->create();
    $usuario->asignarRol($rol);
    $eje = EjeTrabajo::factory()->create();

    $respuesta = $this->actingAs($usuario)->post(route('acciones.registrar'), [
        'nombre' => 'Jornada de reutilización de materiales',
        'descripcion' => 'Recuperación de materiales para los espacios universitarios.',
        'idEjeTrabajo' => $eje->getKey(),
        'modalidad' => 'Presencial',
        'formaTrabajo' => 'Individual',
        'fechaInicio' => '2026-12-01',
        'fechaTermino' => '2026-12-01',
        'evidencias' => [UploadedFile::fake()->create('plan-de-trabajo.pdf', 10, 'application/pdf')],
    ]);

    $accion = AccionSustentabilidad::query()->with('evidencias')->sole();
    $mensaje = "La propuesta {$accion->folio} se registró como Pendiente de revision.";
    $respuesta->assertRedirect(route('acciones.mis-propuestas'))->assertSessionHas('status', $mensaje);
    $this->assertDatabaseHas('AccionSustentabilidad', [
        'idAccion' => $accion->getKey(),
        'idUsuarioSolicitante' => $usuario->getKey(),
        'idEstadoAccion' => EstadoAccion::idPara(EstadoAccionEnum::PendienteDeRevision),
    ]);
    Storage::disk('public')->assertExists($accion->evidencias->sole()->ubicacionArchivo);
    $this->get(route('acciones.mis-propuestas'))->assertOk()->assertSee($mensaje)->assertSee($accion->nombre);
    $this->get(route('acciones.mis-propuestas.detalle', $accion))->assertOk()->assertSee('plan-de-trabajo.pdf');
})->with([
    'estudiante' => [RolSistemaEnum::Estudiante],
    'maestro' => [RolSistemaEnum::Maestro],
]);

it('escapa las observaciones del historial en el detalle privado', function () {
    $usuario = UsuarioInstitucional::factory()->estudiante()->create();
    $accion = AccionSustentabilidad::factory()->create(['idUsuarioSolicitante' => $usuario->getKey()]);
    $observacion = '<script>alert("observaciones")</script>';
    $accion->historialEstados()->create([
        'idEstadoAccion' => $accion->idEstadoAccion,
        'idUsuarioResponsable' => null,
        'fechaCambio' => '2026-11-18 09:00:00',
        'motivo' => $observacion,
    ]);

    $this->actingAs($usuario)->get(route('acciones.mis-propuestas.detalle', $accion))
        ->assertOk()
        ->assertSee($observacion)
        ->assertDontSee($observacion, false);
});
