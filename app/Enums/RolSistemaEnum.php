<?php

declare(strict_types=1);

namespace App\Enums;

enum RolSistemaEnum: string
{
    case Estudiante = 'Estudiante';
    case Maestro = 'Maestro';
    case CoordinadoraSustentabilidad = 'Coordinadora de Sustentabilidad';
    case Director = 'Director';
}
