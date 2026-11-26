<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\UsuarioInstitucional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccesoLocalController
{
    private const array IDENTIFICADORES_DEMO = ['P0001', 'P0002', 'M0001', 'S20260001'];

    public function mostrar(): View
    {
        abort_unless(app()->isLocal(), 404);

        $cuentas = UsuarioInstitucional::query()
            ->with('rolesVigentes')
            ->whereIn('identificadorInstitucional', self::IDENTIFICADORES_DEMO)
            ->orderBy('nombreCompleto')
            ->get();

        return view('auth.acceso-local', compact('cuentas'));
    }

    public function iniciar(Request $request): RedirectResponse
    {
        abort_unless(app()->isLocal(), 404);

        $datos = $request->validate([
            'identificadorInstitucional' => ['required', 'string', Rule::in(self::IDENTIFICADORES_DEMO)],
        ]);

        $usuario = UsuarioInstitucional::buscarPorIdentificador($datos['identificadorInstitucional']);

        if ($usuario === null) {
            throw ValidationException::withMessages([
                'identificadorInstitucional' => 'La cuenta de prueba no está disponible. Ejecuta el seeder de desarrollo.',
            ]);
        }

        Auth::guard('web')->login($usuario);
        $request->session()->regenerate();

        return redirect()->route('acciones.consultar')
            ->with('status', 'Sesión de prueba iniciada.');
    }

    public function cerrar(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('acciones.consultar')
            ->with('status', 'Sesión cerrada.');
    }
}
