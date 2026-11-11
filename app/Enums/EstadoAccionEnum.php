<?php

declare(strict_types=1);

namespace App\Enums;

enum EstadoAccionEnum: string
{
    case Propuesta = 'Propuesta';
    case PendientePorCorregir = 'Pendiente por corregir';
    case Aprobada = 'Aprobada';
    case NoAutorizada = 'No autorizada';
    case Realizada = 'Realizada';
    case Cancelada = 'Cancelada';

    public function esPublica(): bool
    {
        return in_array($this, [self::Aprobada, self::Realizada], true);
    }
}
