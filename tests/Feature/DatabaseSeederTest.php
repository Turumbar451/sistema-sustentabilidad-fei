<?php

declare(strict_types=1);

use App\Enums\EstadoAccionEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use App\Models\EstadoAccion;
use App\Models\Evidencia;
use App\Models\Participacion;
use App\Models\Rol;
use App\Models\UsuarioRol;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('siembra los cuatro roles cinco ejes y seis estados DI-02 sin duplicarlos', function () {
    $this->seed(CatalogosBaseSeeder::class);

    $this->seed(CatalogosBaseSeeder::class);

    expect(Rol::all()->map(fn (Rol $rol): string => $rol->nombre->value)->all())
        ->toEqualCanonicalizing([
            'Estudiante',
            'Maestro',
            'Coordinadora de Sustentabilidad',
            'Director',
        ]);
    expect(EjeTrabajo::query()->pluck('nombre')->all())->toEqualCanonicalizing([
        'Gestion integrada de agua y residuos',
        'Salud',
        'Consumo responsable',
        'Gestion integrada de energia',
        'Administracion y tecnologias sustentables',
    ]);
    expect(EstadoAccion::all()->map(fn (EstadoAccion $estado): string => $estado->nombre->value)->all())
        ->toEqualCanonicalizing([
            'Pendiente de revision',
            'Correcciones solicitadas',
            'Aprobada',
            'Rechazada',
            'En ejecucion',
            'Finalizada',
        ]);
});

it('genera quince acciones en todos los estados con trazabilidad roles históricos y evidencias', function () {
    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseCount('AccionSustentabilidad', 15);
    expect(AccionSustentabilidad::query()->with('estadoAccion')->get()
        ->map(fn (AccionSustentabilidad $accion): string => $accion->estadoAccion->nombre->value)
        ->unique()->values()->all())->toEqualCanonicalizing([
            'Pendiente de revision',
            'Correcciones solicitadas',
            'Aprobada',
            'Rechazada',
            'En ejecucion',
            'Finalizada',
        ]);

    $corregida = AccionSustentabilidad::query()->where('folio', 'FEI-2026-001')
        ->with('historialEstados.estadoAccion')->firstOrFail();
    expect($corregida->historialEstados->map(fn ($cambio): string => $cambio->estadoAccion->nombre->value)->all())
        ->toBe(['Pendiente de revision', 'Correcciones solicitadas', 'Aprobada']);

    $historico = UsuarioRol::query()->whereNotNull('fechaFin')->firstOrFail();
    $this->assertDatabaseHas('UsuarioRol', [
        'idUsuario' => $historico->idUsuario,
        'idRol' => $historico->idRol,
        'fechaFin' => null,
    ]);

    $evidencia = Evidencia::query()->with(['usuarioCarga', 'accion.estadoAccion'])->firstOrFail();
    expect($evidencia->usuarioCarga->idUsuario)->toBe($evidencia->accion->idUsuarioSolicitante);
    expect($evidencia->accion->estadoAccion->nombre)->toBe(EstadoAccionEnum::Finalizada);
    expect(Participacion::query()->exists())->toBeTrue();
});
