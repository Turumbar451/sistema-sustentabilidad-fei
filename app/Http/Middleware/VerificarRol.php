<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\UsuarioInstitucional;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if ($usuario === null) {
            if (app()->isLocal() && $request->isMethod('GET')) {
                return redirect()->route('acceso-local.mostrar');
            }

            abort(403);
        }

        abort_unless($usuario instanceof UsuarioInstitucional && $usuario->tieneAlgunRol($roles), 403);

        return $next($request);
    }
}
