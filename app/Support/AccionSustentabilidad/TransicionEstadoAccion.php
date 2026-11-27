<?php

declare(strict_types=1);

namespace App\Support\AccionSustentabilidad;

use App\Enums\EstadoAccionEnum;
use App\Enums\RolSistemaEnum;
use DomainException;

final class TransicionEstadoAccion
{
    /** El origen null representa el registro inicial, sin dictamen previo. */
    public static function validar(
        ?EstadoAccionEnum $origen,
        EstadoAccionEnum $destino,
        RolSistemaEnum $rol,
        ?string $motivo = null,
    ): void {
        $regla = self::regla($origen, $destino);

        if ($regla === null) {
            throw new DomainException('La transición de estado no está permitida.');
        }

        if (! in_array($rol, $regla['roles'], true)) {
            throw new DomainException('El rol no puede ejecutar esta transición de estado.');
        }

        if ($regla['requiereMotivo'] && trim($motivo ?? '') === '') {
            throw new DomainException('El motivo es obligatorio para dictaminar una propuesta.');
        }
    }

    /** @return array{roles: list<RolSistemaEnum>, requiereMotivo: bool}|null */
    private static function regla(?EstadoAccionEnum $origen, EstadoAccionEnum $destino): ?array
    {
        return match ($origen) {
            null => match ($destino) {
                EstadoAccionEnum::Aprobada => [
                    'roles' => [RolSistemaEnum::CoordinadoraSustentabilidad],
                    'requiereMotivo' => false,
                ],
                EstadoAccionEnum::PendienteDeRevision => [
                    'roles' => [RolSistemaEnum::Estudiante, RolSistemaEnum::Maestro],
                    'requiereMotivo' => false,
                ],
                default => null,
            },
            EstadoAccionEnum::PendienteDeRevision => match ($destino) {
                EstadoAccionEnum::Aprobada,
                EstadoAccionEnum::CorreccionesSolicitadas,
                EstadoAccionEnum::Rechazada => [
                    'roles' => [RolSistemaEnum::CoordinadoraSustentabilidad],
                    'requiereMotivo' => true,
                ],
                default => null,
            },
            EstadoAccionEnum::CorreccionesSolicitadas => match ($destino) {
                EstadoAccionEnum::PendienteDeRevision => [
                    'roles' => [RolSistemaEnum::Estudiante, RolSistemaEnum::Maestro],
                    'requiereMotivo' => false,
                ],
                default => null,
            },
            EstadoAccionEnum::Aprobada,
            EstadoAccionEnum::Rechazada,
            EstadoAccionEnum::EnEjecucion,
            EstadoAccionEnum::Finalizada => null,
        };
    }
}
