<?php

use App\Enums\RolSistemaEnum;
use App\Http\Controllers\AccesoLocalController;
use App\Http\Controllers\AccionSustentabilidadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AccionSustentabilidadController::class, 'consultar'])
    ->name('acciones.consultar');

Route::middleware('rol:'.implode(',', [
    RolSistemaEnum::Estudiante->value,
    RolSistemaEnum::Maestro->value,
    RolSistemaEnum::CoordinadoraSustentabilidad->value,
]))->group(function (): void {
    Route::get('/acciones/registrar', [AccionSustentabilidadController::class, 'mostrarFormularioRegistro'])
        ->name('acciones.formulario-registro');

    Route::post('/acciones', [AccionSustentabilidadController::class, 'registrar'])
        ->name('acciones.registrar');
});

Route::get('/acciones/{accionSustentabilidad}', [AccionSustentabilidadController::class, 'mostrarDetalle'])
    ->whereNumber('accionSustentabilidad')
    ->name('acciones.detalle');

Route::get('/acceso-local', [AccesoLocalController::class, 'mostrar'])
    ->name('acceso-local.mostrar');

Route::post('/acceso-local', [AccesoLocalController::class, 'iniciar'])
    ->name('acceso-local.iniciar');

Route::post('/cerrar-sesion', [AccesoLocalController::class, 'cerrar'])
    ->name('sesion.cerrar');
