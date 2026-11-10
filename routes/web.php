<?php

// routes/web.php  (añade estas líneas al final del archivo)

use App\Http\Controllers\CatalogoController;
use Illuminate\Support\Facades\Route;

// Catálogo público de acciones de sustentabilidad (CU-04)
Route::get('/', [CatalogoController::class, 'index'])->name('catalogo.index');

// Opcional: que la portada apunte al catálogo mientras no exista home
// Route::redirect('/', '/catalogo');
