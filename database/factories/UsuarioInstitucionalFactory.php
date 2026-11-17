<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RolSistemaEnum;
use App\Models\UsuarioInstitucional;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UsuarioInstitucional> */
class UsuarioInstitucionalFactory extends Factory
{
    protected $model = UsuarioInstitucional::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'identificadorInstitucional' => fake()->unique()->bothify('UV########'),
            'nombreCompleto' => fake()->name(),
            'correoInstitucional' => fake()->unique()
                ->numerify('usuario.########@fei.example.test'),
            'telefonoContacto' => fake()->optional()->numerify('228#######'),
        ];
    }

    public function conRol(RolSistemaEnum $rol): static
    {
        return $this->afterCreating(
            static function (UsuarioInstitucional $usuario) use ($rol): void {
                $usuario->asignarRol($rol);
            },
        );
    }

    public function estudiante(): static
    {
        return $this->conRol(RolSistemaEnum::Estudiante);
    }

    public function maestro(): static
    {
        return $this->conRol(RolSistemaEnum::Maestro);
    }

    public function coordinadora(): static
    {
        return $this->conRol(RolSistemaEnum::CoordinadoraSustentabilidad);
    }

    public function director(): static
    {
        return $this->conRol(RolSistemaEnum::Director);
    }
}
