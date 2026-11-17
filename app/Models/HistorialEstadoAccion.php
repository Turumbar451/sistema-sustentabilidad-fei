<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialEstadoAccion extends Model
{
    use HasFactory;

    protected $table = 'HistorialEstadoAccion';

    protected $primaryKey = 'idHistorial';

    public $timestamps = false;

    protected $fillable = [
        'idAccion',
        'idEstadoAccion',
        'idUsuarioResponsable',
        'fechaCambio',
        'motivo',
    ];

    protected function casts(): array
    {
        return ['fechaCambio' => 'immutable_datetime'];
    }

    public function accion(): BelongsTo
    {
        return $this->belongsTo(AccionSustentabilidad::class, 'idAccion', 'idAccion');
    }

    public function estadoAccion(): BelongsTo
    {
        return $this->belongsTo(EstadoAccion::class, 'idEstadoAccion', 'idEstadoAccion');
    }

    public function usuarioResponsable(): BelongsTo
    {
        return $this->belongsTo(
            UsuarioInstitucional::class,
            'idUsuarioResponsable',
            'idUsuario',
        );
    }
}
