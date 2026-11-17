<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class EjeTrabajo extends Model
{
    use HasFactory;

    protected $table = 'EjeTrabajo';

    protected $primaryKey = 'idEjeTrabajo';

    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion'];

    public function acciones(): HasMany
    {
        return $this->hasMany(
            AccionSustentabilidad::class,
            'idEjeTrabajo',
            'idEjeTrabajo',
        );
    }

    public static function asegurar(string $nombre): self
    {
        $nombre = trim($nombre);

        if ($nombre === '' || mb_strlen($nombre) > 150) {
            throw new InvalidArgumentException(
                'El nombre del eje debe tener entre 1 y 150 caracteres.',
            );
        }

        return static::query()->firstOrCreate(['nombre' => $nombre]);
    }

    public static function idPara(string $nombre): int
    {
        return (int) static::asegurar($nombre)->getKey();
    }

    /** @return Collection<int, self> */
    public static function obtenerTodos(): Collection
    {
        return static::query()->orderBy('nombre')->get();
    }
}
