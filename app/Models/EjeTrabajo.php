<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EjeTrabajo extends Model
{
    use HasFactory;

    protected $table = 'ejes_trabajo';

    protected $primaryKey = 'idEjeTrabajo';

    protected $fillable = ['nombre', 'color_tema'];

    public function accionesSustentabilidad(): HasMany
    {
        return $this->hasMany(AccionSustentabilidad::class, 'idEjeTrabajo', 'idEjeTrabajo');
    }

    public static function obtenerTodos(): Collection
    {
        return self::query()->orderBy('nombre')->get();
    }
}
