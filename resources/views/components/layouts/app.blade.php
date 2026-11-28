@php
    $enlaces = [
        [
            'nombre' => 'Mis propuestas',
            'url' => route('acciones.mis-propuestas'),
            'visible' => $puedeRegistrarPropuestas,
            'activo' => request()->routeIs('acciones.mis-propuestas', 'acciones.mis-propuestas.detalle'),
            'icono' => 'M3 12h4v8H3v-8Zm7-8h4v16h-4V4Zm7 5h4v11h-4V9Z',
        ],
        [
            'nombre' => 'Registrar propuesta',
            'url' => route('acciones.formulario-registro'),
            'visible' => $puedeRegistrarPropuestas,
            'activo' => request()->routeIs('acciones.formulario-registro', 'acciones.registrar'),
            'icono' => 'M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9m-6-6 6 6m-6-6v6h6M8 14h8m-4-4v8',
        ],
        [
            'nombre' => 'Evaluación de propuestas',
            'url' => route('evaluacion.propuestas.index'),
            'visible' => $puedeEvaluarPropuestas,
            'activo' => request()->routeIs('evaluacion.propuestas.*'),
            'icono' => 'M9 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3M9 3h6v4H9V3Zm-1 11 3 3 5-6',
        ],
        [
            'nombre' => 'Catálogo público',
            'url' => route('acciones.consultar'),
            'visible' => true,
            'activo' => request()->routeIs('acciones.consultar', 'acciones.detalle'),
            'icono' => 'M3 3h7v7H3V3Zm11 0h7v7h-7V3ZM3 14h7v7H3v-7Zm11 0h7v7h-7v-7Z',
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $metaDescription }}">
    <title>{{ $title }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas font-sans text-ink antialiased">
    <a href="#contenido-principal"
       class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-uv-blue focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Saltar al contenido principal
    </a>

    <aside class="border-t-4 border-b border-t-uv-green border-b-line bg-white lg:fixed lg:inset-y-0 lg:left-0 lg:flex lg:w-64 lg:flex-col lg:border-r lg:border-r-line lg:border-b-0"
           aria-label="Panel de navegación">
        <a href="{{ route('acciones.consultar') }}"
           class="flex min-h-20 items-center gap-3 px-5 py-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-uv-blue"
           aria-label="FEI Sustentabilidad, inicio">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-uv-blue" aria-hidden="true">
                <span class="h-4 w-4 rounded-full bg-uv-green"></span>
            </span>
            <span class="text-lg font-bold text-uv-blue">FEI <span class="text-sm font-medium text-ink">Sustentabilidad</span></span>
        </a>

        <nav class="px-3 pb-5 lg:flex-1 lg:overflow-y-auto" aria-label="Menú principal">
            <p class="px-3 py-3 text-xs font-semibold tracking-wider text-muted uppercase">Espacio de trabajo</p>
            <ul class="grid gap-1 sm:grid-cols-2 lg:grid-cols-1">
                @foreach ($enlaces as $enlace)
                    @continue(! $enlace['visible'])
                    <li>
                        @if ($enlace['url'] !== null)
                            <a href="{{ $enlace['url'] }}"
                               @if ($enlace['activo']) aria-current="page" @endif
                               @class([
                                   'flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-uv-blue',
                                   'bg-uv-green-soft font-semibold text-uv-green-dark' => $enlace['activo'],
                                   'font-medium text-muted hover:bg-canvas hover:text-uv-blue' => ! $enlace['activo'],
                               ])>
                        @else
                            <span aria-disabled="true" class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-muted opacity-60">
                        @endif
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <path d="{{ $enlace['icono'] }}" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>{{ $enlace['nombre'] }}</span>
                        @if ($enlace['url'] !== null)
                            </a>
                        @else
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="hidden border-t border-line px-6 py-5 text-xs text-muted lg:block">
            Universidad Veracruzana<br>
            Facultad de Estadística e Informática
        </div>
    </aside>

    <div class="flex min-h-screen flex-col lg:pl-64">
        <header class="border-b border-line bg-white">
            <div class="flex min-h-20 flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
                <p class="text-sm font-semibold text-uv-blue">Gestión de iniciativas de sustentabilidad</p>
                @auth('web')
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-uv-green-soft text-uv-green-dark" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM4 21v-2a8 8 0 0 1 16 0v2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <p class="font-medium">{{ $usuarioAutenticado->nombreCompleto }}</p>
                            @foreach ($usuarioAutenticado->rolesVigentes as $rol)
                                <p class="text-xs text-muted">{{ $rol->nombre->value }}</p>
                            @endforeach
                        </div>
                        <form method="POST" action="{{ route('sesion.cerrar') }}">
                            @csrf
                            <button type="submit" class="rounded-lg border border-line px-3 py-2 font-semibold text-uv-blue hover:bg-canvas focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-uv-blue">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                @else
                    @if (app()->isLocal())
                        <a href="{{ route('acceso-local.mostrar') }}"
                           class="rounded-lg border border-line px-3 py-2 text-sm font-semibold text-uv-blue hover:bg-canvas focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-uv-blue">
                            Acceso local
                        </a>
                    @endif
                @endauth
            </div>
        </header>

        <main id="contenido-principal" class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            @if (session('status'))
                <div role="status" class="mx-auto mb-6 max-w-7xl rounded-lg border border-uv-green bg-uv-green-soft px-4 py-3 text-sm text-uv-green-dark">
                    {{ session('status') }}
                </div>
            @endif
            {{ $slot }}
        </main>

        <footer class="border-t border-line bg-white">
            <div class="flex flex-col gap-3 px-4 py-6 text-xs text-muted sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>© {{ date('Y') }} FEI · Sustentabilidad</p>
                <p>Universidad Veracruzana</p>
            </div>
        </footer>
    </div>
</body>
</html>
