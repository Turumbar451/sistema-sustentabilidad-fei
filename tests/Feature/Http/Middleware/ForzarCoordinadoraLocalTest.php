<?php

declare(strict_types=1);

use App\Enums\EstadoAccionEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use App\Models\EstadoAccion;
use App\Models\UsuarioInstitucional;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('permite navegar como Ana Luz en local sin crear usuarios roles ni una sesión de acceso', function () {
    EjeTrabajo::factory()->create();
    $nombreSesion = Auth::guard('web')->getName();
    $this->app->instance('env', 'local');

    $respuesta = $this->get(route('acciones.formulario-registro'))
        ->assertOk()
        ->assertSee('Ana Luz Polo Estrella')
        ->assertDontSee('Entrar como docente')
        ->assertDontSee('Cerrar sesión de prueba')
        ->assertSessionMissing($nombreSesion);

    $documento = new DOMDocument;
    $documento->loadHTML($respuesta->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $boton = (new DOMXPath($documento))->query('//button[@type="submit" and @name="accion"]')->item(0);
    expect($boton)->not->toBeNull();
    expect($boton->hasAttribute('disabled'))->toBeFalse();
    $this->assertGuest('web');
    $this->assertDatabaseCount('UsuarioInstitucional', 0);
    $this->assertDatabaseCount('Rol', 0);
    $this->assertDatabaseCount('UsuarioRol', 0);
});

it('reutiliza el identificador existente de Ana Luz sin cambiar sus datos ni asignar roles en la base', function () {
    $coordinadora = UsuarioInstitucional::factory()->create([
        'identificadorInstitucional' => 'P0001',
        'nombreCompleto' => 'Ana Luz Polo Estrella',
        'correoInstitucional' => 'correo-preexistente@fei.example.test',
    ]);
    $datosOriginales = $coordinadora->getAttributes();
    $this->app->instance('env', 'local');

    $this->get(route('acciones.formulario-registro'))->assertOk()->assertSee('Ana Luz Polo Estrella');
    $this->get(route('acciones.consultar'))->assertOk()->assertSee('Ana Luz Polo Estrella');

    expect($coordinadora->fresh()->getAttributes())->toEqual($datosOriginales);
    $this->assertDatabaseCount('UsuarioInstitucional', 1);
    $this->assertDatabaseCount('Rol', 0);
    $this->assertDatabaseCount('UsuarioRol', 0);
});

it('registra una acción aprobada como Ana Luz sin iniciar sesión ni guardar usuarios o roles', function () {
    Storage::fake('public');
    $coordinadora = UsuarioInstitucional::factory()->create(['identificadorInstitucional' => 'P0001']);
    $otroUsuario = UsuarioInstitucional::factory()->create();
    $eje = EjeTrabajo::factory()->create();
    $estadoPendiente = EstadoAccion::idPara(EstadoAccionEnum::PendienteDeRevision);
    $nombreSesion = Auth::guard('web')->getName();
    $this->withSession(['_token' => 'token-csrf-local']);
    $this->app->instance('env', 'local');

    $respuesta = $this->post(route('acciones.registrar'), [
        '_token' => 'token-csrf-local',
        'nombre' => 'Acción registrada directamente por Ana Luz',
        'descripcion' => 'La Coordinadora registra una iniciativa sin la API institucional.',
        'idEjeTrabajo' => $eje->getKey(),
        'modalidad' => 'Presencial',
        'formaTrabajo' => 'Individual',
        'fechaInicio' => '2026-12-01',
        'fechaTermino' => '2026-12-01',
        'idUsuarioSolicitante' => $otroUsuario->getKey(),
        'idEstadoAccion' => $estadoPendiente,
        'evidencias' => [UploadedFile::fake()->create('evidencia.pdf', 128, 'application/pdf')],
    ]);

    $accion = AccionSustentabilidad::query()->with(['estadoAccion', 'evidencias'])->sole();
    $respuesta->assertRedirect(route('acciones.consultar'))
        ->assertSessionHas('status', "La propuesta {$accion->folio} se registró como Aprobada.")
        ->assertSessionMissing($nombreSesion);
    expect($accion->idUsuarioSolicitante)->toBe($coordinadora->getKey());
    expect($accion->estadoAccion->nombre)->toBe(EstadoAccionEnum::Aprobada);
    expect($accion->evidencias->sole()->idUsuarioCarga)->toBe($coordinadora->getKey());
    Storage::disk('public')->assertExists($accion->evidencias->sole()->ubicacionArchivo);
    $this->assertDatabaseCount('HistorialEstadoAccion', 1);
    $this->assertDatabaseHas('HistorialEstadoAccion', [
        'idAccion' => $accion->getKey(),
        'idEstadoAccion' => EstadoAccion::idPara(EstadoAccionEnum::Aprobada),
        'idUsuarioResponsable' => $coordinadora->getKey(),
    ]);
    $this->assertDatabaseCount('UsuarioInstitucional', 2);
    $this->assertDatabaseCount('Rol', 0);
    $this->assertDatabaseCount('UsuarioRol', 0);
    $this->assertGuest('web');

    $this->withCookie(config('session.cookie'), $respuesta->getCookie(config('session.cookie'))->getValue())
        ->get(route('acciones.consultar'))->assertOk()->assertSee('Acción registrada directamente por Ana Luz');
});

it('conserva los cuatro enlaces del menú y marca la sección actual en todas las páginas', function (string $ruta, string $enlaceActivo) {
    $accion = AccionSustentabilidad::factory()->aprobada()->create();
    $this->app->instance('env', 'local');
    $url = route($ruta, $ruta === 'acciones.detalle' ? [$accion] : []);

    $respuesta = $this->get($url)->assertOk();

    $documento = new DOMDocument;
    $documento->loadHTML($respuesta->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($documento);
    $enlaces = $xpath->query('//nav[@aria-label="Menú de la Coordinadora"]//a');
    expect($enlaces->length)->toBe(4);
    expect(array_map(fn (DOMElement $enlace): string => trim($enlace->textContent), iterator_to_array($enlaces)))
        ->toBe(['Resumen', 'Registrar propuesta', 'Evaluación de propuestas', 'Catálogo público']);
    expect(array_map(fn (DOMElement $enlace): string => $enlace->getAttribute('href'), iterator_to_array($enlaces)))
        ->toBe(['#', route('acciones.formulario-registro'), '#', route('acciones.consultar')]);
    $activo = $xpath->query('//nav[@aria-label="Menú de la Coordinadora"]//a[@aria-current="page"]');
    expect($activo->length)->toBe(1);
    expect(trim($activo->item(0)->textContent))->toBe($enlaceActivo);
})->with([
    'registro' => ['acciones.formulario-registro', 'Registrar propuesta'],
    'catálogo' => ['acciones.consultar', 'Catálogo público'],
    'detalle' => ['acciones.detalle', 'Catálogo público'],
]);

it('no asigna a Ana Luz ni permite registrar como visitante fuera de local', function (string $entorno) {
    UsuarioInstitucional::factory()->create([
        'identificadorInstitucional' => 'P0001',
        'nombreCompleto' => 'Ana Luz Polo Estrella',
    ]);
    $this->withSession(['_token' => 'token-csrf-local']);
    $this->app->instance('env', $entorno);

    $this->get(route('acciones.formulario-registro'))->assertOk()->assertDontSee('Ana Luz Polo Estrella');
    $this->post(route('acciones.registrar'), ['_token' => 'token-csrf-local'])->assertForbidden();

    $this->assertGuest('web');
    $this->assertDatabaseCount('UsuarioInstitucional', 1);
    $this->assertDatabaseCount('AccionSustentabilidad', 0);
})->with(['production', 'staging', 'testing']);

it('conserva al usuario autenticado real fuera de local', function () {
    $usuario = UsuarioInstitucional::factory()->create(['nombreCompleto' => 'Docente institucional']);
    $this->actingAs($usuario);
    $this->app->instance('env', 'production');

    $this->get(route('acciones.formulario-registro'))
        ->assertOk()->assertSee('Docente institucional')->assertDontSee('Ana Luz Polo Estrella');

    $this->assertAuthenticatedAs($usuario, 'web');
    $this->assertDatabaseCount('UsuarioInstitucional', 1);
});

it('escapa el nombre del usuario que se muestra en el layout compartido', function () {
    $nombre = '<script>alert("perfil")</script>';
    $usuario = UsuarioInstitucional::factory()->create(['nombreCompleto' => $nombre]);

    $this->actingAs($usuario)->get(route('acciones.formulario-registro'))
        ->assertOk()->assertSee($nombre)->assertDontSee($nombre, false);
});

it('explica la clave foránea cuando falta P0001 sin crear un usuario ni guardar datos', function () {
    Storage::fake('public');
    EjeTrabajo::factory()->create();
    $this->withSession(['_token' => 'token-csrf-local']);
    $this->app->instance('env', 'local');

    $this->postJson(route('acciones.registrar'), ['_token' => 'token-csrf-local'])
        ->assertUnprocessable()->assertJsonValidationErrors([
            'usuario' => 'Para guardar una acción, Ana Luz (P0001) debe existir en UsuarioInstitucional. El acceso temporal no crea usuarios.',
        ]);

    $this->assertGuest('web');
    $this->assertDatabaseCount('UsuarioInstitucional', 0);
    $this->assertDatabaseCount('Rol', 0);
    $this->assertDatabaseCount('UsuarioRol', 0);
    $this->assertDatabaseCount('AccionSustentabilidad', 0);
    $this->assertDatabaseCount('HistorialEstadoAccion', 0);
    $this->assertDatabaseCount('Evidencia', 0);
    Storage::disk('public')->assertEmpty();
});

it('mantiene la protección CSRF al registrar con la identidad automática', function () {
    UsuarioInstitucional::factory()->create(['identificadorInstitucional' => 'P0001']);
    $this->app->instance('env', 'local');

    $this->post(route('acciones.registrar'))->assertStatus(419);

    $this->assertDatabaseCount('AccionSustentabilidad', 0);
});

it('elimina las rutas de inicio y cierre de la anterior sesión de desarrollo', function () {
    EjeTrabajo::factory()->create();
    $this->app->instance('env', 'local');

    $this->post('/desarrollo/sesion')->assertNotFound();
    $this->delete('/desarrollo/sesion')->assertNotFound();

    $this->assertDatabaseCount('UsuarioInstitucional', 0);
});
