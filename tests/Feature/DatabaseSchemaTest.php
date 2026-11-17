<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(LazilyRefreshDatabase::class);

// El esquema físico es un contrato explícito del DI-02, independiente de Eloquent.
it('crea solamente las nueve tablas DI-02 y el registro técnico de migraciones', function () {
    // MySQL en Windows puede normalizar los nombres mediante lower_case_table_names.
    $tablas = array_map(strtolower(...), Schema::getTableListing(DB::connection()->getDatabaseName(), schemaQualified: false));

    expect($tablas)->toEqualCanonicalizing([
        'usuarioinstitucional',
        'rol',
        'usuariorol',
        'ejetrabajo',
        'estadoaccion',
        'accionsustentabilidad',
        'historialestadoaccion',
        'evidencia',
        'participacion',
        'migrations',
    ]);
});

it('respeta las columnas tipos longitudes y nulabilidad definidos en DI-02', function (string $tabla, array $esperadas) {
    $columnas = collect(Schema::getColumns($tabla))->keyBy('name');

    expect($columnas->keys()->all())->toBe(array_keys($esperadas));

    foreach ($esperadas as $nombre => [$tipo, $nullable]) {
        $columna = $columnas->get($nombre);
        $tipoReal = preg_replace('/int\(\d+\)/', 'int', strtolower($columna['type']));

        expect([$tipoReal, $columna['nullable'], $columna['auto_increment']])
            ->toBe([$tipo, $nullable, $nombre === array_key_first($esperadas)], $tabla.'.'.$nombre);
    }

    $primaria = collect(Schema::getIndexes($tabla))->firstWhere('primary', true);
    expect($primaria['columns'])->toBe([array_key_first($esperadas)]);
})->with([
    'UsuarioInstitucional' => ['UsuarioInstitucional', [
        'idUsuario' => ['int unsigned', false],
        'identificadorInstitucional' => ['varchar(100)', false],
        'nombreCompleto' => ['varchar(150)', false],
        'correoInstitucional' => ['varchar(255)', false],
        'telefonoContacto' => ['varchar(30)', true],
    ]],
    'Rol' => ['Rol', [
        'idRol' => ['int unsigned', false],
        'nombre' => ['varchar(50)', false],
        'descripcion' => ['text', true],
    ]],
    'UsuarioRol' => ['UsuarioRol', [
        'idUsuarioRol' => ['int unsigned', false],
        'idUsuario' => ['int unsigned', false],
        'idRol' => ['int unsigned', false],
        'fechaAsignacion' => ['datetime', false],
        'fechaFin' => ['datetime', true],
    ]],
    'EjeTrabajo' => ['EjeTrabajo', [
        'idEjeTrabajo' => ['int unsigned', false],
        'nombre' => ['varchar(150)', false],
        'descripcion' => ['text', true],
    ]],
    'EstadoAccion' => ['EstadoAccion', [
        'idEstadoAccion' => ['int unsigned', false],
        'nombre' => ['varchar(50)', false],
        'descripcion' => ['text', true],
    ]],
    'AccionSustentabilidad' => ['AccionSustentabilidad', [
        'idAccion' => ['int unsigned', false],
        'folio' => ['varchar(50)', false],
        'nombre' => ['varchar(200)', false],
        'descripcion' => ['text', false],
        'modalidad' => ['varchar(100)', false],
        'formaTrabajo' => ['varchar(30)', false],
        'fechaRegistro' => ['datetime', false],
        'fechaInicio' => ['date', true],
        'fechaTermino' => ['date', true],
        'requerimientos' => ['text', true],
        'idEjeTrabajo' => ['int unsigned', false],
        'idUsuarioSolicitante' => ['int unsigned', false],
        'idEstadoAccion' => ['int unsigned', false],
    ]],
    'HistorialEstadoAccion' => ['HistorialEstadoAccion', [
        'idHistorial' => ['int unsigned', false],
        'idAccion' => ['int unsigned', false],
        'idEstadoAccion' => ['int unsigned', false],
        'idUsuarioResponsable' => ['int unsigned', true],
        'fechaCambio' => ['datetime', false],
        'motivo' => ['text', true],
    ]],
    'Evidencia' => ['Evidencia', [
        'idEvidencia' => ['int unsigned', false],
        'nombreArchivo' => ['varchar(255)', false],
        'ubicacionArchivo' => ['varchar(500)', false],
        'tipoArchivo' => ['varchar(100)', false],
        'descripcion' => ['text', true],
        'fechaCarga' => ['datetime', false],
        'idAccion' => ['int unsigned', false],
        'idUsuarioCarga' => ['int unsigned', false],
    ]],
    'Participacion' => ['Participacion', [
        'idParticipacion' => ['int unsigned', false],
        'fechaInscripcion' => ['datetime', false],
        'idUsuario' => ['int unsigned', false],
        'idAccion' => ['int unsigned', false],
    ]],
]);

it('mantiene las referencias y reglas de actualización y eliminación del DI-02', function (string $tabla, array $esperadas) {
    $referencias = collect(Schema::getForeignKeys($tabla))->mapWithKeys(fn (array $fk): array => [
        $fk['columns'][0] => [
            strtolower($fk['foreign_table']),
            $fk['foreign_columns'][0],
            $fk['on_update'],
            $fk['on_delete'],
        ],
    ])->sortKeys()->all();
    ksort($esperadas);

    expect($referencias)->toBe($esperadas);
})->with([
    'UsuarioRol' => ['UsuarioRol', [
        'idUsuario' => ['usuarioinstitucional', 'idUsuario', 'cascade', 'restrict'],
        'idRol' => ['rol', 'idRol', 'cascade', 'restrict'],
    ]],
    'AccionSustentabilidad' => ['AccionSustentabilidad', [
        'idEjeTrabajo' => ['ejetrabajo', 'idEjeTrabajo', 'cascade', 'restrict'],
        'idUsuarioSolicitante' => ['usuarioinstitucional', 'idUsuario', 'cascade', 'restrict'],
        'idEstadoAccion' => ['estadoaccion', 'idEstadoAccion', 'cascade', 'restrict'],
    ]],
    'HistorialEstadoAccion' => ['HistorialEstadoAccion', [
        'idAccion' => ['accionsustentabilidad', 'idAccion', 'cascade', 'restrict'],
        'idEstadoAccion' => ['estadoaccion', 'idEstadoAccion', 'cascade', 'restrict'],
        'idUsuarioResponsable' => ['usuarioinstitucional', 'idUsuario', 'cascade', 'set null'],
    ]],
    'Evidencia' => ['Evidencia', [
        'idAccion' => ['accionsustentabilidad', 'idAccion', 'cascade', 'restrict'],
        'idUsuarioCarga' => ['usuarioinstitucional', 'idUsuario', 'cascade', 'restrict'],
    ]],
    'Participacion' => ['Participacion', [
        'idUsuario' => ['usuarioinstitucional', 'idUsuario', 'cascade', 'restrict'],
        'idAccion' => ['accionsustentabilidad', 'idAccion', 'cascade', 'restrict'],
    ]],
]);

it('garantiza unicidad de identificadores y participaciones y permite conservar roles históricos', function () {
    $unicos = [
        'UsuarioInstitucional' => [['identificadorInstitucional'], ['correoInstitucional']],
        'Rol' => [['nombre']],
        'UsuarioRol' => [],
        'EjeTrabajo' => [['nombre']],
        'EstadoAccion' => [['nombre']],
        'AccionSustentabilidad' => [['folio']],
        'HistorialEstadoAccion' => [],
        'Evidencia' => [],
        'Participacion' => [['idUsuario', 'idAccion']],
    ];

    foreach ($unicos as $tabla => $esperados) {
        $indices = collect(Schema::getIndexes($tabla));
        $reales = $indices->where('unique', true)->where('primary', false)->pluck('columns')->all();

        expect($reales)->toEqualCanonicalizing($esperados);
    }

    $filtros = collect(Schema::getIndexes('AccionSustentabilidad'))->firstWhere('name', 'idx_accion_filtros');
    expect($filtros['columns'])->toBe(['idEstadoAccion', 'idEjeTrabajo', 'modalidad', 'formaTrabajo']);
    expect($filtros['unique'])->toBeFalse();

    $roles = collect(Schema::getIndexes('UsuarioRol'))
        ->first(fn (array $indice): bool => $indice['columns'] === ['idUsuario', 'idRol']);
    expect($roles['unique'])->toBeFalse();
});
