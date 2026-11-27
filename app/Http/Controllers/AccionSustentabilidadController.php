<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AccionSustentabilidad\RegistrarPropuesta;
use App\Enums\EstadoAccionEnum;
use App\Enums\FormaTrabajoEnum;
use App\Enums\RolSistemaEnum;
use App\Http\Requests\AccionSustentabilidad\ConsultarAccionesRequest;
use App\Http\Requests\AccionSustentabilidad\RegistrarAccionRequest;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    public function misPropuestas(Request $request): View
    {
        return view('accion-sustentabilidad.mis-propuestas', [
            'acciones' => AccionSustentabilidad::paginarPropuestasDelSolicitante(
                $request->user()->getKey(),
                pagina: $request->integer('page', 1),
            ),
        ]);
    }

    public function mostrarDetallePropia(Request $request, AccionSustentabilidad $accionSustentabilidad): View
    {
        abort_unless($accionSustentabilidad->idUsuarioSolicitante === $request->user()?->getKey(), 403);

        $accionSustentabilidad->cargarRelacionesDetallePropia();

        return view('accion-sustentabilidad.detalle-propia', ['accion' => $accionSustentabilidad]);
    }

    public function registrar(RegistrarAccionRequest $request, RegistrarPropuesta $registrarPropuesta): RedirectResponse
    {
        $accion = $registrarPropuesta->ejecutar(
            $request->safe()->except('evidencias'),
            $request->file('evidencias', []),
            $request->user(),
        );

        $ruta = $request->user()->tieneRol(RolSistemaEnum::CoordinadoraSustentabilidad)
            ? 'acciones.consultar'
            : 'acciones.mis-propuestas';

        return to_route($ruta)
            ->with('status', "La propuesta {$accion->folio} se registró como {$accion->estadoAccion->nombre->value}.");
    }
}
