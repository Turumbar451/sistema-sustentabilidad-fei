<x-layouts.app title="Catálogo público | FEI Sustentabilidad">
    <section class="relative overflow-hidden bg-uv-blue" aria-labelledby="consulta-titulo">
        <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-130 md:block" aria-hidden="true">
            <div class="absolute -top-13 right-19.75 h-58 w-58 rounded-full bg-white/10"></div>
            <div class="absolute -top-2 right-30.75 h-36 w-36 rounded-full bg-uv-green"></div>
            <div class="absolute right-40.25 top-7.5 h-17 w-17 rounded-full bg-white"></div>
        </div>
        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-uv-green-soft">Catálogo público</p>
            <h1 id="consulta-titulo" class="mt-4 max-w-2xl text-3xl font-bold leading-tight text-white sm:text-4xl">Consulta de acciones de sustentabilidad</h1>
            <p class="mt-4 max-w-xl text-sm leading-relaxed text-white/90">
                Explora las iniciativas autorizadas de nuestra comunidad universitaria.
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12 lg:gap-7">
            <aside class="lg:col-span-3" aria-label="Búsqueda y filtros">
                <x-accion-sustentabilidad.filtros-consulta
                    :ejes="$ejes"
                    :filtros="$filtros"
                    :modalidades="$modalidades"
                    :formas-trabajo="$formasTrabajo"
                    :estados="$estados"
                />
            </aside>

            <section class="min-w-0 lg:col-span-9" aria-label="Resultados del catálogo" aria-live="polite">
                <h2 class="text-2xl font-bold text-ink">Explora las iniciativas</h2>
                <p class="mt-2 text-sm text-muted">Consulta acciones autorizadas para la comunidad y el público.</p>

                @if ($acciones->isEmpty())
                    <div class="mt-6 rounded-xl border border-dashed border-line bg-white px-6 py-16 text-center">
                        <h3 class="text-base font-bold text-ink">
                            {{ $hayFiltros ? 'No encontramos iniciativas' : 'Aún no hay iniciativas publicadas' }}
                        </h3>
                        <p class="mx-auto mt-3 max-w-sm text-sm leading-relaxed text-muted">
                            {{ $hayFiltros
                                ? 'Prueba con otro término de búsqueda o quita algunos filtros para ampliar los resultados.'
                                : 'Las iniciativas autorizadas aparecerán aquí cuando estén disponibles.' }}
                        </p>
                        @if ($hayFiltros)
                            <a href="{{ route('acciones.consultar') }}" class="mt-5 inline-flex rounded-lg border border-line px-4 py-2 text-sm font-semibold text-uv-blue hover:bg-canvas focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue focus-visible:ring-offset-2">
                                Limpiar filtros
                            </a>
                        @endif
                    </div>
                @else
                    <div class="mt-6 grid grid-cols-1 items-stretch gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($acciones as $accion)
                            <x-accion-sustentabilidad.tarjeta-accion :accion="$accion" />
                        @endforeach
                    </div>
                    <p class="mt-8 text-center text-xs font-medium text-muted">
                        Mostrando {{ $acciones->firstItem() }}–{{ $acciones->lastItem() }} de {{ $acciones->total() }} iniciativas
                    </p>
                    @if ($acciones->hasPages())
                        <div class="mt-5">{{ $acciones->withQueryString()->links() }}</div>
                    @endif
                @endif
            </section>
        </div>
    </div>
</x-layouts.app>
