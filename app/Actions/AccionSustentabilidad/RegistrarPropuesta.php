<?php

declare(strict_types=1);

namespace App\Actions\AccionSustentabilidad;

use App\Enums\EstadoAccionEnum;
use App\Enums\RolSistemaEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EstadoAccion;
use App\Models\UsuarioInstitucional;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class RegistrarPropuesta
{
    /**
     * @param  array{nombre: string, descripcion: string, idEjeTrabajo: int|string, modalidad: string, formaTrabajo: string, fechaInicio: string, fechaTermino: string, requerimientos?: ?string, idUsuarioSolicitante?: int}  $datosValidados
     * @param  list<UploadedFile>|null  $archivos
     */
    public function ejecutar(
        array $datosValidados,
        ?array $archivos = null,
        ?UsuarioInstitucional $usuarioSolicitante = null,
    ): AccionSustentabilidad {
        $idSolicitante = self::idPositivo($usuarioSolicitante?->getKey() ?? $datosValidados['idUsuarioSolicitante'] ?? null);
        $fechaRegistro = now();
        $disco = Storage::disk('public');
        $rutasGuardadas = [];

        try {
            return DB::transaction(function () use ($datosValidados, $archivos, $idSolicitante, $usuarioSolicitante, $fechaRegistro, &$rutasGuardadas): AccionSustentabilidad {
                $solicitante = $usuarioSolicitante ?? UsuarioInstitucional::query()->findOrFail($idSolicitante);
                $esCoordinadora = $solicitante->tieneRol(RolSistemaEnum::CoordinadoraSustentabilidad);
                $estadoInicial = EstadoAccion::asegurar(
                    $esCoordinadora ? EstadoAccionEnum::Aprobada : EstadoAccionEnum::PendienteDeRevision,
                );

                $accion = AccionSustentabilidad::query()->create([
                    'folio' => 'FEI-'.$fechaRegistro->year.'-'.Str::ulid(),
                    'nombre' => $datosValidados['nombre'],
                    'descripcion' => $datosValidados['descripcion'],
                    'idEjeTrabajo' => $datosValidados['idEjeTrabajo'],
                    'modalidad' => $datosValidados['modalidad'],
                    'formaTrabajo' => $datosValidados['formaTrabajo'],
                    'fechaInicio' => $datosValidados['fechaInicio'],
                    'fechaTermino' => $datosValidados['fechaTermino'],
                    'requerimientos' => $datosValidados['requerimientos'] ?? null,
                    'fechaRegistro' => $fechaRegistro,
                    'idUsuarioSolicitante' => $idSolicitante,
                    'idEstadoAccion' => $estadoInicial->getKey(),
                ]);

                $accion->historialEstados()->create([
                    'idEstadoAccion' => $estadoInicial->getKey(),
                    'idUsuarioResponsable' => $idSolicitante,
                    'fechaCambio' => $fechaRegistro,
                    'motivo' => $esCoordinadora
                        ? 'Registro y aprobación directa de la Coordinadora de Sustentabilidad.'
                        : 'Registro inicial de la propuesta.',
                ]);

                foreach ($archivos ?? [] as $archivo) {
                    $ruta = $archivo->store('evidencias/'.$accion->getKey(), 'public');

                    if ($ruta === false) {
                        throw new RuntimeException('No fue posible guardar una de las evidencias.');
                    }

                    $rutasGuardadas[] = $ruta;

                    $accion->evidencias()->create([
                        'nombreArchivo' => mb_substr($archivo->getClientOriginalName(), 0, 255),
                        'ubicacionArchivo' => $ruta,
                        'tipoArchivo' => $archivo->getMimeType(),
                        'fechaCarga' => $fechaRegistro,
                        'idUsuarioCarga' => $idSolicitante,
                    ]);
                }

                return $accion->setRelation('estadoAccion', $estadoInicial);
            });
        } catch (Throwable $exception) {
            if ($rutasGuardadas !== []) {
                $disco->delete($rutasGuardadas);
            }

            throw $exception;
        }
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
}
