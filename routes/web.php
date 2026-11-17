<?php

use App\Http\Controllers\AccionSustentabilidadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AccionSustentabilidadController::class, 'consultar'])
    ->name('acciones.consultar');

Route::get('/acciones/{accionSustentabilidad}', [AccionSustentabilidadController::class, 'mostrarDetalle'])
    ->whereNumber('accionSustentabilidad')
    ->name('acciones.detalle');
