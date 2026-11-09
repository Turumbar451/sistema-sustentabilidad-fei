<?php
//controlador responsable de preparar la info y devolver la vista principal.
namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class CatalogoPublicoController extends Controller
{
    public function index(): View
    {
        $acciones = collect();

        return view('catalogo.index', [
            'acciones' => $acciones,
        ]);
    }
}