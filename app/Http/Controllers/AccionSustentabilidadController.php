<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AccionSustentabilidad\RegistrarPropuesta;
use App\Enums\EstadoAccionEnum;
use App\Enums\FormaTrabajoEnum;
use App\Http\Requests\AccionSustentabilidad\ConsultarAccionesRequest;
use App\Http\Requests\AccionSustentabilidad\RegistrarAccionRequest;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AccionSustentabilidadController
{
    public function consultar(ConsultarAccionesRequest $request): View
    {
        $filtros = $request->filtros();

        return view('accion-sustentabilidad.consultar', [
            'acciones' => AccionSustentabilidad::paginarAccionesPublicadas(
                $filtros,
                pagina: $request->integer('page', 1),
            )->withQueryString(),
            'ejes' => EjeTrabajo::obtenerTodos(),
            'modalidades' => ['Presencial', 'Virtual', 'Híbrida'],
            'formasTrabajo' => array_column(FormaTrabajoEnum::cases(), 'value'),
            'estados' => EstadoAccionEnum::valoresVisiblesEnCatalogo(),
            'filtros' => $filtros,
            'hayFiltros' => $request->hayFiltros(),
        ]);
    }

    public function mostrarDetalle(AccionSustentabilidad $accionSustentabilidad): View
    {
        abort_unless($accionSustentabilidad->esVisibleEnCatalogo(), 404);

        $accionSustentabilidad->cargarRelacionesConsulta();

        return view('accion-sustentabilidad.detalle', ['accion' => $accionSustentabilidad]);
    }

    public function mostrarFormularioRegistro(): View
    {
        return view('accion-sustentabilidad.registrar', [
            'ejesTrabajo' => EjeTrabajo::obtenerTodos(),
        ]);
    }

    public function registrar(RegistrarAccionRequest $request, RegistrarPropuesta $registrarPropuesta): RedirectResponse
    {
        $accion = $registrarPropuesta->ejecutar(
            $request->safe()->except('evidencias'),
            $request->file('evidencias', []),
            $request->user(),
        );

        return to_route('acciones.consultar')
            ->with('status', "La propuesta {$accion->folio} se registró como {$accion->estadoAccion->nombre->value}.");
    }
}
