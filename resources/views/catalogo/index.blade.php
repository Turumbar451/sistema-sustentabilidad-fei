
@extends('layouts.app')

@section('title', 'Catálogo público | FEI Sustentabilidad')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Datos de prueba
    |--------------------------------------------------------------------------
    | Estos datos solamente se utilizan cuando el controlador no proporciona
    | una colección de iniciativas.
    */

    if (!isset($items)) {
        $items = collect([
            [
                'id' => 1,
                'titulo' => 'Jornada de recuperación verde',
                'descripcion' => 'Recuperación de espacios comunes con especies nativas y participación voluntaria.',
                'categoria' => 'Áreas verdes',
                'color_tema' => 'green',
                'icono' => 'hoja',
                'fecha' => '22 NOV 2026',
                'modalidad' => 'PRESENCIAL',
            ],
            [
                'id' => 2,
                'titulo' => 'Separación de residuos en el campus',
                'descripcion' => 'Promover la separación responsable y el aprovechamiento de materiales reciclables.',
                'categoria' => 'Gestión de residuos',
                'color_tema' => 'amber',
                'icono' => 'residuos',
                'fecha' => '25 NOV 2026',
                'modalidad' => 'PRESENCIAL',
            ],
            [
                'id' => 3,
                'titulo' => 'Campaña de ahorro de agua',
                'descripcion' => 'Acciones informativas para reducir el consumo y cuidar los recursos hídricos.',
                'categoria' => 'Uso responsable del agua',
                'color_tema' => 'blue',
                'icono' => 'agua',
                'fecha' => '02 DIC 2026',
                'modalidad' => 'HÍBRIDA',
            ],
        ]);
    }

    if (!isset($categorias)) {
        $categorias = [
            'Áreas verdes y biodiversidad',
            'Agua',
            'Residuos',
            'Energía y consumo',
        ];
    }

    $categoriasSeleccionadas = (array) request()->input('categorias', []);

    $totalItems = method_exists($items, 'total')
        ? $items->total()
        : count($items);
@endphp


<div class="min-h-screen bg-[#F5F7F4]">

    {{-- ============================================================
         NAVBAR
    ============================================================= --}}

    <header class="h-[86px] bg-white border-b border-[#E0E7E1]">

        <div class="mx-auto flex h-full max-w-[1320px] items-center justify-between px-6">

            {{-- Identidad FEI --}}
            <a href="/" class="flex items-center gap-3" aria-label="FEI Sustentabilidad, inicio">

                <div class="flex h-[34px] w-[34px] items-center justify-center rounded-[10px] bg-[#287A55]">
                    <div class="h-4 w-4 rounded-full bg-[#DDF3C8]"></div>
                </div>

                <div class="flex items-center gap-1.5">
                    <span class="text-[19px] font-extrabold text-[#153B35]">
                        FEI
                    </span>

                    <span class="text-[13px] font-medium text-[#68766F]">
                        Sustentabilidad
                    </span>
                </div>

            </a>


            {{-- Navegación --}}
            <nav class="hidden items-center gap-10 md:flex" aria-label="Navegación principal">

                <a href="#catalogo"
                   class="text-sm font-semibold text-[#153B35] transition hover:text-[#287A55]">
                    Explorar iniciativas
                </a>

                <a href="{{ url('/como-participar') }}"
                   class="text-sm font-medium text-[#68766F] transition hover:text-[#153B35]">
                    Cómo participar
                </a>

            </nav>


            {{-- Acceso institucional --}}
            <a href="{{ url('/login') }}"
               class="rounded-[9px] bg-[#153B35] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#245244]">
                Acceso institucional
            </a>

        </div>

    </header>



    {{-- ============================================================
         FORMULARIO GENERAL DE BÚSQUEDA Y FILTROS

         Se utiliza un solo formulario GET para que el buscador,
         los filtros y el ordenamiento puedan enviarse juntos.
    ============================================================= --}}

    <form action="{{ url()->current() }}" method="GET" id="catalogo-filtros">


        {{-- ========================================================
             HERO BANNER
        ========================================================= --}}

        <section class="relative min-h-[316px] overflow-hidden bg-[#153B35]">

            {{-- Gráfico decorativo: círculos concéntricos --}}
            <div class="pointer-events-none absolute right-[5.5%] top-[-52px] hidden h-[232px] w-[232px] items-center justify-center rounded-full bg-[#1D4B42] lg:flex">

                <div class="flex h-[144px] w-[144px] items-center justify-center rounded-full bg-[#2A6952]">

                    <div class="h-[68px] w-[68px] rounded-full bg-[#DDF3C8]"></div>

                </div>

            </div>


            {{-- Contenido del hero --}}
            <div class="relative z-10 mx-auto max-w-[1280px] px-6 pb-8 pt-[48px] xl:px-0">

                <p class="mb-4 text-[11px] font-extrabold uppercase tracking-[1.5px] text-[#DDF3C8]">
                    Acciones de sustentabilidad
                </p>


                <h1 class="max-w-[650px] text-[32px] font-extrabold leading-[1.2] text-white sm:text-[39px]">

                    Pequeñas acciones,

                    <span class="block text-[#DDF3C8]">
                        grandes cambios.
                    </span>

                </h1>


                <p class="mt-4 max-w-[600px] text-[15px] leading-relaxed text-[#D9E7DE]">
                    Conoce las iniciativas que transforman nuestra comunidad universitaria.
                </p>


                {{-- Buscador --}}
                <div class="mt-5 grid max-w-[747px] grid-cols-1 gap-3 sm:grid-cols-[minmax(0,1fr)_130px] sm:gap-[17px]">

                    <div class="relative">

                        <svg class="absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-[#839188]"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             aria-hidden="true">

                            <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                            <path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/>

                        </svg>


                        <label for="buscar" class="sr-only">
                            Buscar iniciativas por nombre o tema
                        </label>

                        <input
                            type="search"
                            id="buscar"
                            name="buscar"
                            value="{{ request('buscar') }}"
                            placeholder="Buscar iniciativas por nombre o tema"
                            class="h-12 w-full rounded-[10px] border-0 bg-white pl-12 pr-4 text-sm text-[#153B35] placeholder:text-[#839188] focus:outline-none focus:ring-2 focus:ring-[#DDF3C8]"
                        >

                    </div>


                    <button
                        type="submit"
                        class="h-12 rounded-[9px] bg-[#287A55] px-6 text-sm font-semibold text-white transition hover:bg-[#359365] focus:outline-none focus:ring-2 focus:ring-[#DDF3C8] focus:ring-offset-2 focus:ring-offset-[#153B35]"
                    >
                        Buscar
                    </button>

                </div>

            </div>

        </section>



        {{-- ========================================================
             CONTENIDO PRINCIPAL
        ========================================================= --}}

        <main id="catalogo" class="bg-[#F5F7F4]">

            <div class="mx-auto max-w-[1280px] px-6 pb-12 pt-[46px] xl:px-0">


                {{-- Encabezado del catálogo --}}
                <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">

                    <div>

                        <h2 class="text-[25px] font-extrabold leading-tight text-[#153B35]">
                            Explora las iniciativas
                        </h2>

                        <p class="mt-2 text-[13px] text-[#68766F]">
                            Consulta acciones autorizadas para la comunidad y el público.
                        </p>

                    </div>


                    {{-- Ordenamiento --}}
                    <div class="flex items-center gap-2 text-xs font-semibold text-[#68766F]">

                        <label for="ordenar" class="whitespace-nowrap">
                            Ordenar:
                        </label>

                        <select
                            id="ordenar"
                            name="ordenar"
                            class="border-0 bg-transparent py-1 pr-6 text-xs font-semibold text-[#68766F] focus:ring-0"
                        >

                            <option value="recientes"
                                @selected(request('ordenar', 'recientes') === 'recientes')>
                                Más recientes
                            </option>

                            <option value="antiguas"
                                @selected(request('ordenar') === 'antiguas')>
                                Más antiguas
                            </option>

                        </select>

                    </div>

                </div>



                {{-- =================================================
                     LAYOUT: SIDEBAR + GRID
                ================================================== --}}

                <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[230px_minmax(0,1fr)] lg:gap-[30px]">


                    {{-- =================================================
                         SIDEBAR DE FILTROS
                    ================================================== --}}

                    <aside class="w-full rounded-[14px] border border-[#E0E7E1] bg-white p-5 lg:w-[230px]">

                        <h3 class="text-base font-bold text-[#153B35]">
                            Filtrar resultados
                        </h3>


                        <div class="my-4 border-t border-[#E0E7E1]"></div>


                        {{-- Filtro por eje de trabajo --}}
                        <fieldset>

                            <legend class="mb-4 text-[13px] font-semibold text-[#153B35]">
                                Eje de trabajo
                            </legend>


                            <div class="space-y-[18px]">

                                @foreach ($categorias as $categoria)

                                    @php
                                        $nombreCategoria = is_string($categoria)
                                            ? $categoria
                                            : (
                                                data_get($categoria, 'nombre')
                                                ?? data_get($categoria, 'name')
                                                ?? 'Categoría'
                                            );
                                    @endphp

                                    <div class="flex items-start gap-3">

                                        <input
                                            type="checkbox"
                                            id="categoria-{{ $loop->index }}"
                                            name="categorias[]"
                                            value="{{ $nombreCategoria }}"
                                            @checked(in_array($nombreCategoria, $categoriasSeleccionadas, true))
                                            class="mt-0.5 h-4 w-4 shrink-0 rounded border-[#E0E7E1] text-[#287A55] focus:ring-[#287A55]"
                                        >

                                        <label
                                            for="categoria-{{ $loop->index }}"
                                            class="cursor-pointer text-[11px] leading-relaxed text-[#1B2924]"
                                        >
                                            {{ $nombreCategoria }}
                                        </label>

                                    </div>

                                @endforeach

                            </div>

                        </fieldset>



                        <div class="my-5 border-t border-[#E0E7E1]"></div>



                        {{-- Filtro por modalidad --}}
                        <fieldset>

                            <legend class="mb-4 text-[13px] font-semibold text-[#153B35]">
                                Modalidad
                            </legend>


                            <div class="space-y-3">

                                <div class="flex items-center gap-3">

                                    <input
                                        type="radio"
                                        id="modalidad-presencial"
                                        name="modalidad"
                                        value="Presencial"
                                        @checked(request('modalidad') === 'Presencial')
                                        class="h-4 w-4 border-[#E0E7E1] text-[#287A55] focus:ring-[#287A55]"
                                    >

                                    <label
                                        for="modalidad-presencial"
                                        class="cursor-pointer text-[11px] text-[#1B2924]"
                                    >
                                        Presencial
                                    </label>

                                </div>


                                <div class="flex items-center gap-3">

                                    <input
                                        type="radio"
                                        id="modalidad-virtual"
                                        name="modalidad"
                                        value="Virtual"
                                        @checked(request('modalidad') === 'Virtual')
                                        class="h-4 w-4 border-[#E0E7E1] text-[#287A55] focus:ring-[#287A55]"
                                    >

                                    <label
                                        for="modalidad-virtual"
                                        class="cursor-pointer text-[11px] text-[#1B2924]"
                                    >
                                        Virtual
                                    </label>

                                </div>

                            </div>

                        </fieldset>



                        {{-- Limpiar filtros --}}
                        <a
                            href="{{ url()->current() }}"
                            class="mt-7 flex h-9 w-full items-center justify-center rounded-[9px] border border-[#E0E7E1] bg-white text-sm font-semibold text-[#287A55] transition hover:bg-[#EEF4EF]"
                        >
                            Limpiar filtros
                        </a>

                    </aside>



                    {{-- =================================================
                         GRID DE TARJETAS
                    ================================================== --}}

                    <section class="min-w-0">

                        <div class="grid grid-cols-1 items-stretch gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-[25px]">


                            @forelse ($items as $item)

                                <x-catalogo-card :item="$item" />

                            @empty

                                {{-- Estado sin resultados --}}
                                <div class="col-span-full rounded-xl border border-[#E0E7E1] bg-white px-6 py-16 text-center">

                                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[#EEF4EF]">

                                        <svg class="h-7 w-7 text-[#287A55]"
                                             xmlns="http://www.w3.org/2000/svg"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor"
                                             aria-hidden="true">

                                            <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                                            <path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/>

                                        </svg>

                                    </div>


                                    <h3 class="text-lg font-bold text-[#153B35]">
                                        No se encontraron iniciativas
                                    </h3>

                                    <p class="mt-2 text-sm text-[#68766F]">
                                        Intenta modificar los criterios de búsqueda o limpiar los filtros.
                                    </p>

                                    <a href="{{ url()->current() }}"
                                       class="mt-5 inline-flex text-sm font-semibold text-[#287A55] hover:underline">
                                        Limpiar filtros
                                    </a>

                                </div>

                            @endforelse


                        </div>



                        {{-- Paginación --}}
                        @if (method_exists($items, 'links'))

                            <div class="mt-8">
                                {{ $items->links() }}
                            </div>

                        @endif



                        {{-- Contador de iniciativas --}}
                        <p class="mt-10 text-center text-xs font-medium text-[#68766F]">

                            Mostrando {{ $totalItems }}
                            {{ $totalItems === 1 ? 'iniciativa publicada' : 'iniciativas publicadas' }}

                        </p>

                    </section>


                </div>

            </div>

        </main>

    </form>

</div>

@endsection
