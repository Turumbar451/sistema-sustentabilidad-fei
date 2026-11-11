<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipoEvidenciaEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidencia extends Model
{
    use HasFactory;

    protected $table = 'evidencias';

    protected $primaryKey = 'idEvidencia';

    protected $fillable = ['archivo', 'tipoArchivo', 'descripcion', 'idAccion'];

    protected function casts(): array
    {
        return ['tipoArchivo' => TipoEvidenciaEnum::class];
    }

    public function accion(): BelongsTo
    {
        return $this->belongsTo(AccionSustentabilidad::class, 'idAccion', 'idAccion');
    }
}
