<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoDictamenEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dictamen extends Model
{
    use HasFactory;

    protected $table = 'dictamenes';

    protected $primaryKey = 'idDictamen';

    protected $fillable = ['estadoDictamen', 'comentario', 'fechaDictamen', 'idAccion', 'idUsuarioEmisor'];

    protected function casts(): array
    {
        return ['estadoDictamen' => EstadoDictamenEnum::class, 'fechaDictamen' => 'datetime'];
    }

    public function accion(): BelongsTo
    {
        return $this->belongsTo(AccionSustentabilidad::class, 'idAccion', 'idAccion');
    }

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(UsuarioInstitucional::class, 'idUsuarioEmisor', 'idUsuario');
    }

    public static function crearDictamen(int $idAccion, EstadoDictamenEnum|string $estado, string $comentario, int $idCoordinadora): self
    {
        $value = is_string($estado) ? EstadoDictamenEnum::from($estado) : $estado;

        return self::query()->create(['idAccion' => $idAccion, 'estadoDictamen' => $value, 'comentario' => $comentario,
            'fechaDictamen' => now(), 'idUsuarioEmisor' => $idCoordinadora]);
    }
}
