<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RolSistemaEnum;
use Database\Factories\UsuarioInstitucionalFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UsuarioInstitucional extends Authenticatable
{
    /** @use HasFactory<UsuarioInstitucionalFactory> */
    use HasFactory;

    use Notifiable;

    protected $table = 'UsuarioInstitucional';

    protected $primaryKey = 'idUsuario';

    public $timestamps = false;

    protected $rememberTokenName = '';

    protected $fillable = [
        'identificadorInstitucional',
        'nombreCompleto',
        'correoInstitucional',
        'telefonoContacto',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'UsuarioRol',
            'idUsuario',
            'idRol',
            'idUsuario',
            'idRol',
        )->using(UsuarioRol::class)
            ->withPivot(['idUsuarioRol', 'fechaAsignacion', 'fechaFin']);
    }

    public function rolesVigentes(): BelongsToMany
    {
        return $this->roles()->wherePivotNull('fechaFin');
    }

    public function accionesSolicitadas(): HasMany
    {
        return $this->hasMany(
            AccionSustentabilidad::class,
            'idUsuarioSolicitante',
            'idUsuario',
        );
    }

    public function cambiosEstadoResponsable(): HasMany
    {
        return $this->hasMany(
            HistorialEstadoAccion::class,
            'idUsuarioResponsable',
            'idUsuario',
        );
    }

    public function participaciones(): HasMany
    {
        return $this->hasMany(Participacion::class, 'idUsuario', 'idUsuario');
    }

    public function evidenciasCargadas(): HasMany
    {
        return $this->hasMany(Evidencia::class, 'idUsuarioCarga', 'idUsuario');
    }

    public static function buscarPorIdentificador(string $identificador): ?self
    {
        return static::query()
            ->with('rolesVigentes:Rol.idRol,Rol.nombre')
            ->where('identificadorInstitucional', $identificador)
            ->first();
    }

    public function tieneRol(RolSistemaEnum|string $rol): bool
    {
        return $this->tieneAlgunRol([$rol]);
    }

    /** @param array<RolSistemaEnum|string> $roles */
    public function tieneAlgunRol(array $roles): bool
    {
        $roles = array_values(array_filter(array_map(
            static fn (RolSistemaEnum|string $rol): ?RolSistemaEnum => is_string($rol)
                ? RolSistemaEnum::tryFrom($rol)
                : $rol,
            $roles,
        )));

        if ($roles === []) {
            return false;
        }

        if ($this->relationLoaded('rolesVigentes')) {
            return $this->rolesVigentes->contains(
                static fn (Rol $asignado): bool => in_array($asignado->nombre, $roles, true),
            );
        }

        return $this->rolesVigentes()->whereIn(
            'Rol.nombre',
            array_map(static fn (RolSistemaEnum $rol): string => $rol->value, $roles),
        )->exists();
    }

    public function asignarRol(
        RolSistemaEnum $rol,
        ?DateTimeInterface $fechaAsignacion = null,
    ): void {
        $asignacion = UsuarioRol::asignarRolVigente(
            (int) $this->getKey(),
            Rol::idPara($rol),
        );

        if ($asignacion->wasRecentlyCreated && $fechaAsignacion !== null) {
            $asignacion->update(['fechaAsignacion' => $fechaAsignacion]);
        }

        $this->unsetRelation('roles');
        $this->unsetRelation('rolesVigentes');
    }

    public function routeNotificationForMail(): string
    {
        return $this->correoInstitucional;
    }
}
