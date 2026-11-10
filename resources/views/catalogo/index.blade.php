{{--
    resources/views/catalogo/index.blade.php
    CU-04 · Catálogo público de acciones de sustentabilidad
    Laravel + Blade + Tailwind CSS (solo clases utilitarias)

    CONTRATO DE DATOS (lo que debe enviar el controlador):
      $items       : LengthAwarePaginator de acciones publicadas
                     (id, titulo, descripcion, categoria, color_tema, icono, fecha, modalidad, url)
      $categorias  : array|Collection de ejes de trabajo para el sidebar
      $modalidades : array|Collection de modalidades (opcional)

    Sin controlador / sin base de datos: se genera automáticamente un
    LengthAwarePaginator con datos de ejemplo para que la vista se pueda
    previsualizar en el navegador.
--}}

@php
    if (! isset($items)) {
        $demo = [
            [
                'id' => 1,
                'titulo' => 'Jornada de recuperación verde',
                'descripcion' => 'Recuperación de espacios comunes con especies nativas y participación voluntaria de la comunidad universitaria.',
                'categoria' => 'Áreas verdes',
                'color_tema' => 'green',
                'icono' => 'hoja',
                'fecha' => '22 NOV 2026',
                'modalidad' => 'Presencial',
                'url' => '#',
            ],
            [
                'id' => 2,
                'titulo' => 'Separación de residuos en el campus',
                'descripcion' => 'Promover la separación responsable y el aprovechamiento de materiales reciclables en cada edificio.',
                'categoria' => 'Gestión de residuos',
                'color_tema' => 'amber',
                'icono' => 'reciclaje',
                'fecha' => '25 NOV 2026',
                'modalidad' => 'Presencial',
                'url' => '#',
            ],
            [
                'id' => 3,
                'titulo' => 'Campaña de ahorro de agua',
                'descripcion' => 'Acciones informativas para reducir el consumo y cuidar los recursos hídricos del campus.',
                'categoria' => 'Uso responsable del agua',
                'color_tema' => 'blue',
                'icono' => 'agua',
                'fecha' => '02 DIC 2026',
                'modalidad' => 'Híbrida',
                'url' => '#',
            ],
            [
                'id' => 4,
                'titulo' => 'Ruta segura hacia el campus',
                'descripcion' => 'Acompañamiento y señalización de rutas peatonales y ciclistas para llegar de forma segura.',
                'categoria' => 'Movilidad',
                'color_tema' => 'green',
                'icono' => 'comunidad',
                'fecha' => '05 DIC 2026',
                'modalidad' => 'Presencial',
                'url' => '#',
            ],
            [
                'id' => 5,
                'titulo' => 'Eficiencia energética en laboratorios',
                'descripcion' => 'Diagnóstico y buenas prácticas para reducir el consumo eléctrico en laboratorios y talleres.',
                'categoria' => 'Energía y consumo',
                'color_tema' => 'amber',
                'icono' => 'energia',
                'fecha' => '09 DIC 2026',
                'modalidad' => 'Virtual',
                'url' => '#',
            ],
            [
                'id' => 6,
                'titulo' => 'Monitoreo comunitario del agua',
                'descripcion' => 'Taller abierto para aprender a medir calidad del agua en cuerpos cercanos al campus.',
                'categoria' => 'Agua',
                'color_tema' => 'blue',
                'icono' => 'agua',
                'fecha' => '12 DIC 2026',
                'modalidad' => 'Híbrida',
                'url' => '#',
            ],
        ];

        $items = new \Illuminate\Pagination\LengthAwarePaginator($demo, count($demo), 9);
    }

    $categorias = $categorias ?? [
        'Áreas verdes y biodiversidad',
        'Agua',
        'Residuos',
        'Energía y consumo',
    ];

    $modalidades = $modalidades ?? ['Presencial', 'Virtual', 'Híbrida'];

    // Estado de los filtros (llegan por query string desde el formulario)
    $ejesSeleccionados = array_filter((array) request('ejes', []));
    $modalidadesSeleccionadas = array_filter((array) request('modalidad', []));
    $busqueda = request('q');
    $orden = request('orden', 'recientes');

    $hayFiltros = $busqueda || $ejesSeleccionados || $modalidadesSeleccionadas;
@endphp

<x-guest-layout>
    {{-- ============================ NAVBAR ============================ --}}
    <header class="sticky top-0 z-40 border-b border-[#E0E7E1] bg-white">
        <div class="mx-auto flex h-[86px] max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                <span class="flex h-[34px] w-[34px] items-center justify-center rounded-[10px] bg-[#2A7B56]" aria-hidden="true">
                    <span class="h-4 w-4 rounded-full bg-[#DFECC6]"></span>
                </span>
                <span class="flex items-baseline gap-2">
                    <span class="text-[19px] font-bold tracking-tight text-[#16382B]">FEI</span>
                    <span class="hidden text-[13px] font-medium text-[#68766F] sm:inline">Sustentabilidad</span>
                </span>
            </a>

            {{-- Navegación principal --}}
            <nav class="ml-auto hidden items-center gap-8 md:flex" aria-label="Navegación principal">
                <a href="{{ url()->current() }}" aria-current="page"
                   class="rounded text-sm font-semibold text-[#16382B] underline-offset-8 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-4">
                    Explorar iniciativas
                </a>
                <a href="#" class="rounded text-sm font-medium text-[#68766F] transition hover:text-[#16382B] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-4">
                    Cómo participar
                </a>
            </nav>

            {{-- CTA --}}
            <a href="#" class="ml-auto rounded-[9px] bg-[#16382B] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0F2A20] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2 md:ml-10">
                Acceso institucional
            </a>
        </div>
    </header>

    {{-- ============================= HERO ============================= --}}
    <section class="relative overflow-hidden bg-[#16382B]" aria-labelledby="catalogo-titulo">
        {{-- Gráfico decorativo: 3 círculos concéntricos --}}
        <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-[520px] md:block" aria-hidden="true">
            <div class="absolute right-[79px] top-[-52px] h-[232px] w-[232px] rounded-full bg-[#1D4838]"></div>
            <div class="absolute right-[123px] top-[-8px] h-[144px] w-[144px] rounded-full bg-[#2C6A50]"></div>
            <div class="absolute right-[161px] top-[30px] h-[68px] w-[68px] rounded-full bg-[#DFECC6]"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-16">
            <div class="max-w-2xl">
                <p class="text-[11px] font-bold uppercase tracking-[1.5px] text-[#DFECC6]">
                    Acciones de sustentabilidad
                </p>

                <h1 id="catalogo-titulo" class="mt-5 text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-[39px]">
                    Pequeñas acciones,<br>
                    <span class="text-[#DFECC6]">grandes cambios.</span>
                </h1>

                <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-[#D9E7DE]">
                    Conoce las iniciativas que transforman nuestra comunidad universitaria.
                </p>

                {{-- Búsqueda --}}
                <form method="GET" action="{{ url()->current() }}" role="search" class="mt-8 flex flex-col gap-3 sm:flex-row">
                    @foreach ($ejesSeleccionados as $eje)
                        <input type="hidden" name="ejes[]" value="{{ $eje }}">
                    @endforeach
                    @foreach ($modalidadesSeleccionadas as $modalidad)
                        <input type="hidden" name="modalidad[]" value="{{ $modalidad }}">
                    @endforeach
                    @if ($orden !== 'recientes')
                        <input type="hidden" name="orden" value="{{ $orden }}">
                    @endif

                    <div class="relative flex-1">
                        <label for="busqueda" class="sr-only">Buscar iniciativas por nombre o tema</label>
                        <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#839188]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5" stroke-linecap="round"/>
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
                </form>
            </div>
        </div>
    </section>

    {{-- ======================== CONTENIDO PRINCIPAL ======================== --}}
    <main id="contenido-principal" class="bg-[#F7F9F6]">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">

            {{-- Cabecera de resultados + orden --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-[#16382B]">Explora las iniciativas</h2>
                    <p class="mt-1.5 text-[13px] text-[#68766F]">
                        Consulta acciones autorizadas para la comunidad y el público.
                    </p>
                </div>

                <form method="GET" action="{{ url()->current() }}" class="shrink-0">
                    @if ($busqueda)
                        <input type="hidden" name="q" value="{{ $busqueda }}">
                    @endif
                    @foreach ($ejesSeleccionados as $eje)
                        <input type="hidden" name="ejes[]" value="{{ $eje }}">
                    @endforeach
                    @foreach ($modalidadesSeleccionadas as $modalidad)
                        <input type="hidden" name="modalidad[]" value="{{ $modalidad }}">
                    @endforeach

                    <label for="orden" class="text-xs font-semibold text-[#68766F]">Ordenar:</label>
                    <select
                        id="orden"
                        name="orden"
                        onchange="this.form.submit()"
                        class="ml-1 cursor-pointer rounded-lg border border-[#E0E7E1] bg-white py-1.5 pl-2 pr-7 text-xs font-semibold text-[#16382B] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56]"
                    >
                        <option value="recientes" @selected($orden === 'recientes')>Más recientes</option>
                        <option value="proximas" @selected($orden === 'proximas')>Próximas a realizarse</option>
                        <option value="az" @selected($orden === 'az')>Título (A–Z)</option>
                    </select>
                </form>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-12">

                {{-- ================= SIDEBAR DE FILTROS ================= --}}
                <aside class="lg:col-span-3" aria-label="Filtros de búsqueda">
                    <form method="GET" action="{{ url()->current() }}"
                          class="rounded-xl border border-[#E0E7E1] bg-white p-6">

                        @if ($busqueda)
                            <input type="hidden" name="q" value="{{ $busqueda }}">
                        @endif
                        @if ($orden !== 'recientes')
                            <input type="hidden" name="orden" value="{{ $orden }}">
                        @endif

                        <h2 class="text-base font-bold text-[#16382B]">Filtrar resultados</h2>
                        <hr class="mt-4 border-[#E0E7E1]">

                        {{-- Eje de trabajo (checkboxes) --}}
                        <fieldset class="mt-5">
                            <legend class="text-[13px] font-semibold text-[#16382B]">Eje de trabajo</legend>

                            <div class="mt-3 space-y-3">
                                @foreach ($categorias as $categoria)
                                    @php $idEje = 'eje-'.\Illuminate\Support\Str::slug($categoria); @endphp
                                    <div class="flex items-start gap-3">
                                        <input
                                            type="checkbox"
                                            id="{{ $idEje }}"
                                            name="ejes[]"
                                            value="{{ $categoria }}"
                                            @checked(in_array($categoria, $ejesSeleccionados, true))
                                            class="mt-0.5 h-4 w-4 shrink-0 rounded border-[#C9D6CD] text-[#2A7B56] accent-[#2A7B56] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2"
                                        >
                                        <label for="{{ $idEje }}" class="text-[11px] font-medium leading-snug text-[#1B2924]">
                                            {{ $categoria }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>

                        <hr class="mt-6 border-[#E0E7E1]">

                        {{-- Modalidad (radios) --}}
                        <fieldset class="mt-5">
                            <legend class="text-[13px] font-semibold text-[#16382B]">Modalidad</legend>

                            <div class="mt-3 space-y-3">
                                @foreach ($modalidades as $modalidad)
                                    @php $idModalidad = 'modalidad-'.\Illuminate\Support\Str::slug($modalidad); @endphp
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="radio"
                                            id="{{ $idModalidad }}"
                                            name="modalidad[]"
                                            value="{{ $modalidad }}"
                                            @checked(in_array($modalidad, $modalidadesSeleccionadas, true))
                                            class="h-4 w-4 shrink-0 border-[#C9D6CD] text-[#2A7B56] accent-[#2A7B56] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2"
                                        >
                                        <label for="{{ $idModalidad }}" class="text-[11px] text-[#1B2924]">
                                            {{ $modalidad }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>

                        {{-- Aplicar filtros --}}
                        <button type="submit"
                                class="mt-6 w-full rounded-[9px] bg-[#2A7B56] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#236847] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                            Aplicar filtros
                        </button>

                        {{-- Limpiar filtros --}}
                        <a href="{{ url()->current() }}"
                           class="mt-3 flex w-full items-center justify-center rounded-[9px] border border-gray-300 px-4 py-2 text-sm font-semibold text-[#2A7B56] transition hover:bg-[#F7F9F6] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                            Limpiar filtros
                        </a>
                    </form>
                </aside>

                {{-- ==================== GRID DE TARJETAS ==================== --}}
                <section class="lg:col-span-9" aria-label="Resultados del catálogo">
                    @if ($items->isEmpty())
                        {{-- Estado vacío --}}
                        <div class="rounded-xl border border-dashed border-[#C9D6CD] bg-white px-6 py-16 text-center">
                            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#DFEDE2]" aria-hidden="true">
                                <svg class="h-6 w-6 text-[#2A7B56]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="m20 20-3.5-3.5" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <h3 class="mt-4 text-base font-bold text-[#16382B]">No encontramos iniciativas</h3>
                            <p class="mx-auto mt-2 max-w-sm text-sm text-[#68766F]">
                                Prueba con otro término de búsqueda o quita algunos filtros para ampliar los resultados.
                            </p>
                            @if ($hayFiltros)
                                <a href="{{ url()->current() }}"
                                   class="mt-5 inline-flex rounded-[9px] border border-gray-300 px-4 py-2 text-sm font-semibold text-[#2A7B56] transition hover:bg-[#F7F9F6] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                                    Limpiar filtros
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($items as $item)
                                <x-catalogo-card
                                    :titulo="data_get($item, 'titulo')"
                                    :descripcion="data_get($item, 'descripcion')"
                                    :categoria="data_get($item, 'categoria')"
                                    :tema="data_get($item, 'color_tema', 'green')"
                                    :icono="data_get($item, 'icono', 'hoja')"
                                    :fecha="data_get($item, 'fecha')"
                                    :modalidad="data_get($item, 'modalidad')"
                                    :url="data_get($item, 'url')"
                                />
                            @endforeach
                        </div>

                        {{-- Conteo + paginación --}}
                        <div class="mt-10">
                            <p class="text-center text-xs font-medium text-[#68766F]">
                                @if ($items->total() === 1)
                                    Mostrando 1 iniciativa publicada
                                @else
                                    Mostrando {{ $items->count() }} de {{ $items->total() }} iniciativas publicadas
                                @endif
                            </p>

                            @if (method_exists($items, 'hasPages') && $items->hasPages())
                                <div class="mt-6">
                                    {{ $items->withQueryString()->links() }}
                                </div>
                            @endif
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </main>

    {{-- ============================= FOOTER ============================= --}}
    <footer class="border-t border-[#E0E7E1] bg-white">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 py-8 sm:flex-row sm:px-6 lg:px-8">
            <p class="text-xs text-[#68766F]">© {{ date('Y') }} FEI · Sustentabilidad</p>
            <nav class="flex items-center gap-6" aria-label="Enlaces del pie">
                <a href="#" class="text-xs text-[#68766F] transition hover:text-[#16382B] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2 rounded">Aviso de privacidad</a>
                <a href="#" class="text-xs text-[#68766F] transition hover:text-[#16382B] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2 rounded">Contacto</a>
            </nav>
        </div>
    </footer>
</x-guest-layout>
