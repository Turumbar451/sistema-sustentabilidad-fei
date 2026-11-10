<?php

use App\Http\Controllers\CatalogoPublicoController;
use Illuminate\Support\Facades\Route;


Route::get('/', [CatalogoPublicoController::class, 'index'])
    ->name('catalogo.index');

Route::get('/catalogo/{accion}', [CatalogoPublicoController::class, 'show'])
    ->whereNumber('accion')
    ->name('catalogo.show');
