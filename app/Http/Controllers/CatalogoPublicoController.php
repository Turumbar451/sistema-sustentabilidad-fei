<?php
//controlador responsable de preparar la info y devolver la vista principal.
namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class CatalogoPublicoController extends Controller
{

public function index()
{
    // Colección temporal de iniciativas.
    $acciones = collect();

    // Categorías disponibles para los filtros.
    $categorias = [
        'Áreas verdes y biodiversidad',
        'Agua',
        'Residuos',
        'Energía y consumo',
    ];

    return view('catalogo.index', [
        'items' => $acciones,
        'categorias' => $categorias,
    ]);
}
}