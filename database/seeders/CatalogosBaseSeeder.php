<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\EstadoAccionEnum;
use App\Enums\RolSistemaEnum;
use App\Models\EjeTrabajo;
use App\Models\EstadoAccion;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class CatalogosBaseSeeder extends Seeder
{
    public const array EJES = [
        'Gestion integrada de agua y residuos',
        'Salud',
        'Consumo responsable',
        'Gestion integrada de energia',
        'Administracion y tecnologias sustentables',
    ];

    public function run(): void
    {
        foreach (RolSistemaEnum::cases() as $rol) {
            Rol::asegurar($rol);
        }

        foreach (EstadoAccionEnum::cases() as $estado) {
            EstadoAccion::asegurar($estado);
        }

        foreach (self::EJES as $nombre) {
            EjeTrabajo::asegurar($nombre);
        }
    }
}
