<?php

use App\Http\Controllers\CatalogoPublicoController;
use Illuminate\Support\Facades\Route;

// pagina principal (el catalogo)
Route::get('/', [CatalogoPublicoController::class, 'index'])
    ->name('catalogo.index');

// detalle de una accion de sustentabilidad
Route::get('/acciones/{id}', [CatalogoPublicoController::class, 'show'])
    ->whereNumber('id')
    ->name('catalogo.show'); //en lugar de usar la url completa, se usa el nombre de la ruta.