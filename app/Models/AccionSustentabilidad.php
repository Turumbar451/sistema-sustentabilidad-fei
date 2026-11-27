<?php

declare(strict_types=1);

namespace App\Models;

use App\Builders\AccionSustentabilidadBuilder;
use App\Enums\FormaTrabajoEnum;
use Database\Factories\AccionSustentabilidadFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AccionSustentabilidad extends Model
{
    /* comentarion PHPDoc que utilizan los editores para autocompletado y sugerencias */
    /** @use HasBuilder<AccionSustentabilidadBuilder> */
    use HasBuilder;

    /** @use HasFactory<AccionSustentabilidadFactory> */
    use HasFactory;

    // propiedades definidas por eloquent, no son palabras reservadas.
    protected static string $builder = AccionSustentabilidadBuilder::class;

    protected $table = 'AccionSustentabilidad';

    protected $primaryKey = 'idAccion';

    // si dejamos en true, entonces Eloquent espera que existan las columnas created_at y updated_at
    public $timestamps = false;

    // columnas que se pueden asignar masivamente (create, fill, update)
    protected $fillable = [
        'folio',
        'nombre',
        'descripcion',
        'modalidad',
        'formaTrabajo',
        'fechaRegistro',
        'fechaInicio',
        'fechaTermino',
        'requerimientos',
        'idEjeTrabajo',
        'idUsuarioSolicitante',
        'idEstadoAccion',
    ];

    protected function casts(): array
    {
        return [
            'formaTrabajo' => FormaTrabajoEnum::class,
            'fechaRegistro' => 'immutable_datetime',
            'fechaInicio' => 'immutable_date',
            'fechaTermino' => 'immutable_date',
        ];
    }

    // accessors es una forma de definir propiedades calculdas que no existen en la base de datos
    // fn() define la funcion getter con la logica del calculo
    // attribute crea el objeto que guarda la funcion
    // fechaInicioFormateada asocia la propiedad calculada con el nombre de la funcion
    protected function fechaInicioFormateada(): Attribute
    {
        // que hacer cunando alguien pida este dato
        return Attribute::get(fn (): ?string => $this->fechaInicio?->format('d/m/Y'));
    }

    protected function fechaTerminoFormateada(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->fechaTermino?->format('d/m/Y'));
    }

    protected function fechaRegistroFormateada(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->fechaRegistro?->format('d/m/Y'));
    }

    // belongsTo indica que este modelo (AccionSustentabilidad)
    // tiene una llave foranea(muchos) que apunta a un registro en EjeTrabajo.
    public function ejeTrabajo(): BelongsTo
    {
        // 1. Modelo relacionado
        // 2. Columna foranea en la tabla actual (AccionSustentabilidad)
        // 3. Columna referenciada en la tabla relacionada (EjeTrabajo)
        return $this->belongsTo(EjeTrabajo::class, 'idEjeTrabajo', 'idEjeTrabajo');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(
            UsuarioInstitucional::class,
            'idUsuarioSolicitante',
            'idUsuario',
        );
    }

    public function estadoAccion(): BelongsTo
    {
        return $this->belongsTo(
            EstadoAccion::class,
            'idEstadoAccion',
            'idEstadoAccion',
        );
    }

    // hasMany indica que este modelo apunta a muchos registros
    public function historialEstados(): HasMany
    {
        return $this->hasMany(HistorialEstadoAccion::class, 'idAccion', 'idAccion')
        // ordena por fecha, y si no, por historial.
            ->orderBy('fechaCambio')
            ->orderBy('idHistorial');
    }

    public function participaciones(): HasMany
    {
        return $this->hasMany(Participacion::class, 'idAccion', 'idAccion');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class, 'idAccion', 'idAccion');
    }

    /**
     * @param  array<string, mixed>  $filtros
     * @return Collection<int, self>
     */
    public static function obtenerAccionesPublicadas(array $filtros = []): Collection
    {
        return static::query()->paraCatalogo($filtros)->get();
    }

    /**
     * @param  array<string, mixed>  $filtros
     * @return LengthAwarePaginator<int, self>
     */
    public static function paginarAccionesPublicadas(
        array $filtros = [],
        int $porPagina = 9,
        int $pagina = 1,
    ): LengthAwarePaginator {
        return static::query()->paraCatalogo($filtros)->paginate($porPagina, ['*'], 'page', $pagina);
    }

    /** @return LengthAwarePaginator<int, self> */
    public static function paginarPropuestasDelSolicitante(
        int $idUsuario,
        int $porPagina = 10,
        int $pagina = 1,
    ): LengthAwarePaginator {
        return static::query()
            ->delSolicitante($idUsuario)
            ->select([
                'idAccion',
                'folio',
                'nombre',
                'fechaRegistro',
                'idEjeTrabajo',
                'idEstadoAccion',
                'idUsuarioSolicitante',
            ])
            ->with([
                'ejeTrabajo:idEjeTrabajo,nombre',
                'estadoAccion:idEstadoAccion,nombre',
            ])
            ->orderByDesc('fechaRegistro')
            ->orderByDesc('idAccion')
            ->paginate($porPagina, ['*'], 'page', $pagina);
    }

    public static function buscarPorId(int $idAccion): ?self
    {
        return static::query()->find($idAccion);
    }

    public function esVisibleEnCatalogo(): bool
    {
        $this->loadMissing('estadoAccion:idEstadoAccion,nombre');

        return $this->estadoAccion->nombre->esVisibleEnCatalogo();
    }

    public function cargarRelacionesConsulta(): self
    {
        $this->loadMissing([
            'ejeTrabajo:idEjeTrabajo,nombre',
            'estadoAccion:idEstadoAccion,nombre',
            'solicitante:idUsuario,nombreCompleto',
        ])->loadCount('participaciones');

        return $this;
    }

    public function cargarRelacionesDetallePropia(): self
    {
        $this->loadMissing([
            'ejeTrabajo:idEjeTrabajo,nombre',
            'estadoAccion:idEstadoAccion,nombre',
            'solicitante:idUsuario,nombreCompleto',
            'evidencias',
            'historialEstados.estadoAccion:idEstadoAccion,nombre',
            'historialEstados.usuarioResponsable:idUsuario,nombreCompleto',
        ]);

        return $this;
    }

    public function registrarCambioEstado(
        int $idNuevoEstado,
        ?int $idUsuarioResponsable,
        ?string $motivo = null,
    ): void {
        DB::transaction(function () use ($idNuevoEstado, $idUsuarioResponsable, $motivo): void {
            $accion = static::query()->lockForUpdate()->findOrFail($this->getKey());
            EstadoAccion::query()->findOrFail($idNuevoEstado);

            $accion->historialEstados()->create([
                'idEstadoAccion' => $idNuevoEstado,
                'idUsuarioResponsable' => $idUsuarioResponsable,
                'fechaCambio' => now(),
                'motivo' => $motivo,
            ]);

            $accion->update(['idEstadoAccion' => $idNuevoEstado]);
        });

        $this->idEstadoAccion = $idNuevoEstado;
        $this->syncOriginalAttribute('idEstadoAccion');
        $this->unsetRelation('estadoAccion');
        $this->unsetRelation('historialEstados');
    }
}
