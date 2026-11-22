{{--
    resources/views/components/layouts/app.blade.php
    Layout principal · FEI Sustentabilidad
    Laravel 13 · Blade · Tailwind CSS v4

    Uso:
        <x-layouts.app title="Registro de propuesta">
            ... contenido ...
        </x-layouts.app>

    Paleta institucional (Universidad Veracruzana):
      Verde #28AD56  → acciones, acentos, estado activo
      Azul  #18529D  → información secundaria / institucional
--}}
@props(['title' => 'Panel'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} · FEI Sustentabilidad</title>

    {{-- Tailwind v4 (resources/css/app.css → @import "tailwindcss";) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 font-sans text-gray-900 antialiased">

<div class="min-h-full lg:pl-64">

    {{-- Control del menú lateral en móvil (sin JavaScript) --}}
    <input type="checkbox" id="menu-lateral" class="peer sr-only">

    {{-- ============================ SIDEBAR ============================ --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] -translate-x-full flex-col border-r border-gray-200 bg-white transition-transform duration-300 ease-out peer-checked:translate-x-0 lg:w-64 lg:translate-x-0"
           aria-label="Navegación principal">

        {{-- Logo --}}
        <div class="flex h-16 shrink-0 items-center gap-3 px-5 lg:h-20 lg:px-6">
            <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#28AD56] focus-visible:ring-offset-2">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-[10px] bg-uv-green" aria-hidden="true">
                    <span class="h-4 w-4 rounded-full bg-[#DFF5E3]"></span>
                </span>
                <span class="flex min-w-0 items-baseline gap-2">
                    <span class="text-[17px] font-bold tracking-tight text-gray-900">FEI</span>
                    <span class="truncate text-[13px] font-medium text-gray-500">Sustentabilidad</span>
                </span>
            </a>

            {{-- Cerrar (solo móvil) --}}
            <label for="menu-lateral"
                   class="ml-auto flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 focus-within:ring-2 lg:hidden">
                <span class="sr-only">Cerrar menú</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </label>
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 overflow-y-auto px-3 pb-6">

            <p class="px-3 pb-2 pt-3 text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                Espacio de trabajo
            </p>

            <ul class="space-y-1">
                {{-- Catálogo público --}}
                <li>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('acciones.consultar') ? route('acciones.consultar') : '#' }}"
                       @if (request()->routeIs('acciones.consultar')) aria-current="page" @endif
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#28AD56] focus-visible:ring-offset-2 {{ request()->routeIs('acciones.consultar') ? 'bg-uv-green/10 font-semibold text-[#1B7A40]' : 'font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('acciones.consultar') ? 'text-uv-green' : 'text-gray-400 group-hover:text-gray-500' }}"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="truncate">Catálogo público</span>
                    </a>
                </li>

                {{-- Registrar propuesta (sección activa) --}}
                <li>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('acciones.formulario-registro') ? route('acciones.formulario-registro') : '#' }}"
                       @if (request()->routeIs('acciones.formulario-registro')) aria-current="page" @endif
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#28AD56] focus-visible:ring-offset-2 {{ request()->routeIs('acciones.formulario-registro') ? 'bg-uv-green/10 font-semibold text-[#1B7A40]' : 'font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('acciones.formulario-registro') ? 'text-uv-green' : 'text-gray-400 group-hover:text-gray-500' }}"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 9h3.75m-3.75 3h3.75m-6-6H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="truncate">Registrar propuesta</span>
                    </a>
                </li>
            </ul>

            <p class="px-3 pb-2 pt-6 text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                Gestión
            </p>

            <ul class="space-y-1">
                {{-- Evaluación de propuestas --}}
                <li>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('acciones.evaluar') ? route('acciones.evaluar') : '#' }}"
                       @if (request()->routeIs('acciones.evaluar')) aria-current="page" @endif
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#28AD56] focus-visible:ring-offset-2 {{ request()->routeIs('acciones.evaluar') ? 'bg-uv-green/10 font-semibold text-[#1B7A40]' : 'font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('acciones.evaluar') ? 'text-uv-green' : 'text-gray-400 group-hover:text-gray-500' }}"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="truncate">Evaluación de propuestas</span>
                    </a>
                </li>
            </ul>
        </nav>

        {{-- Tarjeta de ayuda --}}
        <div class="shrink-0 border-t border-gray-200 p-3">
            <div class="rounded-xl bg-gray-50 p-4">
                <div class="flex items-start gap-3">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#E8F6EC] text-[11px] font-bold text-[#1B7A40]" aria-hidden="true">
                        i
                    </span>
                    <div class="min-w-0">
                        <p class="text-[13px] font-semibold text-gray-900">¿Necesitas ayuda?</p>
                        <p class="mt-1 text-[11px] leading-relaxed text-gray-500">
                            Consulta los criterios de registro de iniciativas.
                        </p>
                        <a href="#" class="mt-2 inline-flex text-[11px] font-semibold text-uv-blue underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#18529D] focus-visible:ring-offset-2">
                            Ver lineamientos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    {{-- Fondo oscuro al abrir el menú en móvil --}}
    <label for="menu-lateral"
           class="fixed inset-0 z-40 hidden bg-gray-900/40 peer-checked:block lg:hidden">
        <span class="sr-only">Cerrar menú</span>
    </label>

    {{-- ============================ TOPBAR ============================ --}}
    <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/95 backdrop-blur">
        <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:h-20 lg:px-8">

            {{-- Abrir menú (solo móvil) --}}
            <label for="menu-lateral"
                   class="-ml-1 flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 lg:hidden">
                <span class="sr-only">Abrir menú</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </label>

            <div class="min-w-0">
                <p class="truncate text-base font-bold text-gray-900 lg:text-lg">Registro de propuesta</p>
                <p class="hidden truncate text-xs text-gray-500 sm:block">Comparte una iniciativa de sustentabilidad</p>
            </div>

            <div class="ml-auto flex shrink-0 items-center gap-3">
                {{-- Institución (azul institucional) --}}
                <span class="hidden items-center rounded-full border border-uv-blue/20 bg-uv-blue/5 px-3 py-1 text-[11px] font-semibold text-uv-blue md:inline-flex">
                    Universidad Veracruzana
                </span>

                {{-- Usuario --}}
                <div class="flex items-center gap-3 border-l border-gray-200 pl-3">
                    <div class="hidden text-right sm:block">
                        <p class="max-w-45 truncate text-xs font-semibold text-gray-900">
                            {{ auth()->user()?->name ?? 'Usuario invitado' }}
                        </p>
                        <p class="text-[10px] text-uv-blue">Usuario institucional</p>
                    </div>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#E8F6EC] text-[11px] font-bold text-[#1B7A40]" aria-hidden="true">
                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(auth()->user()?->name ?? 'Invitado', 0, 2)) }}
                    </span>
                </div>
            </div>
        </div>
    </header>

    {{-- ============================ CONTENIDO ============================ --}}
    <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        {{ $slot }}
    </main>
</div>

</body>
</html>
