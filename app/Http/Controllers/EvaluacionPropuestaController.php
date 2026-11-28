<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AccionSustentabilidad;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EvaluacionPropuestaController
{
    public function index(Request $request): View
    {
        return view('evaluacion.index', [
            'acciones' => AccionSustentabilidad::paginarPropuestasParaEvaluacion(
                pagina: $request->integer('page', 1),
            ),
        ]);
    }

    public function mostrarDetalle(AccionSustentabilidad $accionSustentabilidad): View
    {
        $accionSustentabilidad->cargarRelacionesEvaluacion();

        return view('evaluacion.detalle', ['accion' => $accionSustentabilidad]);
    }
}
