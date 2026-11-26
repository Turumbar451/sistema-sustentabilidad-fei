<x-layouts.app :title="$accion->nombre . ' | FEI Sustentabilidad'">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 sm:py-10">
        <a href="{{ route('acciones.consultar') }}" class="inline-flex rounded text-sm font-semibold text-uv-blue hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue focus-visible:ring-offset-2">
            ← Volver al catálogo
        </a>

        <article class="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
            <div class="border-b-4 border-uv-green bg-uv-blue px-6 py-8 text-white sm:px-8 sm:py-10">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-wider">{{ $accion->folio }}</p>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-uv-blue">{{ $accion->estadoAccion?->nombre?->etiqueta() ?? 'Sin estado' }}</span>
                </div>
                <h1 class="mt-4 text-2xl font-bold leading-tight sm:text-3xl">{{ $accion->nombre }}</h1>
                <p class="mt-3 text-sm text-white/90">{{ $accion->ejeTrabajo?->nombre ?? 'Sin eje asignado' }}</p>
            </div>

            <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-3">
                <div class="space-y-8 lg:col-span-2">
                    <section aria-labelledby="descripcion-titulo">
                        <h2 id="descripcion-titulo" class="text-lg font-bold text-ink">Descripción de la acción de sustentabilidad</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-muted">{{ $accion->descripcion ?: 'No hay una descripción disponible.' }}</p>
                    </section>

                    @if ($accion->requerimientos)
                        <section aria-labelledby="requerimientos-titulo">
                            <h2 id="requerimientos-titulo" class="text-lg font-bold text-ink">Requerimientos</h2>
                            <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-muted">{{ $accion->requerimientos }}</p>
                        </section>
                    @endif
                </div>

                <section class="h-fit rounded-xl bg-uv-blue-soft p-5" aria-labelledby="informacion-titulo">
                    <h2 id="informacion-titulo" class="text-base font-bold text-uv-blue">Información general</h2>
                    <dl class="mt-5 grid gap-5 text-sm sm:grid-cols-2 lg:grid-cols-1">
                        <div>
                            <dt class="text-xs font-semibold text-muted">Modalidad</dt>
                            <dd class="mt-1 font-semibold">{{ $accion->modalidad ?? 'No especificada' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-muted">Forma de trabajo</dt>
                            <dd class="mt-1 font-semibold">{{ $accion->formaTrabajo?->value ?? 'No especificada' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-muted">Fecha de inicio</dt>
                            <dd class="mt-1 font-semibold">{{ $accion->fechaInicioFormateada ?? 'Por definir' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-muted">Fecha de finalización</dt>
                            <dd class="mt-1 font-semibold">{{ $accion->fechaTerminoFormateada ?? 'Por definir' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-muted">Responsable de la acción de sustentabilidad</dt>
                            <dd class="mt-1 font-semibold">{{ $accion->solicitante?->nombreCompleto ?? 'No disponible' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-muted">Participantes inscritos</dt>
                            <dd class="mt-1 font-semibold">{{ $accion->participaciones_count }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-muted">Fecha de registro</dt>
                            <dd class="mt-1 font-semibold">{{ $accion->fechaRegistroFormateada ?? 'No disponible' }}</dd>
                        </div>
                    </dl>
                </section>
            </div>
        </article>
    </div>
</x-layouts.app>
