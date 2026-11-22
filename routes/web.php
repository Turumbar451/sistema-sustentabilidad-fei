<?php

use App\Http\Controllers\AccionSustentabilidadController;
use App\Http\Middleware\ForzarCoordinadoraLocal;
use Illuminate\Support\Facades\Route;

// Temporal: el middleware solo asigna a la Coordinadora en el entorno local.
Route::middleware(ForzarCoordinadoraLocal::class)->group(function (): void {
    Route::get('/', [AccionSustentabilidadController::class, 'consultar'])
        ->name('acciones.consultar');

    Route::get('/acciones/registrar', [AccionSustentabilidadController::class, 'mostrarFormularioRegistro'])
        ->name('acciones.formulario-registro');

    Route::post('/acciones', [AccionSustentabilidadController::class, 'registrar'])
        ->name('acciones.registrar');

    Route::get('/acciones/{accionSustentabilidad}', [AccionSustentabilidadController::class, 'mostrarDetalle'])
        ->whereNumber('accionSustentabilidad')
        ->name('acciones.detalle');
});
