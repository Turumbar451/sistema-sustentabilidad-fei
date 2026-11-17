<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoAccionEnum;
use App\Enums\FormaTrabajoEnum;
use Database\Factories\AccionSustentabilidadFactory;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AccionSustentabilidad extends Model
{
    /** @use HasFactory<AccionSustentabilidadFactory> */
    use HasFactory;

    protected $table = 'AccionSustentabilidad';

    protected $primaryKey = 'idAccion';

    public $timestamps = false;

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

    protected function fechaInicioFormateada(): Attribute
    {
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

    public function ejeTrabajo(): BelongsTo
    {
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

    public function historialEstados(): HasMany
    {
        return $this->hasMany(HistorialEstadoAccion::class, 'idAccion', 'idAccion')
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
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    #[Scope]
    protected function publicadas(Builder $query): Builder
    {
        $publicos = array_values(array_filter(
            EstadoAccionEnum::cases(),
            static fn (EstadoAccionEnum $estado): bool => $estado->esVisibleEnCatalogo(),
        ));

        return $query->whereHas(
            'estadoAccion',
            static fn (Builder $estado): Builder => $estado
                ->whereIn('nombre', array_column($publicos, 'value')),
        );
    }

    /**
     * @param  Builder<self>  $query
     * @param  array<string, mixed>  $filtros
     * @return Builder<self>
     */
    #[Scope]
    protected function filtrarCatalogo(Builder $query, array $filtros): Builder
    {
        $permitidos = [
            'q',
            'buscar',
            'busqueda',
            'idEjeTrabajo',
            'modalidad',
            'formaTrabajo',
            'estado',
            'orden',
            'idEstadoAccion',
            'fechaRegistroDesde',
            'fechaRegistroHasta',
        ];

        if (array_diff(array_keys($filtros), $permitidos) !== []) {
            throw new InvalidArgumentException(
                'El catálogo recibió un filtro desconocido.',
            );
        }

        $busqueda = $filtros['q'] ?? $filtros['buscar'] ?? $filtros['busqueda'] ?? '';

        if (! is_string($busqueda)) {
            throw new InvalidArgumentException('La búsqueda debe ser una cadena.');
        }

        $busqueda = trim($busqueda);

        if ($busqueda !== '') {
            $query->where(static function (Builder $grupo) use ($busqueda): void {
                $grupo->where('nombre', 'like', '%'.$busqueda.'%')
                    ->orWhere('folio', 'like', '%'.$busqueda.'%');
            });
        }

        if (array_key_exists('idEjeTrabajo', $filtros)) {
            $ejes = is_array($filtros['idEjeTrabajo'])
                ? $filtros['idEjeTrabajo']
                : [$filtros['idEjeTrabajo']];
            $ids = array_map(self::idPositivo(...), $ejes);

            if ($ids !== []) {
                $query->whereIn('idEjeTrabajo', array_values(array_unique($ids)));
            }
        }

        if (array_key_exists('modalidad', $filtros)) {
            $modalidades = is_array($filtros['modalidad'])
                ? $filtros['modalidad']
                : [$filtros['modalidad']];

            foreach ($modalidades as $modalidad) {
                if (! is_string($modalidad) || trim($modalidad) === '') {
                    throw new InvalidArgumentException('Las modalidades deben ser cadenas no vacías.');
                }
            }

            if ($modalidades !== []) {
                $query->whereIn('modalidad', array_values(array_unique(array_map(trim(...), $modalidades))));
            }
        }

        if (array_key_exists('formaTrabajo', $filtros)) {
            $formaTrabajo = $filtros['formaTrabajo'];

            if (is_string($formaTrabajo)) {
                $formaTrabajo = FormaTrabajoEnum::tryFrom($formaTrabajo);
            }

            if (! $formaTrabajo instanceof FormaTrabajoEnum) {
                throw new InvalidArgumentException('La forma de trabajo no pertenece al catálogo.');
            }

            $query->where('formaTrabajo', $formaTrabajo->value);
        }

        if (array_key_exists('estado', $filtros)) {
            $estado = $filtros['estado'];

            if (is_string($estado)) {
                $estado = EstadoAccionEnum::tryFrom($estado);
            }

            if (! $estado instanceof EstadoAccionEnum) {
                throw new InvalidArgumentException(
                    'El estado no pertenece al catálogo.',
                );
            }

            $query->whereHas(
                'estadoAccion',
                static fn (Builder $catalogo): Builder => $catalogo
                    ->where('nombre', $estado->value),
            );
        }

        if (array_key_exists('idEstadoAccion', $filtros)) {
            $query->where(
                'idEstadoAccion',
                self::idPositivo($filtros['idEstadoAccion']),
            );
        }

        $desde = array_key_exists('fechaRegistroDesde', $filtros)
            ? self::fechaFiltro($filtros['fechaRegistroDesde'])
            : null;

        $hasta = array_key_exists('fechaRegistroHasta', $filtros)
            ? self::fechaFiltro($filtros['fechaRegistroHasta'])
            : null;

        if ($desde !== null && $hasta !== null && $desde > $hasta) {
            throw new InvalidArgumentException(
                'La fecha inicial del filtro excede la fecha final.',
            );
        }

        if ($desde !== null) {
            $query->where(
                'fechaRegistro',
                '>=',
                $desde->format('Y-m-d 00:00:00'),
            );
        }

        if ($hasta !== null) {
            $query->where(
                'fechaRegistro',
                '<',
                $hasta->modify('+1 day')->format('Y-m-d 00:00:00'),
            );
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filtros
     * @return Collection<int, self>
     */
    public static function obtenerAccionesPublicadas(array $filtros = []): Collection
    {
        $query = static::query()
            ->select([
                'idAccion',
                'folio',
                'nombre',
                'descripcion',
                'requerimientos',
                'modalidad',
                'formaTrabajo',
                'fechaRegistro',
                'fechaInicio',
                'fechaTermino',
                'idEjeTrabajo',
                'idUsuarioSolicitante',
                'idEstadoAccion',
            ])
            ->publicadas()
            ->filtrarCatalogo($filtros)
            ->with([
                'ejeTrabajo:idEjeTrabajo,nombre',
                'estadoAccion:idEstadoAccion,nombre',
                'solicitante:idUsuario,nombreCompleto',
            ])
            ->withCount('participaciones');

        match ($filtros['orden'] ?? 'recientes') {
            'recientes' => $query->orderByDesc('fechaRegistro')->orderByDesc('idAccion'),
            'proximas' => $query->orderByRaw('fechaInicio IS NULL')->orderBy('fechaInicio')->orderBy('idAccion'),
            'az' => $query->orderBy('nombre')->orderBy('idAccion'),
            default => throw new InvalidArgumentException('El orden del catálogo no es válido.'),
        };

        return $query->get();
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

    private static function idPositivo(mixed $valor): int
    {
        if ((! is_int($valor) && ! is_string($valor))
            || filter_var(
                $valor,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]],
            ) === false) {
            throw new InvalidArgumentException(
                'Los identificadores deben ser enteros positivos.',
            );
        }

        return (int) $valor;
    }

    private static function fechaFiltro(mixed $valor): DateTimeImmutable
    {
        if (! is_string($valor)) {
            throw new InvalidArgumentException(
                'Las fechas del filtro deben usar el formato YYYY-MM-DD.',
            );
        }

        $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);

        if ($fecha === false || $fecha->format('Y-m-d') !== $valor) {
            throw new InvalidArgumentException(
                'Las fechas del filtro deben usar el formato YYYY-MM-DD y ser válidas.',
            );
        }

        return $fecha;
    }
}
