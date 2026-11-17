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

    protected $table = 'Rol';

    protected $primaryKey = 'idRol';

    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion'];

    protected function casts(): array
    {
        return ['nombre' => RolSistemaEnum::class];
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            UsuarioInstitucional::class,
            'UsuarioRol',
            'idRol',
            'idUsuario',
            'idRol',
            'idUsuario',
        )->using(UsuarioRol::class)
            ->withPivot(['idUsuarioRol', 'fechaAsignacion', 'fechaFin']);
    }

    public static function asegurar(RolSistemaEnum $rol): self
    {
        return static::query()->firstOrCreate(['nombre' => $rol->value]);
    }

    public static function idPara(RolSistemaEnum $rol): int
    {
        return (int) static::asegurar($rol)->getKey();
    }
}
