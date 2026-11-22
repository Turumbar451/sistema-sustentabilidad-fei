<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RolSistemaEnum;
use App\Models\Rol;
use App\Models\UsuarioInstitucional;
use Closure;
use Illuminate\Auth\SessionGuard;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ForzarCoordinadoraLocal
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->isLocal()) {
            return $next($request);
        }

        // firstOrNew permite navegar sin crear ni actualizar usuarios o roles.
        $coordinadora = UsuarioInstitucional::query()->firstOrNew([
            'identificadorInstitucional' => 'P0001',
        ], [
            'nombreCompleto' => 'Ana Luz Polo Estrella',
            'correoInstitucional' => 'anpolo@uv.mx',
        ]);
        $coordinadora->setRelation('rolesVigentes', new Collection([
            new Rol(['nombre' => RolSistemaEnum::CoordinadoraSustentabilidad]),
        ]));

        
        $guard = Auth::guard('web');
        /** @var SessionGuard $guard */
        $guard->setUser($coordinadora);

        try {
            if (! $coordinadora->exists && ! $request->isMethodSafe()) {
                throw ValidationException::withMessages([
                    'usuario' => 'Para guardar una acción, Ana Luz (P0001) debe existir en UsuarioInstitucional. El acceso temporal no crea usuarios.',
                ]);
            }

            return $next($request);
        } finally {
            // La identidad solo dura esta petición; no se inicia una sesión persistente.
            $guard->forgetUser();
        }
    }
}
