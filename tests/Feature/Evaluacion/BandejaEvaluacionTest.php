<?php

declare(strict_types=1);

use App\Enums\EstadoAccionEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use App\Models\EstadoAccion;
use App\Models\Evidencia;
use App\Models\UsuarioInstitucional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
});

it('permite a la coordinadora consultar las propuestas relevantes en orden de revisión', function (): void {
    $coordinadora = UsuarioInstitucional::factory()->coordinadora()->create();
    $solicitante = UsuarioInstitucional::factory()->estudiante()->create([
        'nombreCompleto' => 'Andrea Solicitante',
    ]);
    $eje = EjeTrabajo::factory()->create(['nombre' => 'Cuidado del entorno']);
    $pendiente = EstadoAccion::query()->firstOrCreate(
        ['nombre' => EstadoAccionEnum::PendienteDeRevision->value],
        ['descripcion' => 'Propuesta recibida para revisión.'],
    );
    $correcciones = EstadoAccion::query()->firstOrCreate(
        ['nombre' => EstadoAccionEnum::CorreccionesSolicitadas->value],
        ['descripcion' => 'Propuesta con ajustes solicitados.'],
    );
    $propuestas = AccionSustentabilidad::factory()
        ->for($solicitante, 'solicitante')
        ->for($eje, 'ejeTrabajo');
    $enCorreccion = $propuestas->for($correcciones, 'estadoAccion')->create([
        'nombre' => 'Propuesta con correcciones',
        'fechaRegistro' => '2026-11-01 09:00:00',
    ]);
    $reciente = $propuestas->for($pendiente, 'estadoAccion')->create([
        'nombre' => 'Propuesta pendiente reciente',
        'fechaRegistro' => '2026-11-20 09:00:00',
    ]);
    $antigua = $propuestas->for($pendiente, 'estadoAccion')->create([
        'folio' => 'EVAL-001',
        'nombre' => 'Propuesta pendiente antigua',
        'fechaRegistro' => '2026-11-15 09:00:00',
    ]);
    $excluidas = [];

    foreach ([EstadoAccionEnum::Aprobada, EstadoAccionEnum::Rechazada, EstadoAccionEnum::EnEjecucion, EstadoAccionEnum::Finalizada] as $estado) {
        $estadoAccion = EstadoAccion::query()->firstOrCreate(
            ['nombre' => $estado->value],
            ['descripcion' => 'Estado fuera de la bandeja de evaluación.'],
        );
        $excluidas[] = $propuestas->for($estadoAccion, 'estadoAccion')->create([
            'nombre' => 'Propuesta excluida '.$estado->value,
        ])->nombre;
    }

    $respuesta = $this->actingAs($coordinadora)->get(route('evaluacion.propuestas.index'));

    $respuesta->assertOk()
        ->assertSeeInOrder([$antigua->nombre, $reciente->nombre, $enCorreccion->nombre])
        ->assertSeeText([
            'EVAL-001',
            'Andrea Solicitante',
            'Cuidado del entorno',
            'Pendiente de revisión',
            'Correcciones solicitadas',
            '15/11/2026',
        ])
        ->assertSee('href="'.route('evaluacion.propuestas.detalle', $antigua).'"', false)
        ->assertSee('href="'.route('evaluacion.propuestas.index').'"', false)
        ->assertDontSeeText($excluidas);
});

it('permite a la coordinadora revisar los datos completos y el historial cronológico de otra persona', function (): void {
    $coordinadora = UsuarioInstitucional::factory()->coordinadora()->create([
        'nombreCompleto' => 'Carolina Coordinadora',
    ]);
    $solicitante = UsuarioInstitucional::factory()->estudiante()->create([
        'nombreCompleto' => 'Andrea Solicitante',
        'correoInstitucional' => 'andrea@estudiantes.uv.mx',
        'identificadorInstitucional' => 'zs26001234',
        'telefonoContacto' => '2281234567',
    ]);
    $eje = EjeTrabajo::factory()->create(['nombre' => 'Cuidado del entorno']);
    $pendiente = EstadoAccion::query()->firstOrCreate(
        ['nombre' => EstadoAccionEnum::PendienteDeRevision->value],
        ['descripcion' => 'Propuesta recibida para revisión.'],
    );
    $correcciones = EstadoAccion::query()->firstOrCreate(
        ['nombre' => EstadoAccionEnum::CorreccionesSolicitadas->value],
        ['descripcion' => 'Propuesta con ajustes solicitados.'],
    );
    $accion = AccionSustentabilidad::factory()
        ->for($solicitante, 'solicitante')
        ->for($eje, 'ejeTrabajo')
        ->for($correcciones, 'estadoAccion')
        ->create([
            'folio' => 'EVAL-002',
            'nombre' => 'Huerto universitario',
            'descripcion' => 'Cultivo comunitario <script>alert("propuesta")</script>',
            'modalidad' => 'Presencial',
            'fechaRegistro' => '2026-11-20 09:15:00',
            'fechaInicio' => '2026-12-01',
            'fechaTermino' => '2026-12-15',
            'requerimientos' => 'Semillas, herramientas y acceso al jardín.',
        ]);
    $evidencia = Evidencia::factory()->create([
        'idAccion' => $accion->getKey(),
        'idUsuarioCarga' => $solicitante->getKey(),
        'nombreArchivo' => 'plan-del-huerto.pdf',
        'ubicacionArchivo' => 'evidencias/plan-del-huerto.pdf',
    ]);
    $accion->historialEstados()->create([
        'idEstadoAccion' => $correcciones->getKey(),
        'idUsuarioResponsable' => $coordinadora->getKey(),
        'fechaCambio' => '2026-11-25 12:00:00',
        'motivo' => 'Completar las medidas de accesibilidad.',
    ]);
    $accion->historialEstados()->create([
        'idEstadoAccion' => $pendiente->getKey(),
        'idUsuarioResponsable' => $solicitante->getKey(),
        'fechaCambio' => '2026-11-20 09:15:00',
        'motivo' => 'Registro institucional inicial.',
    ]);

    $respuesta = $this->actingAs($coordinadora)->get(route('evaluacion.propuestas.detalle', $accion));

    $respuesta->assertOk()
        ->assertSeeText([
            'EVAL-002',
            'Huerto universitario',
            'Cuidado del entorno',
            'Correcciones solicitadas',
            'Andrea Solicitante',
            'andrea@estudiantes.uv.mx',
            'zs26001234',
            '2281234567',
            'Presencial',
            $accion->formaTrabajo->value,
            '20/11/2026',
            '01/12/2026',
            '15/12/2026',
            'Semillas, herramientas y acceso al jardín.',
            'plan-del-huerto.pdf',
            'Carolina Coordinadora',
            '20/11/2026 09:15',
            '25/11/2026 12:00',
        ])
        ->assertSeeInOrder(['Registro institucional inicial.', 'Completar las medidas de accesibilidad.'])
        ->assertSee('href="'.Storage::disk('public')->url($evidencia->ubicacionArchivo).'"', false)
        ->assertSee($accion->descripcion)
        ->assertDontSee('<script>alert("propuesta")</script>', false);
});

it('muestra una bandeja vacía cuando no hay propuestas por evaluar', function (): void {
    $coordinadora = UsuarioInstitucional::factory()->coordinadora()->create();

    $respuesta = $this->actingAs($coordinadora)->get(route('evaluacion.propuestas.index'));

    $respuesta->assertOk()->assertSeeText('No hay propuestas para evaluar');
});

it('rechaza a visitantes en las rutas de evaluación', function (string $ruta): void {
    $accion = AccionSustentabilidad::factory()->create();

    $respuesta = $this->get(sprintf($ruta, $accion->getKey()));

    $respuesta->assertForbidden();
})->with([
    'bandeja' => '/evaluacion/propuestas',
    'detalle' => '/evaluacion/propuestas/%d',
]);

it('rechaza a los roles sin permiso en las rutas de evaluación', function (string $estadoUsuario, string $ruta): void {
    $usuario = UsuarioInstitucional::factory()->{$estadoUsuario}()->create();
    $accion = AccionSustentabilidad::factory()->create();

    $respuesta = $this->actingAs($usuario)->get(sprintf($ruta, $accion->getKey()));

    $respuesta->assertForbidden();
})->with([
    'Estudiante' => 'estudiante',
    'Maestro' => 'maestro',
    'Director' => 'director',
])->with([
    'bandeja' => '/evaluacion/propuestas',
    'detalle' => '/evaluacion/propuestas/%d',
]);

it('oculta el menú de evaluación a los roles sin permiso', function (string $estadoUsuario): void {
    $usuario = UsuarioInstitucional::factory()->{$estadoUsuario}()->create();

    $respuesta = $this->actingAs($usuario)->get(route('acciones.consultar'));

    $respuesta->assertOk()->assertDontSee('href="'.route('evaluacion.propuestas.index').'"', false);
})->with([
    'Estudiante' => 'estudiante',
    'Maestro' => 'maestro',
    'Director' => 'director',
]);

it('oculta el menú de evaluación a visitantes', function (): void {
    $respuesta = $this->get(route('acciones.consultar'));

    $respuesta->assertOk()->assertDontSee('href="'.route('evaluacion.propuestas.index').'"', false);
});
