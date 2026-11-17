<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;

class UsuarioRol extends Pivot
{
    use HasFactory;

    protected $table = 'UsuarioRol';

    protected $primaryKey = 'idUsuarioRol';

    protected $fillable = ['idUsuario', 'idRol', 'fechaAsignacion', 'fechaFin'];

    public $incrementing = true;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'fechaAsignacion' => 'immutable_datetime',
            'fechaFin' => 'immutable_datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(UsuarioInstitucional::class, 'idUsuario', 'idUsuario');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'idRol', 'idRol');
    }

    public static function asignarRolVigente(int $idUsuario, int $idRol): self
    {
        return DB::transaction(function () use ($idUsuario, $idRol): self {
            UsuarioInstitucional::query()->lockForUpdate()->findOrFail($idUsuario);
            Rol::query()->findOrFail($idRol);

            $vigente = static::query()
                ->where('idUsuario', $idUsuario)
                ->where('idRol', $idRol)
                ->whereNull('fechaFin')
                ->lockForUpdate()
                ->first();

            return $vigente ?? static::query()->create([
                'idUsuario' => $idUsuario,
                'idRol' => $idRol,
                'fechaAsignacion' => now(),
                'fechaFin' => null,
            ]);
        });
    }
}
