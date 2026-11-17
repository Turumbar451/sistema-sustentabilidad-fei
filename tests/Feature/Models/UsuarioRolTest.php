<?php

declare(strict_types=1);

use App\Enums\RolSistemaEnum;
use App\Models\Rol;
use App\Models\UsuarioInstitucional;
use App\Models\UsuarioRol;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('reutiliza la asignación vigente cuando se solicita dos veces el mismo rol', function () {
    $this->travelTo('2026-11-12 10:00:00');
    $usuario = UsuarioInstitucional::factory()->create();
    $rol = Rol::asegurar(RolSistemaEnum::Maestro);

    $primera = UsuarioRol::asignarRolVigente($usuario->idUsuario, $rol->idRol);
    $segunda = UsuarioRol::asignarRolVigente($usuario->idUsuario, $rol->idRol);

    expect($segunda->idUsuarioRol)->toBe($primera->idUsuarioRol);
    $this->assertDatabaseCount('UsuarioRol', 1);
    $this->assertDatabaseHas('UsuarioRol', [
        'idUsuario' => $usuario->idUsuario,
        'idRol' => $rol->idRol,
        'fechaAsignacion' => '2026-11-12 10:00:00',
        'fechaFin' => null,
    ]);
});

it('excluye los roles históricos de las asignaciones vigentes y los permisos', function () {
    $usuario = UsuarioInstitucional::factory()->create();
    UsuarioRol::create([
        'idUsuario' => $usuario->idUsuario,
        'idRol' => Rol::idPara(RolSistemaEnum::Director),
        'fechaAsignacion' => '2025-01-01 08:00:00',
        'fechaFin' => '2025-12-31 18:00:00',
    ]);
    $usuario->asignarRol(RolSistemaEnum::Maestro);

    $vigentes = $usuario->rolesVigentes()->get();

    expect($usuario->roles()->count())->toBe(2);
    expect($vigentes->map(fn (Rol $rol): string => $rol->nombre->value)->all())
        ->toBe(['Maestro']);
    expect($usuario->tieneRol(RolSistemaEnum::Director))->toBeFalse();
    expect($usuario->tieneRol(RolSistemaEnum::Maestro))->toBeTrue();
});

it('crea una nueva asignación después de cerrar el rol sin perder su historia', function () {
    $this->travelTo('2026-01-01 08:00:00');
    $usuario = UsuarioInstitucional::factory()->create();
    $rol = Rol::asegurar(RolSistemaEnum::Maestro);
    $anterior = UsuarioRol::asignarRolVigente($usuario->idUsuario, $rol->idRol);
    $anterior->update(['fechaFin' => '2026-06-30 18:00:00']);
    $this->travelTo('2026-08-01 08:00:00');

    $vigente = UsuarioRol::asignarRolVigente($usuario->idUsuario, $rol->idRol);

    expect($vigente->idUsuarioRol)->not->toBe($anterior->idUsuarioRol);
    $this->assertDatabaseCount('UsuarioRol', 2);
    $this->assertDatabaseHas('UsuarioRol', [
        'idUsuarioRol' => $anterior->idUsuarioRol,
        'fechaAsignacion' => '2026-01-01 08:00:00',
        'fechaFin' => '2026-06-30 18:00:00',
    ]);
    $this->assertDatabaseHas('UsuarioRol', [
        'idUsuarioRol' => $vigente->idUsuarioRol,
        'fechaAsignacion' => '2026-08-01 08:00:00',
        'fechaFin' => null,
    ]);
    expect($usuario->rolesVigentes()->count())->toBe(1);
});
