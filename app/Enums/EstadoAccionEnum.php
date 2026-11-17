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

    public function esVisibleEnCatalogo(): bool
    {
        return $this === self::Aprobada
            || $this === self::EnEjecucion
            || $this === self::Finalizada;
    }
}
