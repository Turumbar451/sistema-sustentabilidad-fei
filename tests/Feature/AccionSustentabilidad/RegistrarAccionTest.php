<?php

declare(strict_types=1);

use App\Actions\AccionSustentabilidad\RegistrarPropuesta;
use App\Enums\EstadoAccionEnum;
use App\Enums\FormaTrabajoEnum;
use App\Enums\RolSistemaEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use App\Models\EstadoAccion;
use App\Models\Evidencia;
use App\Models\UsuarioInstitucional;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCreateDirectory;

uses(LazilyRefreshDatabase::class);

function datosRegistroDeAccion(EjeTrabajo $eje): array
{
    return [
        'nombre' => 'Jornada de recuperación verde',
        'descripcion' => 'Recuperación de espacios universitarios con materiales reutilizables.',
        'idEjeTrabajo' => $eje->getKey(),
        'modalidad' => 'Presencial',
        'formaTrabajo' => 'Equipo conformado',
        'fechaInicio' => '2026-12-01',
        'fechaTermino' => '2026-12-01',
        'requerimientos' => 'Herramientas de jardinería.',
    ];
}

it('muestra el formulario antes de resolver el detalle y utiliza los ejes reales ordenados', function () {
    $usuario = UsuarioInstitucional::factory()->estudiante()->create();
    $zeta = EjeTrabajo::factory()->create(['nombre' => 'Zeta']);
    $alfa = EjeTrabajo::factory()->create(['nombre' => 'Alfa']);

    $this->actingAs($usuario)->get(route('acciones.formulario-registro'))
        ->assertOk()
        ->assertViewIs('accion-sustentabilidad.registrar')
        ->assertViewHas('ejesTrabajo', fn ($ejes) => $ejes->modelKeys() === [$alfa->getKey(), $zeta->getKey()])
        ->assertSee('action="'.route('acciones.registrar').'"', false)
        ->assertSee('enctype="multipart/form-data"', false)
        ->assertSee('name="_token"', false)
        ->assertSee('value="'.$alfa->getKey().'"', false)
        ->assertSee('name="formaTrabajo"', false)
        ->assertSee('Nombre de la propuesta')
        ->assertDontSee('name="accion"', false)
        ->assertDontSee('value="enviar"', false)
        ->assertSee('Mis propuestas')
        ->assertDontSee('Evaluación de propuestas')
        ->assertDontSee('Menú de la Coordinadora');
});

it('explica la revisión de la Coordinadora antes de publicar propuestas de estudiantes y maestros', function (RolSistemaEnum $rol) {
    $usuario = UsuarioInstitucional::factory()->create();
    $usuario->asignarRol($rol);

    $this->actingAs($usuario)->get(route('acciones.formulario-registro'))
        ->assertOk()
        ->assertSee('Registro para evaluación')
        ->assertSee('Tu propuesta queda Pendiente de revisión para evaluación de la Coordinadora antes de publicarse.')
        ->assertSee('La propuesta se registra como Pendiente de revisión.')
        ->assertSee('La Coordinadora evalúa la propuesta antes de publicarla en el catálogo público.')
        ->assertDontSee('Aprobación y publicación')
        ->assertDontSee('La propuesta queda aprobada directamente.');
})->with([
    'estudiante' => [RolSistemaEnum::Estudiante],
    'maestro' => [RolSistemaEnum::Maestro],
]);

it('explica la aprobación directa y la publicación al registrar una propuesta de la Coordinadora', function () {
    $usuario = UsuarioInstitucional::factory()->create();
    $usuario->asignarRol(RolSistemaEnum::CoordinadoraSustentabilidad);

    $this->actingAs($usuario)->get(route('acciones.formulario-registro'))
        ->assertOk()
        ->assertSee('Aprobación y publicación')
        ->assertSee('Tu propuesta queda aprobada directamente y se publica en el catálogo.')
        ->assertSee('La propuesta queda aprobada directamente.')
        ->assertSee('La acción de sustentabilidad se publica en el catálogo público.')
        ->assertDontSee('Registro para evaluación')
        ->assertDontSee('Pendiente de revisión')
        ->assertDontSee('La Coordinadora evalúa la propuesta antes de publicarla en el catálogo público.');
});

it('rechaza el registro sin usuario fuera de local y no guarda datos ni archivos', function () {
    Storage::fake('public');

    $this->post(route('acciones.registrar'))->assertForbidden();

    $this->assertDatabaseCount('AccionSustentabilidad', 0);
    $this->assertDatabaseCount('Evidencia', 0);
    Storage::disk('public')->assertEmpty();
});

it('registra la propuesta del usuario actual como pendiente e ignora campos controlados por el servidor', function (string $forma) {
    $this->travelTo('2026-11-18 10:00:00');
    $usuario = UsuarioInstitucional::factory()->estudiante()->create();
    $otroUsuario = UsuarioInstitucional::factory()->create();
    $eje = EjeTrabajo::factory()->create();
    $estadoAprobada = EstadoAccion::idPara(EstadoAccionEnum::Aprobada);
    $datos = array_replace(datosRegistroDeAccion($eje), [
        'nombre' => str_repeat('N', 200),
        'formaTrabajo' => $forma,
        'requerimientos' => null,
        'idUsuarioSolicitante' => $otroUsuario->getKey(),
        'idEstadoAccion' => $estadoAprobada,
        'folio' => 'FOLIO-ALTERADO',
        'fechaRegistro' => '2000-01-01 00:00:00',
    ]);

    $respuesta = $this->actingAs($usuario)->post(route('acciones.registrar'), $datos);

    $accion = AccionSustentabilidad::query()->with('estadoAccion')->sole();
    $mensaje = "La propuesta {$accion->folio} se registró como Pendiente de revision.";
    $respuesta->assertRedirect(route('acciones.consultar'))->assertSessionHas('status', $mensaje);
    expect($accion->idUsuarioSolicitante)->toBe($usuario->getKey());
    expect($accion->estadoAccion->nombre)->toBe(EstadoAccionEnum::PendienteDeRevision);
    expect($accion->formaTrabajo)->toBe(FormaTrabajoEnum::from($forma));
    expect($accion->modalidad)->toBe('Presencial');
    expect($accion->folio)->toStartWith('FEI-2026-')->not->toBe('FOLIO-ALTERADO');
    expect($accion->fechaRegistro->format('Y-m-d H:i:s'))->toBe('2026-11-18 10:00:00');
    expect($accion->requerimientos)->toBeNull();
    $this->assertDatabaseCount('HistorialEstadoAccion', 1);
    $this->assertDatabaseHas('HistorialEstadoAccion', [
        'idAccion' => $accion->getKey(),
        'idEstadoAccion' => $accion->idEstadoAccion,
        'idUsuarioResponsable' => $usuario->getKey(),
        'fechaCambio' => '2026-11-18 10:00:00',
    ]);
    $this->assertDatabaseCount('Evidencia', 0);
    $this->get(route('acciones.consultar'))->assertOk()->assertSee($mensaje)->assertDontSee($datos['nombre']);
})->with(['Individual', 'Equipo conformado']);

it('aprueba directamente la propuesta de la Coordinadora sin evidencias', function () {
    Storage::fake('public');
    $usuario = UsuarioInstitucional::factory()->create();
    $usuario->asignarRol(RolSistemaEnum::CoordinadoraSustentabilidad);
    $eje = EjeTrabajo::factory()->create();
    $datos = [
        ...datosRegistroDeAccion($eje),
        'idEstadoAccion' => EstadoAccion::idPara(EstadoAccionEnum::PendienteDeRevision),
    ];

    $respuesta = $this->actingAs($usuario)->post(route('acciones.registrar'), $datos);

    $accion = AccionSustentabilidad::query()->with(['estadoAccion', 'evidencias'])->sole();
    $mensaje = "La propuesta {$accion->folio} se registró como Aprobada.";
    $respuesta->assertRedirect(route('acciones.consultar'))->assertSessionHas('status', $mensaje);
    expect($accion->estadoAccion->nombre)->toBe(EstadoAccionEnum::Aprobada);
    expect($accion->idUsuarioSolicitante)->toBe($usuario->getKey());
    expect($accion->evidencias)->toBeEmpty();
    $this->assertDatabaseCount('HistorialEstadoAccion', 1);
    $this->assertDatabaseHas('HistorialEstadoAccion', [
        'idAccion' => $accion->getKey(),
        'idEstadoAccion' => EstadoAccion::idPara(EstadoAccionEnum::Aprobada),
        'idUsuarioResponsable' => $usuario->getKey(),
        'motivo' => 'Registro y aprobación directa de la Coordinadora de Sustentabilidad.',
    ]);

    Storage::disk('public')->assertEmpty();

    $this->get(route('acciones.consultar'))->assertOk()->assertSee($datos['nombre']);
});

it('guarda evidencias PDF JPG y PNG hasta cinco MB con rutas generadas y el usuario que las carga', function () {
    Storage::fake('public');
    $usuario = UsuarioInstitucional::factory()->create();
    $usuario->asignarRol(RolSistemaEnum::CoordinadoraSustentabilidad);
    $eje = EjeTrabajo::factory()->create();
    $archivos = [
        UploadedFile::fake()->create('apoyo.pdf', 5120, 'application/pdf'),
        UploadedFile::fake()->create('foto.jpg', 128, 'image/jpeg'),
        UploadedFile::fake()->create('grafica.png', 128, 'image/png'),
    ];

    $this->actingAs($usuario)->post(route('acciones.registrar'), [
        ...datosRegistroDeAccion($eje),
        'evidencias' => $archivos,
    ])->assertRedirect(route('acciones.consultar'));

    $accion = AccionSustentabilidad::query()->with(['evidencias', 'estadoAccion'])->sole();
    expect($accion->estadoAccion->nombre)->toBe(EstadoAccionEnum::Aprobada);
    expect($accion->evidencias)->toHaveCount(3);
    expect($accion->evidencias->pluck('nombreArchivo')->all())->toBe(['apoyo.pdf', 'foto.jpg', 'grafica.png']);
    expect($accion->evidencias->pluck('tipoArchivo')->all())->toBe(['application/pdf', 'image/jpeg', 'image/png']);

    foreach ($accion->evidencias as $evidencia) {
        Storage::disk('public')->assertExists($evidencia->ubicacionArchivo);
        expect($evidencia->idUsuarioCarga)->toBe($usuario->getKey());
        expect(basename($evidencia->ubicacionArchivo))->not->toBe($evidencia->nombreArchivo);
    }
});

it('valida los campos del dominio antes de insertar la propuesta', function (string $campo, mixed $valor, string $mensaje) {
    $usuario = UsuarioInstitucional::factory()->estudiante()->create();
    $eje = EjeTrabajo::factory()->create();

    $this->actingAs($usuario)->postJson(route('acciones.registrar'), [
        ...datosRegistroDeAccion($eje),
        $campo => $valor,
    ])->assertUnprocessable()->assertJsonValidationErrors([$campo => $mensaje]);

    $this->assertDatabaseCount('AccionSustentabilidad', 0);
    $this->assertDatabaseCount('HistorialEstadoAccion', 0);
})->with([
    'nombre obligatorio' => ['nombre', '', 'Escribe el nombre de la propuesta.'],
    'nombre texto' => ['nombre', ['incorrecto'], 'El nombre debe ser un texto.'],
    'nombre supera la columna' => ['nombre', str_repeat('N', 201), 'El nombre no puede superar los 200 caracteres.'],
    'descripción obligatoria' => ['descripcion', '', 'Describe la propuesta.'],
    'descripción texto' => ['descripcion', ['incorrecto'], 'La descripción debe ser un texto.'],
    'descripción extensa' => ['descripcion', str_repeat('D', 1201), 'La descripción no puede superar los 1200 caracteres.'],
    'eje obligatorio' => ['idEjeTrabajo', '', 'Selecciona un eje de trabajo.'],
    'eje numérico' => ['idEjeTrabajo', 'agua', 'El eje de trabajo debe ser un identificador válido.'],
    'eje inexistente' => ['idEjeTrabajo', 2147483647, 'El eje de trabajo seleccionado no existe.'],
    'modalidad diferente de forma de trabajo' => ['modalidad', 'Individual', 'Selecciona Presencial, Virtual o Híbrida como modalidad.'],
    'forma de trabajo desconocida' => ['formaTrabajo', 'Virtual', 'Selecciona Individual o Equipo conformado como forma de trabajo.'],
    'inicio inválido' => ['fechaInicio', '2026-02-30', 'La fecha de inicio debe tener el formato AAAA-MM-DD y ser válida.'],
    'término inválido' => ['fechaTermino', 'incorrecta', 'La fecha de término debe tener el formato AAAA-MM-DD y ser válida.'],
    'término anterior' => ['fechaTermino', '2026-11-30', 'La fecha de término debe ser igual o posterior a la fecha de inicio.'],
    'requerimientos texto' => ['requerimientos', ['incorrecto'], 'Los requerimientos deben ser un texto.'],
    'requerimientos extensos' => ['requerimientos', str_repeat('R', 601), 'Los requerimientos no pueden superar los 600 caracteres.'],
    'evidencias como lista' => ['evidencias', 'incorrectas', 'Las evidencias deben enviarse como una lista de archivos.'],
]);

it('rechaza evidencias no permitidas o mayores de cinco MB sin guardar archivos', function (string $caso) {
    Storage::fake('public');
    $usuario = UsuarioInstitucional::factory()->estudiante()->create();
    $eje = EjeTrabajo::factory()->create();
    [$archivo, $mensaje] = match ($caso) {
        'tipo inválido' => [UploadedFile::fake()->create('programa.exe', 10, 'application/x-msdownload'), 'Las evidencias solo pueden ser archivos PDF, JPG o PNG.'],
        'tamaño excesivo' => [UploadedFile::fake()->create('apoyo.pdf', 5121, 'application/pdf'), 'Cada evidencia puede pesar como máximo 5 MB.'],
    };

    $this->actingAs($usuario)->postJson(route('acciones.registrar'), [
        ...datosRegistroDeAccion($eje),
        'evidencias' => [$archivo],
    ])->assertUnprocessable()->assertJsonValidationErrors(['evidencias.0' => $mensaje]);

    $this->assertDatabaseCount('AccionSustentabilidad', 0);
    $this->assertDatabaseCount('Evidencia', 0);
    Storage::disk('public')->assertEmpty();
})->with(['tipo inválido', 'tamaño excesivo']);

it('inspecciona el contenido real de una evidencia aunque el cliente la presente como JPG', function () {
    Storage::fake('public');
    $usuario = UsuarioInstitucional::factory()->estudiante()->create();
    $eje = EjeTrabajo::factory()->create();
    $temporal = UploadedFile::fake()->createWithContent('foto.jpg', '<?php echo "archivo inválido";');
    $archivo = new UploadedFile($temporal->getPathname(), 'foto.jpg', 'image/jpeg', null, true);

    $this->actingAs($usuario)->postJson(route('acciones.registrar'), [
        ...datosRegistroDeAccion($eje),
        'evidencias' => [$archivo],
    ])->assertUnprocessable()->assertJsonValidationErrors([
        'evidencias.0' => 'Las evidencias solo pueden ser archivos PDF, JPG o PNG.',
    ]);

    $this->assertDatabaseCount('AccionSustentabilidad', 0);
    $this->assertDatabaseCount('Evidencia', 0);
    Storage::disk('public')->assertEmpty();
});

it('conserva los datos del formulario y muestra errores escapando el contenido anterior', function () {
    $usuario = UsuarioInstitucional::factory()->estudiante()->create();
    $eje = EjeTrabajo::factory()->create();
    $nombre = '<script>alert("xss")</script>';
    $formulario = route('acciones.formulario-registro');

    $respuesta = $this->actingAs($usuario)->from($formulario)->post(route('acciones.registrar'), [
        ...datosRegistroDeAccion($eje),
        'nombre' => $nombre,
        'fechaTermino' => '2026-11-30',
    ]);
    $respuesta->assertRedirect($formulario)
        ->assertSessionHasErrors(['fechaTermino' => 'La fecha de término debe ser igual o posterior a la fecha de inicio.'])
        ->assertSessionHasInput('nombre', $nombre);

    $this->withCookie(config('session.cookie'), $respuesta->getCookie(config('session.cookie'))->getValue())
        ->get($formulario)->assertOk()->assertSee($nombre)->assertDontSee($nombre, false)
        ->assertSee('La fecha de término debe ser igual o posterior a la fecha de inicio.');
});

it('genera folios diferentes para propuestas registradas en el mismo instante', function () {
    $this->travelTo('2026-11-18 10:00:00');
    $usuario = UsuarioInstitucional::factory()->create();
    $eje = EjeTrabajo::factory()->create();
    $datos = [...datosRegistroDeAccion($eje), 'idUsuarioSolicitante' => $usuario->getKey()];

    $registro = new RegistrarPropuesta;
    $primera = $registro->ejecutar($datos);
    $segunda = $registro->ejecutar($datos);

    expect($primera->folio)->not->toBe($segunda->folio);
    $this->assertDatabaseCount('AccionSustentabilidad', 2);
});

it('revierte la propuesta y su historial si no puede escribir una evidencia', function () {
    $disco = Storage::fake('public');
    $disco->put('evidencias', 'Archivo previo que impide crear la carpeta.');
    $usuario = UsuarioInstitucional::factory()->create();
    $eje = EjeTrabajo::factory()->create();
    $datos = [...datosRegistroDeAccion($eje), 'idUsuarioSolicitante' => $usuario->getKey()];

    expect(fn () => (new RegistrarPropuesta)->ejecutar($datos, [
        UploadedFile::fake()->create('apoyo.pdf', 10, 'application/pdf'),
    ]))->toThrow(UnableToCreateDirectory::class);

    $this->assertDatabaseCount('AccionSustentabilidad', 0);
    $this->assertDatabaseCount('HistorialEstadoAccion', 0);
    $this->assertDatabaseCount('Evidencia', 0);
    expect($disco->get('evidencias'))->toBe('Archivo previo que impide crear la carpeta.');
});

it('borra los archivos nuevos y revierte todos los registros si falla la segunda evidencia', function () {
    $disco = Storage::fake('public');
    $usuario = UsuarioInstitucional::factory()->create();
    $eje = EjeTrabajo::factory()->create();
    $datos = [...datosRegistroDeAccion($eje), 'idUsuarioSolicitante' => $usuario->getKey()];
    $eventosOriginales = Evidencia::getEventDispatcher();
    Evidencia::setEventDispatcher(clone $eventosOriginales);
    $evidenciasCreadas = 0;
    Evidencia::creating(function () use (&$evidenciasCreadas): void {
        $evidenciasCreadas++;

        if ($evidenciasCreadas === 2) {
            throw new RuntimeException('Fallo al registrar la segunda evidencia.');
        }
    });

    try {
        expect(fn () => (new RegistrarPropuesta)->ejecutar($datos, [
            UploadedFile::fake()->create('primera.pdf', 10, 'application/pdf'),
            UploadedFile::fake()->create('segunda.pdf', 10, 'application/pdf'),
        ]))->toThrow(RuntimeException::class, 'Fallo al registrar la segunda evidencia.');
    } finally {
        Evidencia::setEventDispatcher($eventosOriginales);
    }

    $this->assertDatabaseCount('AccionSustentabilidad', 0);
    $this->assertDatabaseCount('HistorialEstadoAccion', 0);
    $this->assertDatabaseCount('Evidencia', 0);
    $disco->assertEmpty();
});
