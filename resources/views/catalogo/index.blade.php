@extends('layouts.guest')

@section('title', 'Catálogo público | FEI Sustentabilidad')
@section('meta_description', 'Explora las iniciativas de sustentabilidad de la comunidad universitaria de la FEI.')

@section('content')
    {{-- Navbar --}}
    <header class="border-b border-[#E0E7E1] bg-white">
        <div class="mx-auto flex h-21.5 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('catalogo.index') }}"
               class="flex shrink-0 items-center gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2"
               aria-label="FEI Sustentabilidad, inicio">
                <span class="flex h-8.5 w-8.5 items-center justify-center rounded-[10px] bg-[#2A7B56]" aria-hidden="true">
                    <span class="h-4 w-4 rounded-full bg-[#DFECC6]"></span>
                </span>
                <span class="flex items-baseline gap-2">
                    <span class="text-[19px] font-extrabold tracking-tight text-[#16382B]">FEI</span>
                    <span class="hidden text-[13px] font-medium text-[#68766F] sm:inline">Sustentabilidad</span>
                </span>
            </a>

            <nav class="ml-auto hidden items-center gap-8 md:flex" aria-label="Navegación principal">
                <a href="{{ route('catalogo.index') }}"
                   aria-current="page"
                   class="rounded text-sm font-semibold text-[#16382B] underline-offset-8 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-4">
                    Explorar iniciativas
                </a>
                <a href="#como-participar"
                   class="rounded text-sm font-medium text-[#68766F] transition hover:text-[#16382B] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-4">
                    Cómo participar
                </a>
            </nav>

            <a href="{{ url('/login') }}"
               class="shrink-0 rounded-[9px] bg-[#16382B] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0F2A20] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                Acceso institucional
            </a>
        </div>
    </header>

    {{-- Un solo formulario GET mantiene sincronizados búsqueda, filtros y orden. --}}
    <form method="GET" action="{{ route('catalogo.index') }}" id="catalogo-filtros">
        {{-- Hero --}}
        <section class="relative overflow-hidden bg-[#16382B]" aria-labelledby="catalogo-titulo">
            <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-130 md:block" aria-hidden="true">
                <div class="absolute right-19.75 -top-13 h-58 w-58 rounded-full bg-[#1D4838]"></div>
                <div class="absolute right-30.75 -top-2 h-36 w-36 rounded-full bg-[#2C6A50]"></div>
                <div class="absolute right-40.25 top-7.5 h-17 w-17 rounded-full bg-[#DFECC6]"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-16">
                <div class="max-w-2xl">
                    <p class="text-[11px] font-bold uppercase tracking-[1.5px] text-[#DFECC6]">
                        Acciones de sustentabilidad
                    </p>

                    <h1 id="catalogo-titulo" class="mt-5 text-3xl font-extrabold leading-tight text-white sm:text-4xl lg:text-[39px]">
                        Pequeñas acciones,<br>
                        <span class="text-[#DFECC6]">grandes cambios.</span>
                    </h1>

                    <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-[#D9E7DE]">
                        Conoce las iniciativas que transforman nuestra comunidad universitaria.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <div class="relative min-w-0 flex-1">
                            <label for="busqueda" class="sr-only">Buscar iniciativas por nombre o tema</label>
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#839188]"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m20 20-3.5-3.5" stroke-linecap="round" />
                            </svg>
                            <input
                                type="search"
                                id="busqueda"
                                name="q"
                                value="{{ $busqueda }}"
                                placeholder="Buscar iniciativas por nombre o tema"
                                autocomplete="off"
                                class="h-12 w-full rounded-[10px] border-0 bg-white pl-11 pr-4 text-sm text-[#1B2924] placeholder:text-[#839188] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#DFECC6]"
                            >
                        </div>

                        <button type="submit"
                                class="h-12 shrink-0 rounded-[9px] bg-[#2A7B56] px-8 text-sm font-semibold text-white transition hover:bg-[#236847] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#DFECC6] focus-visible:ring-offset-2 focus-visible:ring-offset-[#16382B]">
                            Buscar
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contenido y filtros --}}
        <main id="contenido-principal" class="bg-[#F7F9F6]">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-[#16382B]">Explora las iniciativas</h2>
                        <p class="mt-1.5 text-[13px] text-[#68766F]">
                            Consulta acciones autorizadas para la comunidad y el público.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="orden" class="shrink-0 text-xs font-semibold text-[#68766F]">Ordenar:</label>
                        <select
                            id="orden"
                            name="orden"
                            class="cursor-pointer rounded-lg border border-[#E0E7E1] bg-white py-2 pl-3 pr-8 text-xs font-semibold text-[#16382B] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56]"
                        >
                            <option value="recientes" @selected($ordenSeleccionado === 'recientes')>Más recientes</option>
                            <option value="proximas" @selected($ordenSeleccionado === 'proximas')>Próximas a realizarse</option>
                            <option value="az" @selected($ordenSeleccionado === 'az')>Título (A–Z)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-12 lg:gap-7">
                    {{-- Sidebar de filtros --}}
                    <aside class="lg:col-span-3" aria-label="Filtros de búsqueda">
                        <div class="rounded-xl border border-[#E0E7E1] bg-white p-5 sm:p-6">
                            <h2 class="text-base font-bold text-[#16382B]">Filtrar resultados</h2>
                            <hr class="mt-4 border-[#E0E7E1]">

                            <fieldset class="mt-5">
                                <legend class="text-[13px] font-semibold text-[#16382B]">Eje de trabajo</legend>
                                <div class="mt-3 space-y-3">
                                    @foreach ($categorias as $categoria)
                                        @php($idEje = 'eje-' . $loop->index)
                                        <div class="flex items-start gap-3">
                                            <input
                                                type="checkbox"
                                                id="{{ $idEje }}"
                                                name="ejes[]"
                                                value="{{ $categoria }}"
                                                @checked(in_array($categoria, $ejesSeleccionados, true))
                                                class="mt-0.5 h-4 w-4 shrink-0 rounded border-[#C9D6CD] text-[#2A7B56] accent-[#2A7B56] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2"
                                            >
                                            <label for="{{ $idEje }}" class="cursor-pointer text-[11px] font-medium leading-snug text-[#1B2924]">
                                                {{ $categoria }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>

                            <hr class="mt-6 border-[#E0E7E1]">

                            <fieldset class="mt-5">
                                <legend class="text-[13px] font-semibold text-[#16382B]">Modalidad</legend>
                                <div class="mt-3 space-y-3">
                                    @foreach ($modalidades as $modalidad)
                                        @php($idModalidad = 'modalidad-' . $loop->index)
                                        <div class="flex items-center gap-3">
                                            <input
                                                type="radio"
                                                id="{{ $idModalidad }}"
                                                name="modalidad"
                                                value="{{ $modalidad }}"
                                                @checked($modalidadSeleccionada === $modalidad)
                                                class="h-4 w-4 shrink-0 border-[#C9D6CD] text-[#2A7B56] accent-[#2A7B56] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2"
                                            >
                                            <label for="{{ $idModalidad }}" class="cursor-pointer text-[11px] text-[#1B2924]">
                                                {{ $modalidad }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>

                            <button type="submit"
                                    class="mt-6 w-full rounded-[9px] bg-[#2A7B56] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#236847] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                                Aplicar filtros
                            </button>

                            <a href="{{ route('catalogo.index') }}"
                               class="mt-3 flex w-full items-center justify-center rounded-[9px] border border-gray-300 px-4 py-2 text-sm font-semibold text-[#2A7B56] transition hover:bg-[#F7F9F6] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                                Limpiar filtros
                            </a>
                        </div>
                    </aside>

                    {{-- Grid de iniciativas --}}
                    <section class="min-w-0 lg:col-span-9" aria-label="Resultados del catálogo" aria-live="polite">
                        @if ($items->isEmpty())
                            <div class="rounded-xl border border-dashed border-[#C9D6CD] bg-white px-6 py-16 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#DFEDE2]" aria-hidden="true">
                                    <svg class="h-6 w-6 text-[#2A7B56]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="11" cy="11" r="7" />
                                        <path d="m20 20-3.5-3.5" stroke-linecap="round" />
                                    </svg>
                                </span>
                                <h3 class="mt-4 text-base font-bold text-[#16382B]">No encontramos iniciativas</h3>
                                <p class="mx-auto mt-2 max-w-sm text-sm text-[#68766F]">
                                    Prueba con otro término de búsqueda o quita algunos filtros para ampliar los resultados.
                                </p>
                                <a href="{{ route('catalogo.index') }}"
                                   class="mt-5 inline-flex rounded-[9px] border border-gray-300 px-4 py-2 text-sm font-semibold text-[#2A7B56] transition hover:bg-[#F7F9F6] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                                    Limpiar filtros
                                </a>
                            </div>
                        @else
                            <div class="grid grid-cols-1 items-stretch gap-6 md:grid-cols-2 xl:grid-cols-3">
                                @foreach ($items as $item)
                                    <x-catalogo-card :item="$item" />
                                @endforeach
                            </div>

                            <div class="mt-8">
                                <p class="mb-5 text-center text-xs font-medium text-[#68766F]">
                                    Mostrando {{ $items->firstItem() }}–{{ $items->lastItem() }} de {{ $items->total() }} iniciativas
                                </p>

                                @if ($items->hasPages())
                                    <div>
                                        {{ $items->withQueryString()->links() }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </main>
    </form>

    <footer id="como-participar" class="border-t border-[#E0E7E1] bg-white">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 py-8 sm:flex-row sm:px-6 lg:px-8">
            <p class="text-xs text-[#68766F]">© {{ date('Y') }} FEI · Sustentabilidad</p>
            <nav class="flex items-center gap-6" aria-label="Enlaces del pie de página">
                <a href="#" class="rounded text-xs text-[#68766F] transition hover:text-[#16382B] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">Aviso de privacidad</a>
                <a href="#" class="rounded text-xs text-[#68766F] transition hover:text-[#16382B] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">Contacto</a>
            </nav>
        </div>
    </footer>
@endsection
