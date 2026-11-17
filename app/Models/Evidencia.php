<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EvidenciaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidencia extends Model
{
    /** @use HasFactory<EvidenciaFactory> */
    use HasFactory;

    protected $table = 'Evidencia';

    protected $primaryKey = 'idEvidencia';

    public $timestamps = false;

    protected $fillable = [
        'nombreArchivo',
        'ubicacionArchivo',
        'tipoArchivo',
        'descripcion',
        'fechaCarga',
        'idAccion',
        'idUsuarioCarga',
    ];

    protected function casts(): array
    {
        return [
            'fechaCarga' => 'immutable_datetime',
        ];
    }

    public function accion(): BelongsTo
    {
        return $this->belongsTo(AccionSustentabilidad::class, 'idAccion', 'idAccion');
    }

    public function usuarioCarga(): BelongsTo
    {
        return $this->belongsTo(UsuarioInstitucional::class, 'idUsuarioCarga', 'idUsuario');
    }
}
