<?php

declare(strict_types=1);

use App\Enums\RolSistemaEnum;
use App\Http\Requests\AccionSustentabilidad\RegistrarAccionRequest;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use App\Models\Rol;
use App\Models\UsuarioInstitucional;
use App\Models\UsuarioRol;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withSession(['_token' => 'token-autorizacion-roles']);
    $this->withHeader('X-CSRF-TOKEN', 'token-autorizacion-roles');
});

dataset('perfiles del catalogo publico', [
    'visitante' => [null],
    'estudiante' => [RolSistemaEnum::Estudiante],
    'maestro' => [RolSistemaEnum::Maestro],
    'coordinadora' => [RolSistemaEnum::CoordinadoraSustentabilidad],
    'director' => [RolSistemaEnum::Director],
]);

dataset('roles que registran propuestas', [
    'estudiante' => [RolSistemaEnum::Estudiante],
    'maestro' => [RolSistemaEnum::Maestro],
    'coordinadora' => [RolSistemaEnum::CoordinadoraSustentabilidad],
]);

function datosPropuestaParaAutorizacion(EjeTrabajo $eje): array
{
    return [
        'nombre' => 'Jornada de sustentabilidad',
        'descripcion' => 'Actividad de la comunidad universitaria para recuperar espacios verdes.',
        'idEjeTrabajo' => $eje->getKey(),
        'modalidad' => 'Presencial',
        'formaTrabajo' => 'Individual',
        'fechaInicio' => '2026-12-01',
        'fechaTermino' => '2026-12-01',
        'requerimientos' => null,
    ];
}

it('permite consultar el catalogo y el detalle publico a todos los perfiles', function (?RolSistemaEnum $rol) {
    $accion = AccionSustentabilidad::factory()->aprobada()->create();

    if ($rol !== null) {
        $this->actingAs(UsuarioInstitucional::factory()->conRol($rol)->create(), 'web');
    }

    $this->get(route('acciones.consultar'))
        ->assertOk()
        ->assertSee($accion->nombre);
    $this->get(route('acciones.detalle', $accion))
        ->assertOk()
        ->assertSee($accion->nombre);
})->with('perfiles del catalogo publico');

it('permite abrir el formulario con un rol vigente autorizado', function (RolSistemaEnum $rol) {
    $usuario = UsuarioInstitucional::factory()->conRol($rol)->create();

    $this->actingAs($usuario, 'web')->get(route('acciones.formulario-registro'))
        ->assertOk()
        ->assertViewIs('accion-sustentabilidad.registrar');
})->with('roles que registran propuestas');

it('permite registrar propuestas con un rol vigente autorizado', function (RolSistemaEnum $rol) {
    Storage::fake('public');
    $usuario = UsuarioInstitucional::factory()->conRol($rol)->create();
    $datos = datosPropuestaParaAutorizacion(EjeTrabajo::factory()->create());

    $this->actingAs($usuario, 'web')->post(route('acciones.registrar'), $datos)
        ->assertRedirect(route('acciones.consultar'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('AccionSustentabilidad', 1);
    $this->assertDatabaseHas('AccionSustentabilidad', [
        'nombre' => $datos['nombre'],
        'idUsuarioSolicitante' => $usuario->getKey(),
    ]);
    $this->assertDatabaseCount('HistorialEstadoAccion', 1);
    $this->assertDatabaseCount('Evidencia', 0);
    Storage::disk('public')->assertEmpty();
})->with('roles que registran propuestas');

it('redirige al visitante al acceso local al solicitar el formulario en local', function () {
    $this->app->instance('env', 'local');

    $this->get(route('acciones.formulario-registro'))
        ->assertRedirect(route('acceso-local.mostrar'));

    $this->assertGuest('web');
});

it('devuelve 403 al visitante que solicita el formulario fuera de local', function (string $entorno) {
    $this->app->instance('env', $entorno);

    $this->get(route('acciones.formulario-registro'))->assertForbidden();

    $this->assertGuest('web');
})->with(['testing', 'production']);

it('devuelve 403 al visitante que intenta registrar en cualquier entorno', function (string $entorno) {
    $this->app->instance('env', $entorno);
    $datos = datosPropuestaParaAutorizacion(EjeTrabajo::factory()->create());

    $this->post(route('acciones.registrar'), $datos)->assertForbidden();

    $this->assertGuest('web');
    $this->assertDatabaseCount('AccionSustentabilidad', 0);
    $this->assertDatabaseCount('HistorialEstadoAccion', 0);
    $this->assertDatabaseCount('Evidencia', 0);
})->with(['local', 'testing', 'production']);

it('devuelve 403 al director en el formulario y en el registro', function () {
    $usuario = UsuarioInstitucional::factory()->director()->create();
    $datos = datosPropuestaParaAutorizacion(EjeTrabajo::factory()->create());
    $this->actingAs($usuario, 'web');

    $this->get(route('acciones.formulario-registro'))->assertForbidden();
    $this->post(route('acciones.registrar'), $datos)->assertForbidden();

    $this->assertDatabaseCount('AccionSustentabilidad', 0);
    $this->assertDatabaseCount('HistorialEstadoAccion', 0);
});

it('devuelve 403 al usuario institucional que no tiene roles', function () {
    $usuario = UsuarioInstitucional::factory()->create();
    $datos = datosPropuestaParaAutorizacion(EjeTrabajo::factory()->create());
    $this->actingAs($usuario, 'web');

    $this->get(route('acciones.formulario-registro'))->assertForbidden();
    $this->post(route('acciones.registrar'), $datos)->assertForbidden();

    $this->assertDatabaseCount('AccionSustentabilidad', 0);
    $this->assertDatabaseCount('HistorialEstadoAccion', 0);
});

it('devuelve 403 cuando el rol permitido tiene fecha de fin aunque sea futura', function (RolSistemaEnum $rol, string $fechaFin) {
    $this->travelTo('2026-11-26 10:00:00');
    $usuario = UsuarioInstitucional::factory()->create();
    UsuarioRol::factory()->historico()->create([
        'idUsuario' => $usuario->getKey(),
        'idRol' => Rol::idPara($rol),
        'fechaFin' => $fechaFin,
    ]);
    $datos = datosPropuestaParaAutorizacion(EjeTrabajo::factory()->create());
    $this->actingAs($usuario, 'web');

    $this->get(route('acciones.formulario-registro'))->assertForbidden();
    $this->post(route('acciones.registrar'), $datos)->assertForbidden();

    $solicitud = new RegistrarAccionRequest;
    $solicitud->setUserResolver(fn () => $usuario);
    expect($solicitud->authorize())->toBeFalse();
    $this->assertDatabaseCount('AccionSustentabilidad', 0);
    $this->assertDatabaseCount('HistorialEstadoAccion', 0);
})->with('roles que registran propuestas')->with([
    'rol vencido' => ['2026-11-25 10:00:00'],
    'fecha de fin futura' => ['2026-11-27 10:00:00'],
]);

it('permite registrar cuando alguno de los roles vigentes autoriza aunque tambien sea director', function () {
    $usuario = UsuarioInstitucional::factory()->director()->estudiante()->create();
    $datos = datosPropuestaParaAutorizacion(EjeTrabajo::factory()->create());
    $this->actingAs($usuario, 'web');

    $this->get(route('acciones.formulario-registro'))->assertOk();
    $this->post(route('acciones.registrar'), $datos)
        ->assertRedirect(route('acciones.consultar'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('AccionSustentabilidad', [
        'nombre' => $datos['nombre'],
        'idUsuarioSolicitante' => $usuario->getKey(),
    ]);
});

it('autoriza el FormRequest solo para usuarios institucionales con un rol permitido vigente', function (?RolSistemaEnum $rol, bool $autorizado) {
    $usuario = $rol === null ? null : UsuarioInstitucional::factory()->conRol($rol)->create();
    $solicitud = new RegistrarAccionRequest;
    $solicitud->setUserResolver(fn () => $usuario);

    expect($solicitud->authorize())->toBe($autorizado);
})->with([
    'visitante' => [null, false],
    'estudiante' => [RolSistemaEnum::Estudiante, true],
    'maestro' => [RolSistemaEnum::Maestro, true],
    'coordinadora' => [RolSistemaEnum::CoordinadoraSustentabilidad, true],
    'director' => [RolSistemaEnum::Director, false],
]);

it('muestra en el menu solo las opciones permitidas para el perfil', function (?RolSistemaEnum $rol, array $esperadas) {
    if ($rol !== null) {
        $this->actingAs(UsuarioInstitucional::factory()->conRol($rol)->create(), 'web');
    }

    $respuesta = $this->get(route('acciones.consultar'))->assertOk();

    $documento = new DOMDocument;
    $documento->loadHTML('<?xml encoding="UTF-8">'.$respuesta->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($documento);
    $menu = $xpath->query('//aside//nav')->item(0);
    expect($menu)->not->toBeNull();
    expect($menu->getAttribute('aria-label'))->not->toContain('Coordinadora');

    $opciones = [];
    foreach ($xpath->query('.//a | .//*[@aria-disabled="true"]', $menu) as $opcion) {
        $opciones[] = trim($opcion->textContent);
    }
    sort($opciones);
    sort($esperadas);
    expect($opciones)->toBe($esperadas);

    $catalogo = $xpath->query('.//a[normalize-space(.)="Catálogo público"]', $menu);
    expect($catalogo->length)->toBe(1);
    expect($catalogo->item(0)->getAttribute('href'))->toBe(route('acciones.consultar'));

    foreach ($xpath->query('.//*[@aria-disabled="true"]', $menu) as $opcion) {
        expect($opcion->hasAttribute('href'))->toBeFalse();
    }

    foreach ($xpath->query('.//a', $menu) as $enlace) {
        expect($enlace->getAttribute('href'))->toBeIn([
            route('acciones.consultar'),
            route('acciones.formulario-registro'),
        ]);
    }
})->with([
    'visitante' => [null, ['Catálogo público']],
    'estudiante' => [RolSistemaEnum::Estudiante, ['Catálogo público', 'Registrar propuesta', 'Mis propuestas']],
    'maestro' => [RolSistemaEnum::Maestro, ['Catálogo público', 'Registrar propuesta', 'Mis propuestas']],
    'coordinadora' => [RolSistemaEnum::CoordinadoraSustentabilidad, ['Catálogo público', 'Registrar propuesta', 'Mis propuestas', 'Evaluación de propuestas']],
    'director' => [RolSistemaEnum::Director, ['Catálogo público']],
]);

it('omite las opciones privadas del menu cuando todos los roles permitidos estan vencidos', function () {
    $this->travelTo('2026-11-26 10:00:00');
    $usuario = UsuarioInstitucional::factory()->create();
    UsuarioRol::factory()->historico()->create([
        'idUsuario' => $usuario->getKey(),
        'idRol' => Rol::idPara(RolSistemaEnum::CoordinadoraSustentabilidad),
    ]);

    $respuesta = $this->actingAs($usuario, 'web')->get(route('acciones.consultar'))->assertOk();

    $documento = new DOMDocument;
    $documento->loadHTML('<?xml encoding="UTF-8">'.$respuesta->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($documento);
    $menu = $xpath->query('//aside//nav')->item(0);
    expect($menu->textContent)
        ->toContain('Catálogo público')
        ->not->toContain('Registrar propuesta')
        ->not->toContain('Mis propuestas')
        ->not->toContain('Evaluación de propuestas');
});
