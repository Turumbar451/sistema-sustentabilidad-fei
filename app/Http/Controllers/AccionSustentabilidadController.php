<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EstadoAccionEnum;
use App\Enums\FormaTrabajoEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class AccionSustentabilidadController
{
    public function consultar(Request $request): View
    {
        $request->merge([
            'ejes' => (array) $request->input('ejes', $request->input('idEjeTrabajo', [])),
        ]);

        $datos = $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'buscar' => ['nullable', 'string', 'max:200'],
            'ejes' => ['array'],
            'ejes.*' => ['integer', 'min:1'],
            'modalidad' => ['nullable', 'string', 'max:100'],
            'formaTrabajo' => ['nullable', Rule::enum(FormaTrabajoEnum::class)],
            'estado' => ['nullable', Rule::enum(EstadoAccionEnum::class)],
            'orden' => ['sometimes', Rule::in(['recientes', 'proximas', 'az'])],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $busqueda = trim($datos['q'] ?? $datos['buscar'] ?? '');
        $ejesSeleccionados = array_values(array_unique(array_map('intval', $datos['ejes'])));
        $modalidad = $datos['modalidad'] ?? '';
        $formaTrabajo = $datos['formaTrabajo'] ?? '';
        $estado = $datos['estado'] ?? '';
        $orden = $datos['orden'] ?? 'recientes';

        $filtros = array_filter([
            'q' => $busqueda,
            'idEjeTrabajo' => $ejesSeleccionados,
            'modalidad' => $modalidad,
            'formaTrabajo' => $formaTrabajo,
            'estado' => $estado,
            'orden' => $orden,
        ], static fn (mixed $valor): bool => $valor !== '' && $valor !== []);

        $acciones = AccionSustentabilidad::obtenerAccionesPublicadas($filtros);
        $pagina = (int) ($datos['page'] ?? 1);
        $porPagina = 9;

        $accionesPaginadas = new LengthAwarePaginator(
            $acciones->forPage($pagina, $porPagina)->values(),
            $acciones->count(),
            $porPagina,
            $pagina,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('accion-sustentabilidad.consultar', [
            'acciones' => $accionesPaginadas,
            'ejes' => EjeTrabajo::orderBy('nombre')->get(),
            'modalidades' => ['Presencial', 'Virtual', 'Híbrida'],
            'formasTrabajo' => array_column(FormaTrabajoEnum::cases(), 'value'),
            'estados' => array_values(array_map(
                static fn (EstadoAccionEnum $opcion): string => $opcion->value,
                array_filter(EstadoAccionEnum::cases(), static fn (EstadoAccionEnum $opcion): bool => $opcion->esVisibleEnCatalogo()),
            )),
            'filtros' => $filtros,
            'hayFiltros' => $busqueda !== '' || $ejesSeleccionados !== [] || $modalidad !== '' || $formaTrabajo !== '' || $estado !== '',
        ]);
    }

    public function mostrarDetalle(AccionSustentabilidad $accionSustentabilidad): View
    {
        abort_unless($accionSustentabilidad->esVisibleEnCatalogo(), 404);

        $accionSustentabilidad->cargarRelacionesConsulta();

        return view('accion-sustentabilidad.detalle', ['accion' => $accionSustentabilidad]);
    }
}
