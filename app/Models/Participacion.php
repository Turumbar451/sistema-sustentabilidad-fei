<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participacion extends Model
{
    use HasFactory;

    protected $table = 'participaciones';

    protected $primaryKey = 'idParticipacion';

    protected $fillable = ['fechaInscripcion', 'idUsuario', 'idAccion'];

    protected function casts(): array
    {
        return ['fechaInscripcion' => 'datetime'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(UsuarioInstitucional::class, 'idUsuario', 'idUsuario');
    }

    public function accion(): BelongsTo
    {
        return $this->belongsTo(AccionSustentabilidad::class, 'idAccion', 'idAccion');
    }

    public static function consultarDisponibilidad(int $idAccion): array
    {
        $accion = AccionSustentabilidad::query()->find($idAccion);

        return ['totalParticipantes' => $accion?->participaciones()->count() ?? 0,
            'disponible' => $accion?->estadoActual->esPublica() ?? false];
    }
}
