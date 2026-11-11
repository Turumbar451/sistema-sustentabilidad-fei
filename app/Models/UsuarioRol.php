<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UsuarioRol extends Pivot
{
    protected $table = 'usuario_rol';

    protected $primaryKey = 'idUsuarioRol';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = ['idUsuario', 'idRol', 'fechaAsignacion'];

    protected function casts(): array
    {
        return ['fechaAsignacion' => 'datetime'];
    }

    public static function obtenerRoles(int $idUsuario): Collection
    {
        return UsuarioInstitucional::query()->findOrFail($idUsuario)->obtenerRoles();
    }
}
