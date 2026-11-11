<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RolSistemaEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $primaryKey = 'idRol';

    protected $fillable = ['nombre', 'descripcion'];

    protected function casts(): array
    {
        return ['nombre' => RolSistemaEnum::class];
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(UsuarioInstitucional::class, 'usuario_rol', 'idRol', 'idUsuario', 'idRol', 'idUsuario')
            ->using(UsuarioRol::class)->withPivot('idUsuarioRol', 'fechaAsignacion');
    }
}
