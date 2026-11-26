<?php

declare(strict_types=1);

use App\Enums\RolSistemaEnum;
use App\Models\Rol;
use App\Models\UsuarioInstitucional;
use App\Models\UsuarioRol;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->app->instance('env', 'local');
    $this->withSession(['_token' => 'token-acceso-local']);
    $this->withHeader('X-CSRF-TOKEN', 'token-acceso-local');
});

it('muestra las cuatro cuentas sembradas y el aviso de acceso temporal en local', function () {
    $this->seed(DatabaseSeeder::class);

    $this->get(route('acceso-local.mostrar'))
        ->assertOk()
        ->assertViewIs('auth.acceso-local')
        ->assertSee('temporal')
        ->assertSee('desarrollo')
        ->assertSee('Ana Luz Polo Estrella')
        ->assertSee('P0001')
        ->assertSee('P0002')
        ->assertSee('M0001')
        ->assertSee('S20260001')
        ->assertSee(RolSistemaEnum::CoordinadoraSustentabilidad->value)
        ->assertSee(RolSistemaEnum::Director->value)
        ->assertSee(RolSistemaEnum::Maestro->value)
        ->assertSee(RolSistemaEnum::Estudiante->value)
        ->assertSee(route('acciones.consultar'));

    $this->assertGuest('web');
});

it('inicia una sesión persistente con cada perfil y conserva sus roles reales sin crear registros', function (string $identificador, RolSistemaEnum $rol) {
    $this->seed(DatabaseSeeder::class);
    $usuario = UsuarioInstitucional::buscarPorIdentificador($identificador);
    $usuariosAntes = UsuarioInstitucional::query()->count();
    $rolesAntes = Rol::query()->count();
    $asignacionesAntes = UsuarioRol::query()->count();
    $this->withSession(['dato_previo' => 'conservado']);
    $sesionAnterior = session()->getId();

    $this->post(route('acceso-local.iniciar'), [
        'identificadorInstitucional' => $identificador,
    ])->assertRedirect(route('acciones.consultar'))
        ->assertSessionHas('status')
        ->assertSessionHas(Auth::guard('web')->getName(), $usuario->getKey());

    expect(session()->getId())->not->toBe($sesionAnterior);
    expect(session('dato_previo'))->toBe('conservado');
    Auth::forgetGuards();

    $this->get(route('acciones.consultar'))
        ->assertOk()
        ->assertSee($usuario->nombreCompleto)
        ->assertSee($rol->value)
        ->assertSee('Cerrar sesión');

    $this->assertAuthenticatedAs($usuario, 'web');
    expect(Auth::guard('web')->user()->rolesVigentes->pluck('nombre')->all())->toBe([$rol]);
    $this->assertDatabaseHas('UsuarioRol', [
        'idUsuario' => $usuario->getKey(),
        'idRol' => Rol::idPara($rol),
        'fechaFin' => null,
    ]);
    $this->assertDatabaseCount('UsuarioInstitucional', $usuariosAntes);
    $this->assertDatabaseCount('Rol', $rolesAntes);
    $this->assertDatabaseCount('UsuarioRol', $asignacionesAntes);
})->with([
    'coordinadora' => ['P0001', RolSistemaEnum::CoordinadoraSustentabilidad],
    'director' => ['P0002', RolSistemaEnum::Director],
    'maestro' => ['M0001', RolSistemaEnum::Maestro],
    'estudiante' => ['S20260001', RolSistemaEnum::Estudiante],
]);

it('rechaza un identificador fuera de la lista aunque el usuario exista', function () {
    UsuarioInstitucional::factory()->create(['identificadorInstitucional' => 'P9999']);

    $this->from(route('acceso-local.mostrar'))->post(route('acceso-local.iniciar'), [
        'identificadorInstitucional' => 'P9999',
    ])->assertRedirect(route('acceso-local.mostrar'))
        ->assertSessionHasErrors('identificadorInstitucional');

    $this->assertGuest('web');
    $this->assertDatabaseCount('UsuarioInstitucional', 1);
    $this->assertDatabaseCount('Rol', 0);
    $this->assertDatabaseCount('UsuarioRol', 0);
});

it('rechaza una cuenta demo inexistente sin crear usuarios ni roles', function () {
    $this->from(route('acceso-local.mostrar'))->post(route('acceso-local.iniciar'), [
        'identificadorInstitucional' => 'P0001',
    ])->assertRedirect(route('acceso-local.mostrar'))
        ->assertSessionHasErrors('identificadorInstitucional');

    $this->assertGuest('web');
    $this->assertDatabaseCount('UsuarioInstitucional', 0);
    $this->assertDatabaseCount('Rol', 0);
    $this->assertDatabaseCount('UsuarioRol', 0);
});

it('valida que el identificador sea obligatorio y de texto', function (mixed $identificador) {
    $this->postJson(route('acceso-local.iniciar'), [
        'identificadorInstitucional' => $identificador,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('identificadorInstitucional');

    $this->assertGuest('web');
    $this->assertDatabaseCount('UsuarioInstitucional', 0);
    $this->assertDatabaseCount('Rol', 0);
    $this->assertDatabaseCount('UsuarioRol', 0);
})->with([
    'sin identificador' => [null],
    'identificador como arreglo' => [['P0001']],
]);

it('cierra la sesión web invalida sus datos y regenera el token', function () {
    $usuario = UsuarioInstitucional::factory()->create();
    $this->actingAs($usuario, 'web')->withSession(['dato_privado' => 'anterior']);
    $sesionAnterior = session()->getId();
    $tokenAnterior = session()->token();

    $this->post(route('sesion.cerrar'))
        ->assertRedirect(route('acciones.consultar'))
        ->assertSessionHas('status')
        ->assertSessionMissing('dato_privado');

    $this->assertGuest('web');
    expect(session()->getId())->not->toBe($sesionAnterior);
    expect(session()->token())->not->toBe($tokenAnterior);
    Auth::forgetGuards();

    $this->get(route('acciones.consultar'))
        ->assertOk()
        ->assertDontSee($usuario->nombreCompleto)
        ->assertDontSee('Cerrar sesión')
        ->assertSee(route('acceso-local.mostrar'));

    $this->assertGuest('web');
    $this->assertModelExists($usuario);
});

it('devuelve 404 para el acceso local en producción sin autenticar ni crear registros', function (string $metodo) {
    $this->app->instance('env', 'production');
    $usuario = UsuarioInstitucional::factory()->create(['identificadorInstitucional' => 'P0001']);

    $this->{$metodo}('/acceso-local', [
        'identificadorInstitucional' => 'P0001',
    ])->assertNotFound();

    $this->assertGuest('web');
    $this->assertModelExists($usuario);
    $this->assertDatabaseCount('UsuarioInstitucional', 1);
    $this->assertDatabaseCount('Rol', 0);
    $this->assertDatabaseCount('UsuarioRol', 0);
})->with(['get', 'post']);

it('muestra el enlace de acceso local al visitante en desarrollo', function () {
    $this->get(route('acciones.consultar'))
        ->assertOk()
        ->assertSee(route('acceso-local.mostrar'))
        ->assertDontSee('Cerrar sesión');

    $this->assertGuest('web');
});

it('omite el enlace de acceso local al visitante en producción', function () {
    $this->app->instance('env', 'production');

    $this->get(route('acciones.consultar'))
        ->assertOk()
        ->assertDontSee(route('acceso-local.mostrar'))
        ->assertDontSee('Cerrar sesión');

    $this->assertGuest('web');
});
