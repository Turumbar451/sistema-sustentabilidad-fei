<?php

declare(strict_types=1);

namespace App\Enums;

enum ModalidadAccionEnum: string
{
    case Presencial = 'Presencial';
    case Virtual = 'Virtual';
    case Hibrida = 'Híbrida';
}
