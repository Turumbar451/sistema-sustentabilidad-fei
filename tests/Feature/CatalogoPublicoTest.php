<?php

declare(strict_types=1);

use App\Enums\EstadoAccionEnum;
use App\Enums\FormaTrabajoEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use App\Models\EstadoAccion;
use App\Models\Participacion;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(LazilyRefreshDatabase::class);

it('muestra el catálogo como página principal y conserva el menú compartido', function () {
    $this->get('/')
        ->assertOk()
        ->assertViewIs('accion-sustentabilidad.consultar')
        ->assertSee('Menú de la Coordinadora')
        ->assertSee('Resumen')
        ->assertSee('Registrar propuesta')
        ->assertSee('Evaluación de propuestas')
        ->assertSee('href="'.route('acciones.consultar').'"', false);
});

it('muestra acciones publicadas reales y oculta las propuestas pendientes', function () {
    AccionSustentabilidad::factory()->aprobada()->create([
        'nombre' => 'Jornada pública de recuperación verde',
        'descripcion' => 'Recuperación de espacios universitarios.',
    ]);
    AccionSustentabilidad::factory()->pendienteDeRevision()->create(['nombre' => 'Propuesta privada']);

    $this->get(route('acciones.consultar'))
        ->assertOk()
        ->assertViewIs('accion-sustentabilidad.consultar')
        ->assertSee('Explora las iniciativas')
        ->assertSee('Jornada pública de recuperación verde')
        ->assertSee('Recuperación de espacios universitarios.')
        ->assertDontSee('Propuesta privada')
        ->assertViewHas('acciones', fn ($acciones) => $acciones->total() === 1);
});

it('busca acciones por texto con los nombres de parámetro admitidos', function (string $parametro) {
    AccionSustentabilidad::factory()->aprobada()->create(['nombre' => 'Campaña de ahorro de agua']);
    AccionSustentabilidad::factory()->aprobada()->create(['nombre' => 'Separación de residuos']);

    $this->get(route('acciones.consultar', [$parametro => 'ahorro']))
        ->assertOk()
        ->assertSee('Campaña de ahorro de agua')
        ->assertDontSee('Separación de residuos');
})->with(['q', 'buscar']);

it('conecta cada filtro del catálogo con su criterio del dominio', function (string $criterio) {
    $eje = EjeTrabajo::factory()->create();
    $otroEje = EjeTrabajo::factory()->create();
    $atributos = [
        'idEjeTrabajo' => $eje->getKey(),
        'modalidad' => 'Virtual',
        'formaTrabajo' => FormaTrabajoEnum::Individual,
    ];
    $objetivo = AccionSustentabilidad::factory()->enEjecucion()->create($atributos);
    $diferencias = match ($criterio) {
        'ejes' => ['idEjeTrabajo' => $otroEje->getKey()],
        'modalidad' => ['modalidad' => 'Presencial'],
        'formaTrabajo' => ['formaTrabajo' => FormaTrabajoEnum::EquipoConformado],
        'estado' => ['idEstadoAccion' => EstadoAccion::idPara(EstadoAccionEnum::Aprobada)],
    };
    AccionSustentabilidad::factory()->enEjecucion()->create(array_replace($atributos, $diferencias));
    $valor = match ($criterio) {
        'ejes' => [$eje->getKey()],
        'modalidad' => 'Virtual',
        'formaTrabajo' => 'Individual',
        'estado' => 'En ejecucion',
    };

    $this->get(route('acciones.consultar', [$criterio => $valor]))
        ->assertOk()
        ->assertViewHas('acciones', fn ($acciones) => $acciones->total() === 1 && $acciones->first()->idAccion === $objetivo->getKey());
})->with(['ejes', 'modalidad', 'formaTrabajo', 'estado']);

it('aplica el orden seleccionado a los resultados', function (string $orden, array $folios) {
    AccionSustentabilidad::factory()->aprobada()->create([
        'folio' => 'ORD-A', 'nombre' => 'Alfa', 'fechaRegistro' => '2026-01-01 09:00:00', 'fechaInicio' => '2026-03-01',
    ]);
    AccionSustentabilidad::factory()->aprobada()->create([
        'folio' => 'ORD-B', 'nombre' => 'Beta', 'fechaRegistro' => '2026-01-03 09:00:00', 'fechaInicio' => null,
    ]);
    AccionSustentabilidad::factory()->aprobada()->create([
        'folio' => 'ORD-C', 'nombre' => 'Gamma', 'fechaRegistro' => '2026-01-02 09:00:00', 'fechaInicio' => '2026-02-01',
    ]);

    $this->get(route('acciones.consultar', ['orden' => $orden]))
        ->assertOk()
        ->assertViewHas('acciones', fn ($acciones) => $acciones->getCollection()->pluck('folio')->all() === $folios);
})->with([
    'recientes' => ['recientes', ['ORD-B', 'ORD-C', 'ORD-A']],
    'proximas' => ['proximas', ['ORD-C', 'ORD-A', 'ORD-B']],
    'alfabetico' => ['az', ['ORD-A', 'ORD-B', 'ORD-C']],
]);

it('pagina nueve acciones y conserva los filtros en los enlaces', function () {
    AccionSustentabilidad::factory()->aprobada()->count(10)->create(['nombre' => 'Iniciativa paginada']);
    Event::fake(['eloquent.retrieved: '.AccionSustentabilidad::class]);

    $this->get(route('acciones.consultar', ['q' => 'paginada']))
        ->assertOk()
        ->assertViewHas('acciones', fn ($acciones) => $acciones->count() === 9 && $acciones->total() === 10 && str_contains($acciones->nextPageUrl(), 'q=paginada'));
    Event::assertDispatchedTimes('eloquent.retrieved: '.AccionSustentabilidad::class, 9);

    $this->get(route('acciones.consultar', ['q' => 'paginada', 'page' => 2]))
        ->assertOk()
        ->assertViewHas('acciones', fn ($acciones) => $acciones->count() === 1 && $acciones->currentPage() === 2);
    Event::assertDispatchedTimes('eloquent.retrieved: '.AccionSustentabilidad::class, 10);
});

it('normaliza la búsqueda y los identificadores de eje recibidos como valores individuales', function (string $parametroEje) {
    $eje = EjeTrabajo::factory()->create();
    $objetivo = AccionSustentabilidad::factory()->aprobada()->create([
        'nombre' => 'Monitoreo de agua',
        'idEjeTrabajo' => $eje->getKey(),
    ]);
    AccionSustentabilidad::factory()->aprobada()->create(['nombre' => 'Otra acción de agua']);

    $this->get(route('acciones.consultar', [
        'buscar' => '  agua  ',
        $parametroEje => (string) $eje->getKey(),
        'orden' => 'az',
    ]))->assertOk()
        ->assertViewHas('acciones', fn ($acciones) => $acciones->modelKeys() === [$objetivo->getKey()])
        ->assertViewHas('filtros', [
            'q' => 'agua',
            'idEjeTrabajo' => [$eje->getKey()],
            'orden' => 'az',
        ])->assertViewHas('hayFiltros', true);
})->with(['ejes', 'idEjeTrabajo']);

it('prioriza los parámetros principales sobre los alias y elimina ejes repetidos', function () {
    $eje = EjeTrabajo::factory()->create();
    $otroEje = EjeTrabajo::factory()->create();
    $objetivo = AccionSustentabilidad::factory()->aprobada()->create([
        'nombre' => 'Monitoreo de agua',
        'idEjeTrabajo' => $eje->getKey(),
    ]);
    AccionSustentabilidad::factory()->aprobada()->create([
        'nombre' => 'Separación de residuos',
        'idEjeTrabajo' => $otroEje->getKey(),
    ]);

    $this->get(route('acciones.consultar', [
        'q' => 'Monitoreo',
        'buscar' => 'residuos',
        'ejes' => [(string) $eje->getKey(), (string) $eje->getKey()],
        'idEjeTrabajo' => $otroEje->getKey(),
    ]))->assertOk()
        ->assertViewHas('acciones', fn ($acciones) => $acciones->modelKeys() === [$objetivo->getKey()])
        ->assertViewHas('filtros', [
            'q' => 'Monitoreo',
            'idEjeTrabajo' => [$eje->getKey()],
            'orden' => 'recientes',
        ]);
});

it('mantiene la consulta sin filtros al cambiar solo el orden o la página', function (array $parametros) {
    $this->get(route('acciones.consultar', $parametros))
        ->assertOk()->assertViewHas('hayFiltros', false);
})->with([
    'orden' => [['orden' => 'az']],
    'página' => [['page' => 2]],
]);

it('muestra el detalle publicado con forma de trabajo y participantes', function () {
    $accion = AccionSustentabilidad::factory()->finalizada()->create([
        'nombre' => 'Acción concluida',
        'formaTrabajo' => FormaTrabajoEnum::EquipoConformado,
        'requerimientos' => 'Material reutilizable',
        'fechaInicio' => '2026-11-12',
        'fechaTermino' => null,
        'fechaRegistro' => '2026-11-01 10:00:00',
    ]);
    Participacion::factory()->create(['idAccion' => $accion->getKey()]);

    $this->get(route('acciones.detalle', ['accionSustentabilidad' => $accion]))
        ->assertOk()
        ->assertViewIs('accion-sustentabilidad.detalle')
        ->assertSee('Acción concluida')
        ->assertSee('Equipo conformado')
        ->assertSee('Material reutilizable')
        ->assertSee('12/11/2026')
        ->assertSee('01/11/2026')
        ->assertSee('Por definir')
        ->assertViewHas('accion', fn (AccionSustentabilidad $detalle) => $detalle->participaciones_count === 1 && $detalle->estadoAccion->nombre === EstadoAccionEnum::Finalizada);
});

it('responde 404 para el detalle de una acción no pública', function (EstadoAccionEnum $estado) {
    $accion = AccionSustentabilidad::factory()->enEstado($estado)->create();

    $this->get(route('acciones.detalle', ['accionSustentabilidad' => $accion]))->assertNotFound();
})->with([EstadoAccionEnum::PendienteDeRevision, EstadoAccionEnum::CorreccionesSolicitadas, EstadoAccionEnum::Rechazada]);

it('responde 404 cuando la acción no existe o el identificador no es numérico', function (string $id) {
    $this->get(route('acciones.detalle', ['accionSustentabilidad' => $id]))->assertNotFound();
})->with(['999999', 'invalido']);

it('rechaza filtros malformados antes de consultar el catálogo', function (string $campo, mixed $valor) {
    $this->getJson(route('acciones.consultar', [$campo => $valor]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors($campo === 'ejes' ? 'ejes.0' : $campo);
})->with([
    'texto como arreglo' => ['q', ['invalido']],
    'eje negativo' => ['ejes', [-1]],
    'modalidad como arreglo' => ['modalidad', ['Virtual']],
    'forma desconocida' => ['formaTrabajo', 'Colectiva'],
    'estado desconocido' => ['estado', 'Borrador'],
    'orden malicioso' => ['orden', 'nombre desc; drop table Rol'],
    'pagina cero' => ['page', 0],
]);

it('escapa contenido almacenado al mostrar el catálogo y el detalle', function () {
    $nombre = '<script>alert("xss")</script>';
    $accion = AccionSustentabilidad::factory()->aprobada()->create(['nombre' => $nombre]);

    $this->get(route('acciones.consultar'))->assertOk()->assertSee($nombre)->assertDontSee($nombre, false);
    $this->get(route('acciones.detalle', ['accionSustentabilidad' => $accion]))->assertOk()->assertSee($nombre)->assertDontSee($nombre, false);
});
