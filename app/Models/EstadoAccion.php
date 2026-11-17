<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoAccionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoAccion extends Model
{
    use HasFactory;

    protected $table = 'EstadoAccion';

    protected $primaryKey = 'idEstadoAccion';

    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion'];

    protected function casts(): array
    {
        return ['nombre' => EstadoAccionEnum::class];
    }

    public function acciones(): HasMany
    {
        return $this->hasMany(
            AccionSustentabilidad::class,
            'idEstadoAccion',
            'idEstadoAccion',
        );
    }

    public function historiales(): HasMany
    {
        return $this->hasMany(
            HistorialEstadoAccion::class,
            'idEstadoAccion',
            'idEstadoAccion',
        );
    }

    public static function asegurar(EstadoAccionEnum $estado): self
    {
        return static::query()->firstOrCreate(['nombre' => $estado->value]);
    }

    public static function idPara(EstadoAccionEnum $estado): int
    {
        return (int) static::asegurar($estado)->getKey();
    }
}
