<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ParticipacionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participacion extends Model
{
    /** @use HasFactory<ParticipacionFactory> */
    use HasFactory;

    protected $table = 'Participacion';

    protected $primaryKey = 'idParticipacion';

    public $timestamps = false;

    protected $fillable = ['fechaInscripcion', 'idAccion', 'idUsuario'];

    protected function casts(): array
    {
        return ['fechaInscripcion' => 'immutable_datetime'];
    }

    public function accion(): BelongsTo
    {
        return $this->belongsTo(AccionSustentabilidad::class, 'idAccion', 'idAccion');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(UsuarioInstitucional::class, 'idUsuario', 'idUsuario');
    }
}
