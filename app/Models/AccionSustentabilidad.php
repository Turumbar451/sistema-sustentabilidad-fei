<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoAccionEnum;
use App\Enums\ModalidadAccionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccionSustentabilidad extends Model
{
    //necesario para usar los factories de laravel
    use HasFactory;

    protected $table = 'acciones_sustentabilidad';

    protected $primaryKey = 'idAccion';

    //lista de campos que se pueden asignar masivamente.
    //evitar que se asignen campos que no deben ser modificados por el usuario.
    //create, update, fill,
    protected $fillable = ['folio', 'nombre', 'descripcion', 'modalidad', 'fechaRegistro', 'fechaInicio', 'fechaTermino', 'requerimientos', 'estadoActual', 'idEjeTrabajo', 'idUsuarioSolicitante'];

    //casteo de atributos
    //laravel con date indica que es un obj de tipo CArbon
    protected function casts(): array
    {
        return ['modalidad' => ModalidadAccionEnum::class, 
        'estadoActual' => EstadoAccionEnum::class,
            'fechaRegistro' => 'datetime', 
            'fechaInicio' => 'date', 
            'fechaTermino' => 'date'];
    }

    //BelongsTo indica relacion entre tablas, en este caso AccionSustentabilidad pertenece a EjeTrabajo, es decir, cada accion tiene un eje de trabajo.
    public function ejeTrabajo(): BelongsTo
    {
        //este objeto -> (
        /*  
        modelo relacionado
        llave foranea en esta tabla
        llalve primara en la tabla rlacionada
        */
        return $this->belongsTo(EjeTrabajo::class, 'idEjeTrabajo', 'idEjeTrabajo');
    }

    //nombre de la conexion, es decir, el nombre de la funcion que devuelve la relacion
    public function solicitante(): BelongsTo
    {
        //accionSustentabilidad le pertenece a usuraioInstitucional
        return $this->belongsTo(UsuarioInstitucional::class, 'idUsuarioSolicitante', 'idUsuario');
        //:: es un operador de resolucion de ambito, sirve para acceder a metodos estaticos.
        //devuelve el nombre de la clase junto al namespace App\Models\UsuarioInstitucional
    }

    //uno a muchos, es decir, una accion puede tener muchos dictamenes
    public function dictamenes(): HasMany
    {
        return $this->hasMany(Dictamen::class, 'idAccion', 'idAccion');
    }

    public function participaciones(): HasMany
    {
        return $this->hasMany(Participacion::class, 'idAccion', 'idAccion');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class, 'idAccion', 'idAccion');
    }

    public static function crearBorrador(array $datos, int $idUsuario): self
    {
        return DB::transaction(function () use ($datos, $idUsuario): self {
            $year = now()->year;
            $last = self::query()->where('folio', 'like', "FEI-{$year}-%")->lockForUpdate()->orderByDesc('idAccion')->value('folio');
            $next = $last ? ((int) Str::afterLast($last, '-')) + 1 : 1;

            return self::query()->create([...$datos, 'folio' => sprintf('FEI-%d-%03d', $year, $next),
                'fechaRegistro' => now(), 'estadoActual' => EstadoAccionEnum::Propuesta, 'idUsuarioSolicitante' => $idUsuario]);
        });
    }

    public static function obtenerPropuestasPendientes(): Collection
    {
        return self::query()->whereIn('estadoActual', [EstadoAccionEnum::Propuesta->value, EstadoAccionEnum::PendientePorCorregir->value])
            ->with(['ejeTrabajo', 'solicitante'])->orderByDesc('fechaRegistro')->get();
    }

    public static function buscarPorId(int $idAccion): ?self
    {
        return self::query()->find($idAccion);
    }

    public static function actualizarEstado(int $idAccion, EstadoAccionEnum|string $estado): bool
    {
        $accion = self::query()->find($idAccion);
        if (! $accion) {
            return false;
        }
        $accion->estadoActual = is_string($estado) ? EstadoAccionEnum::from($estado) : $estado;

        return $accion->save();
    }
    //un collection es un obj mas facil de manejar que un arreglo
    public static function obtenerAccionesPublicadas(array $filtros = []): Collection
    {
        $q = self::query()
            ->whereIn('estadoActual', [
                EstadoAccionEnum::Aprobada->value,
                EstadoAccionEnum::Realizada->value
            ])
            ->with(['ejeTrabajo', 'solicitante']);
            //with carga relaciones, o sea, carga los datos de la tabla relacionada.
        $term = trim((string) ($filtros['q'] ?? ''));

        if ($term !== '') {
            $q->where(
                fn (Builder $b) => $b
                    ->where('nombre', 'like', "%{$term}%")
                    ->orWhere('folio', 'like', "%{$term}%")
            );
        }

        if (! empty($filtros['idEjeTrabajo'])) {
            $q->whereIn('idEjeTrabajo', (array) $filtros['idEjeTrabajo']);
        }

        if (! empty($filtros['modalidad'])) {
            $values = array_map(
                fn ($v) => $v instanceof ModalidadAccionEnum ? $v->value : (string) $v, 
                (array) $filtros['modalidad']
            );

            $q->whereIn('modalidad', $values);
        }

        return $q->orderByDesc('fechaRegistro')->get();
    }

    public static function obtenerDetallePublico(int $idAccion): ?self
    {
        return self::query()->whereKey($idAccion)->whereIn('estadoActual', [EstadoAccionEnum::Aprobada->value, EstadoAccionEnum::Realizada->value])
            ->with(['ejeTrabajo', 'solicitante'])->withCount('participaciones')->first();
    }

    public static function obtenerAccionPublicadaPorId(int $id): ?self
    {
        return self::query()
            ->with('ejeTrabajo')
            ->where('idAccion', $id)
            ->whereIn('estadoActual', ['Aprobada', 'Realizada'])
            ->first();
    }
}
