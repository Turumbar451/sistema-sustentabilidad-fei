<?php

declare(strict_types=1);

namespace App\Enums;

enum EstadoDictamenEnum: string
{
    case Aprobada = 'Aprobada';
    case PendientePorCorregir = 'Pendiente por corregir';
    case NoAutorizada = 'No autorizada';
}
