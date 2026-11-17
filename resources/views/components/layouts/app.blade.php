@props([
    'title' => 'FEI Sustentabilidad',
    'metaDescription' => 'Consulta las iniciativas de sustentabilidad de la comunidad universitaria de la FEI.',
])

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
<body class="flex min-h-screen flex-col border-t-4 border-uv-green bg-canvas font-sans text-ink antialiased">
    <a href="#contenido-principal"
       class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-uv-blue focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Saltar al contenido principal
    </a>

    <header class="border-b border-line bg-white">
        <div class="mx-auto flex min-h-20 max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}"
               class="flex items-center gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue focus-visible:ring-offset-2"
               aria-label="FEI Sustentabilidad, inicio">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-uv-blue" aria-hidden="true">
                    <span class="h-4 w-4 rounded-full bg-uv-green"></span>
                </span>
                <span class="text-lg font-bold text-uv-blue">FEI <span class="hidden font-medium text-ink sm:inline">Sustentabilidad</span></span>
            </a>

            <nav class="flex items-center gap-5 text-sm" aria-label="Navegación principal">
                <a href="{{ url('/') }}"
                   @if (request()->is('/')) aria-current="page" @endif
                   class="rounded font-semibold text-ink hover:text-uv-blue focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue focus-visible:ring-offset-4">
                    Inicio
                </a>
                <a href="{{ route('acciones.consultar') }}"
                   @if (request()->routeIs('catalogo.*')) aria-current="page" @endif
                   class="rounded font-semibold text-uv-blue hover:text-uv-blue-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue focus-visible:ring-offset-4">
                    Catálogo público
                </a>
            </nav>
        </div>
    </header>

    <main id="contenido-principal" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-line bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-7 text-xs text-muted sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>© {{ date('Y') }} FEI · Sustentabilidad</p>
            <p>Universidad Veracruzana</p>
        </div>
    </footer>
</body>
</html>
