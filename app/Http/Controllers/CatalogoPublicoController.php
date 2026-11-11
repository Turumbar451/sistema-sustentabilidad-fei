<?php
namespace App\Http\Controllers;

use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use BackedEnum;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class CatalogoPublicoController extends Controller
{
    public function index(Request $request): View    
    {      
        //obtener filtros enviados por las vistas
      $busqueda = trim($request->input('q'));

        $ejesSeleccionados = collect(
            (array) $request->input('ejes', [])
        )
            ->filter(fn ($id) => filter_var($id, FILTER_VALIDATE_INT) !== false)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all(); //all devuelve un arreglo con los valores de la coleccion

        $modalidadesSeleccionadas = collect(
            (array) $request->input('modalidad', [])
        )
            ->filter(fn ($modalidad) => is_string($modalidad) && $modalidad !== '')
            ->unique()
            ->values()
            ->all();

        // obtener y validad criterios de ordenamiento
        $ordenSeleccionado = $request->input('orden', 'recientes');

            //lo que buscamos, el arreglo donde buscamos, comparacion estricta
        if (!in_array($ordenSeleccionado, ['recientes', 'proximas', 'az'], true)) {
            $ordenSeleccionado = 'recientes';
        }

      //preparar los filtros para el modelo
        $filtros = [
            'q' => $busqueda,
            'idEjeTrabajo' => $ejesSeleccionados,
            'modalidad' => $modalidadesSeleccionadas,
        ];

        //obtener las acciones publicadas
        $acciones = AccionSustentabilidad::obtenerAccionesPublicadas($filtros);

    
        //transformar los modelos en arreglos para las vistas
        $datos = $acciones->map(function (AccionSustentabilidad $accion): array {
            return [
                'id' => $accion->idAccion,
                'folio' => $accion->folio,
                'nombre' => $accion->nombre,
                'titulo' => $accion->nombre,
                'descripcion' => $accion->descripcion,

                'idEjeTrabajo' => $accion->idEjeTrabajo,
                'ejeTrabajo' => $accion->ejeTrabajo?->nombre ?? 'Sin eje asignado',
                'categoria' => $accion->ejeTrabajo?->nombre ?? 'Sin eje asignado',

                'modalidad' => $this->obtenerValorEnum($accion->modalidad),
                'estado' => $this->obtenerValorEnum($accion->estadoActual),

                'fechaInicio' => $this->formatearFecha($accion->fechaInicio),
                'fechaFin' => $this->formatearFecha($accion->fechaTermino),
                'fechaRegistro' => $this->formatearFecha($accion->fechaRegistro),
            ];
        })->values();

         //paginar los resultados
        $porPagina = 9;

        $paginaActual = max(
            1,
            LengthAwarePaginator::resolveCurrentPage()
        );

        $items = new LengthAwarePaginator(
            $datos->forPage($paginaActual, $porPagina)->values(),
            $datos->count(),
            $porPagina,
            $paginaActual,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

         //obtener ejes de trrabjao para los filtros

        $categorias = EjeTrabajo::obtenerTodos()
            ->map(fn (EjeTrabajo $eje): array => [
                'id' => (int) $eje->idEjeTrabajo,
                'nombre' => $eje->nombre,
            ])
            ->values()
            ->all();

        //preparar modalidades disponibles
        $modalidades = [
            'Presencial',
            'Virtual',
            'Híbrida',
        ];

        //determinar si hay filtros aplicados

        $hayFiltros = $busqueda !== ''
            || !empty($ejesSeleccionados)
            || !empty($modalidadesSeleccionadas);

       //enviar datos a la vista

        return view('catalogo.index', [
            'items' => $items,
            'categorias' => $categorias,
            'modalidades' => $modalidades,

            'busqueda' => $busqueda,
            'ejesSeleccionados' => $ejesSeleccionados,
            'modalidadesSeleccionadas' => $modalidadesSeleccionadas,
            'ordenSeleccionado' => $ordenSeleccionado,

            'hayFiltros' => $hayFiltros,
            'filtros' => $filtros,
        ]);
    }


    private function obtenerValorEnum(mixed $valor): ?string
    {
        if ($valor instanceof BackedEnum) {
            return (string) $valor->value;
        }

        return is_string($valor) ? $valor : null;
    }

    //convertir fecha en algo legible para la vista
    private function formatearFecha(
            DateTimeInterface|string|null $fecha
        ): ?string {
            if ($fecha === null) {
                return null;
            }

            return Carbon::parse($fecha)->format('d/m/Y');
    }

    public function show(int $id): View {
        // 1.obtener la accion publicada 
        $accion = AccionSustentabilidad::obtenerAccionPublicadaPorId($id);

        // 2. si no existe, responder con 404
        abort_if($accion === null, 404);

        // 3. transformar el modelo en un arreglo para la vista
        $item = [
            'id' => $accion->idAccion,
            'folio' => $accion->folio,
            'nombre' => $accion->nombre,
            'titulo' => $accion->nombre,
            'descripcion' => $accion->descripcion,
            'requerimientos' => $accion->requerimientos,

            'idEjeTrabajo' => $accion->idEjeTrabajo,
            'ejeTrabajo' => $accion->ejeTrabajo?->nombre ?? 'Sin eje asignado',

            'modalidad' => $this->obtenerValorEnum($accion->modalidad),
            'estado' => $this->obtenerValorEnum($accion->estadoActual),

            'fechaInicio' => $this->formatearFecha($accion->fechaInicio),
            'fechaFin' => $this->formatearFecha($accion->fechaTermino),
            'fechaRegistro' => $this->formatearFecha($accion->fechaRegistro),
        ];

        // 4. enviar el arreglo a la vista
        return view('catalogo.show', [
            'item' => $item,
        ]);
}
}
