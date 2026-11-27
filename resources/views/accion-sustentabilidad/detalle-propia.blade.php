<x-layouts.app :title="$accion->nombre . ' | Mis propuestas | FEI Sustentabilidad'">
    <div class="mx-auto flex max-w-5xl flex-col gap-6">
        <a href="{{ route('acciones.mis-propuestas') }}" class="inline-flex self-start rounded text-sm font-semibold text-uv-blue hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-uv-blue">
            ← Volver a mis propuestas
        </a>

        <article class="overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b-4 border-uv-green bg-uv-blue px-6 py-8 text-white sm:px-8 sm:py-10">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-wider">{{ $accion->folio }}</p>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-uv-blue">{{ $accion->estadoAccion?->nombre?->etiqueta() ?? 'Sin estado' }}</span>
                </div>
                <h1 class="text-2xl font-bold leading-tight sm:text-3xl">{{ $accion->nombre }}</h1>
                <p class="text-sm text-white/90">{{ $accion->ejeTrabajo?->nombre ?? 'Sin eje asignado' }}</p>
            </div>

            <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-3">
                <div class="flex flex-col gap-8 lg:col-span-2">
                    <section class="flex flex-col gap-3" aria-labelledby="descripcion-titulo">
                        <h2 id="descripcion-titulo" class="text-lg font-bold text-ink">Descripción de la propuesta</h2>
                        <p class="whitespace-pre-line text-sm leading-relaxed text-muted">{{ $accion->descripcion ?: 'No hay una descripción disponible.' }}</p>
                    </section>

                    <section class="flex flex-col gap-3" aria-labelledby="requerimientos-titulo">
                        <h2 id="requerimientos-titulo" class="text-lg font-bold text-ink">Requerimientos</h2>
                        <p class="whitespace-pre-line text-sm leading-relaxed text-muted">{{ $accion->requerimientos ?: 'Sin requerimientos.' }}</p>
                    </section>

                    @if ($accion->evidencias->isNotEmpty())
                        <section class="flex flex-col gap-3" aria-labelledby="evidencias-titulo">
                            <h2 id="evidencias-titulo" class="text-lg font-bold text-ink">Evidencias</h2>
                            <ul class="flex flex-col gap-3 text-sm">
                                @foreach ($accion->evidencias as $evidencia)
                                    <li>
                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($evidencia->ubicacionArchivo) }}" class="inline-flex rounded font-semibold text-uv-blue hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-uv-blue">
                                            {{ $evidencia->nombreArchivo }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </div>

                <section class="flex h-fit flex-col gap-5 rounded-xl bg-uv-blue-soft p-5" aria-labelledby="informacion-titulo">
                    <h2 id="informacion-titulo" class="text-base font-bold text-uv-blue">Información general</h2>
                    <dl class="grid gap-5 text-sm sm:grid-cols-2 lg:grid-cols-1">
                        <div>
                            <dt class="text-xs font-semibold text-muted">Solicitante</dt>
                            <dd class="mt-1 font-semibold">{{ $accion->solicitante?->nombreCompleto ?? 'No disponible' }}</dd>
                        </div>
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
                            <dt class="text-xs font-semibold text-muted">Fecha de registro</dt>
                            <dd class="mt-1 font-semibold">{{ $accion->fechaRegistroFormateada ?? 'No disponible' }}</dd>
                        </div>
                    </dl>
                </section>
            </div>
        </article>

        <section class="flex flex-col gap-6 rounded-2xl border border-line bg-white p-6 shadow-sm sm:p-8" aria-labelledby="historial-titulo">
            <h2 id="historial-titulo" class="text-lg font-bold text-ink">Historial de estados</h2>
            @if ($accion->historialEstados->isEmpty())
                <p class="text-sm text-muted">No hay cambios de estado registrados.</p>
            @else
                <ol class="flex flex-col gap-6 border-l-2 border-uv-green pl-5">
                    @foreach ($accion->historialEstados as $historial)
                        <li class="flex flex-col gap-3">
                            <h3 class="text-sm font-bold text-uv-blue">{{ $historial->estadoAccion?->nombre?->etiqueta() ?? 'Sin estado' }}</h3>
                            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                                <div>
                                    <dt class="text-xs font-semibold text-muted">Fecha</dt>
                                    <dd class="mt-1">{{ $historial->fechaCambio?->format('d/m/Y H:i') ?? 'No disponible' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-muted">Responsable</dt>
                                    <dd class="mt-1">{{ $historial->usuarioResponsable?->nombreCompleto ?? 'No disponible' }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-xs font-semibold text-muted">Motivo / observaciones</dt>
                                    <dd class="mt-1 whitespace-pre-line leading-relaxed">{{ $historial->motivo ?: 'Sin observaciones.' }}</dd>
                                </div>
                            </dl>
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>
    </div>
</x-layouts.app>
