<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RolSistemaEnum;
use App\Models\Rol;
use App\Models\UsuarioInstitucional;
use App\Models\UsuarioRol;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UsuarioRol> */
class UsuarioRolFactory extends Factory
{
    protected $model = UsuarioRol::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'idUsuario' => UsuarioInstitucional::factory(),
            'idRol' => static fn (): int => Rol::idPara(
                fake()->randomElement(RolSistemaEnum::cases()),
            ),
            'fechaAsignacion' => CarbonImmutable::now(),
            'fechaFin' => null,
        ];
    }

    public function historico(): static
    {
        return $this->state(fn (): array => [
            'fechaAsignacion' => CarbonImmutable::now()->subMonth(),
            'fechaFin' => CarbonImmutable::now()->subDay(),
        ]);
    }
}
