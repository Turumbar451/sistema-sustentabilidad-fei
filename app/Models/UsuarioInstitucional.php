<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UsuarioInstitucional extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'usuarios_institucionales';

    protected $primaryKey = 'idUsuario';

    protected $fillable = ['identificadorInstitucional', 'nombreCompleto', 'correoInstitucional', 'telefonoContacto', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->correoInstitucional;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol', 'idUsuario', 'idRol', 'idUsuario', 'idRol')
            ->using(UsuarioRol::class)->withPivot('idUsuarioRol', 'fechaAsignacion');
    }

    public function accionesSolicitadas(): HasMany
    {
        return $this->hasMany(AccionSustentabilidad::class, 'idUsuarioSolicitante', 'idUsuario');
    }

    public function dictamenesEmitidos(): HasMany
    {
        return $this->hasMany(Dictamen::class, 'idUsuarioEmisor', 'idUsuario');
    }

    public function participaciones(): HasMany
    {
        return $this->hasMany(Participacion::class, 'idUsuario', 'idUsuario');
    }

    public static function buscarPorIdentificador(string $identificadorInstitucional): ?self
    {
        return self::query()->where('identificadorInstitucional', $identificadorInstitucional)->first();
    }

    public function determinarPerfil(): string
    {
        $rol = $this->roles()->orderBy('roles.idRol')->first();

        return $rol?->nombre?->value ?? 'Sin perfil asignado';
    }

    public function obtenerRoles(): Collection
    {
        return $this->roles()->orderBy('roles.idRol')->get();
    }
}
