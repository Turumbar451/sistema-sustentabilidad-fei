<?php

namespace App\Http\Controllers;

use App\Support\Catalogo\CatalogoDemoData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class CatalogoPublicoController extends Controller
{
/* cambiar esto cuando se tenga la base de datos
de mientras se usa la clase catalogodemodata que esta en app/Support/Catalogo/CatalogoDemoData.php para simular la base de datos y construir la interfaz
*/
    public function index(Request $request): View
    {
        $categorias = CatalogoDemoData::categorias();
        $modalidades = CatalogoDemoData::modalidades();

        // validar los parametrs recibidos por el forulario de busqueda y filtrado
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'ejes' => ['sometimes', 'array'],
            'ejes.*' => ['string', Rule::in($categorias)],
            'modalidad' => ['nullable', 'string', Rule::in($modalidades)],
            'orden' => ['nullable', 'string', Rule::in(['recientes', 'proximas', 'az'])],
        ]);

        $busqueda = trim((string) ($filtros['q'] ?? ''));
        $ejesSeleccionados = $filtros['ejes'] ?? [];
        $modalidadSeleccionada = $filtros['modalidad'] ?? '';
        $ordenSeleccionado = $filtros['orden'] ?? 'recientes';

       
        $acciones = CatalogoDemoData::items();

        // aplicar filtro de busqueda, eje y modalidad
        $resultados = $acciones->filter(function (array $accion) use (
            $busqueda,
            $ejesSeleccionados,
            $modalidadSeleccionada
        ): bool {
            $coincideBusqueda = $busqueda === '';

            if (! $coincideBusqueda) {
                $termino = mb_strtolower($busqueda);

                $camposBusqueda = [
                    $accion['titulo'],
                    $accion['descripcion'],
                    $accion['categoria'],
                ];

                foreach ($camposBusqueda as $campo) {
                    if (str_contains(mb_strtolower($campo), $termino)) {
                        $coincideBusqueda = true;
                        break;
                    }
                }
            }

            $coincideEje = $ejesSeleccionados === []
                || in_array($accion['categoria'], $ejesSeleccionados, true);

            $coincideModalidad = $modalidadSeleccionada === ''
                || $accion['modalidad'] === $modalidadSeleccionada;

            return $coincideBusqueda && $coincideEje && $coincideModalidad;
        });

        // Ordenar los resultados antes de paginarlos.
        $resultados = match ($ordenSeleccionado) {
            'az' => $resultados->sortBy('titulo', SORT_NATURAL | SORT_FLAG_CASE),
            'proximas' => $resultados->sortBy('fecha_iso'),
            default => $resultados->sortByDesc('fecha_iso'),
        };

        // paginacion temporal
        $porPagina = 9;
        $paginaActual = LengthAwarePaginator::resolveCurrentPage();
        $totalResultados = $resultados->count();

        $items = new LengthAwarePaginator(
            $resultados->values()->forPage($paginaActual, $porPagina),
            $totalResultados,
            $porPagina,
            $paginaActual,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('catalogo.index', [
            'items' => $items,
            'categorias' => $categorias,
            'modalidades' => $modalidades,
            'busqueda' => $busqueda,
            'ejesSeleccionados' => $ejesSeleccionados,
            'modalidadSeleccionada' => $modalidadSeleccionada,
            'ordenSeleccionado' => $ordenSeleccionado,
        ]);
    }

    /**
     * Muestra el detalle de una iniciativa temporal.
     *
     * Este metodo permite que los enlaces de las tarjetas funcionen durante
     * la etapa de maquetacion. Luego se reemplazara por una consulta a eloquent.
     */
    public function show(int $accion): View
    {
        $item = CatalogoDemoData::items()->firstWhere('id', $accion);

        abort_if($item === null, 404);

        return view('catalogo.show', [
            'item' => $item,
        ]);
    }
}
