<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\EstadoAccionEnum;
use App\Enums\FormaTrabajoEnum;
use App\Models\AccionSustentabilidad;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

/** @extends Builder<AccionSustentabilidad> */
class AccionSustentabilidadBuilder extends Builder
{
    public function delSolicitante(int $idUsuario): static
    {
        return $this->where('idUsuarioSolicitante', self::idPositivo($idUsuario));
    }

    public function publicadas(): static
    {
        return $this->whereHas(
            'estadoAccion',
            static fn (Builder $estado): Builder => $estado
                ->whereIn('nombre', EstadoAccionEnum::valoresVisiblesEnCatalogo()),
        );
    }

    /** @param array<string, mixed> $filtros */
    public function filtrarCatalogo(array $filtros): static
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
            $this->where(static function (Builder $grupo) use ($busqueda): void {
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
                $this->whereIn('idEjeTrabajo', array_values(array_unique($ids)));
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
                $this->whereIn('modalidad', array_values(array_unique(array_map(trim(...), $modalidades))));
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

            $this->where('formaTrabajo', $formaTrabajo->value);
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

            $this->whereHas(
                'estadoAccion',
                static fn (Builder $catalogo): Builder => $catalogo
                    ->where('nombre', $estado->value),
            );
        }

        if (array_key_exists('idEstadoAccion', $filtros)) {
            $this->where(
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
            $this->where(
                'fechaRegistro',
                '>=',
                $desde->format('Y-m-d 00:00:00'),
            );
        }

        if ($hasta !== null) {
            $this->where(
                'fechaRegistro',
                '<',
                $hasta->modify('+1 day')->format('Y-m-d 00:00:00'),
            );
        }

        return $this;
    }

    /** @param array<string, mixed> $filtros */
    public function paraCatalogo(array $filtros = []): static
    {
        $this
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

        return match ($filtros['orden'] ?? 'recientes') {
            'recientes' => $this->orderByDesc('fechaRegistro')->orderByDesc('idAccion'),
            'proximas' => $this->orderByRaw('fechaInicio IS NULL')->orderBy('fechaInicio')->orderBy('idAccion'),
            'az' => $this->orderBy('nombre')->orderBy('idAccion'),
            default => throw new InvalidArgumentException('El orden del catálogo no es válido.'),
        };
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
