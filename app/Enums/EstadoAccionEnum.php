<?php

declare(strict_types=1);

namespace App\Enums;

enum EstadoAccionEnum: string
{
    case PendienteDeRevision = 'Pendiente de revision';
    case CorreccionesSolicitadas = 'Correcciones solicitadas';
    case Aprobada = 'Aprobada';
    case Rechazada = 'Rechazada';
    case EnEjecucion = 'En ejecucion';
    case Finalizada = 'Finalizada';

    public function etiqueta(): string
    {
        return match ($this) {
            self::PendienteDeRevision => 'Pendiente de revisión',
            self::CorreccionesSolicitadas => 'Correcciones solicitadas',
            self::Aprobada => 'Aprobada',
            self::Rechazada => 'Rechazada',
            self::EnEjecucion => 'En ejecución',
            self::Finalizada => 'Finalizada',
        };
    }

    public function esVisibleEnCatalogo(): bool
    {
        return $this === self::Aprobada
            || $this === self::EnEjecucion
            || $this === self::Finalizada;
    }

    /** @return list<string> */
    public static function valoresVisiblesEnCatalogo(): array
    {
        return array_values(array_map(
            static fn (self $estado): string => $estado->value,
            array_filter(self::cases(), static fn (self $estado): bool => $estado->esVisibleEnCatalogo()),
        ));
    }
}
