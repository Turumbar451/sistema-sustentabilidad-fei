<x-layouts.app title="Evaluación de propuestas | FEI Sustentabilidad">
    <div class="mx-auto flex max-w-7xl flex-col gap-6">
        <header class="flex flex-col gap-2">
            <h1 class="text-2xl font-bold text-uv-blue sm:text-3xl">Evaluación de propuestas</h1>
            <p class="text-sm leading-relaxed text-muted">Consulta las propuestas pendientes de revisión y aquellas con correcciones solicitadas. Las pendientes se muestran primero, comenzando por las más antiguas.</p>
        </header>

        <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-sm" aria-label="Bandeja de propuestas recibidas">
            @if ($acciones->isEmpty())
                <div class="flex flex-col gap-2 px-6 py-12 text-center sm:px-8">
                    <h2 class="text-lg font-bold text-ink">No hay propuestas para evaluar</h2>
                    <p class="text-sm text-muted">Las propuestas pendientes de revisión o con correcciones solicitadas aparecerán aquí.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <caption class="sr-only">Propuestas recibidas para evaluación</caption>
                        <thead class="border-b border-line bg-uv-blue-soft text-xs font-semibold text-uv-blue">
                            <tr>
                                <th scope="col" class="px-5 py-4">Folio</th>
                                <th scope="col" class="px-5 py-4">Nombre</th>
                                <th scope="col" class="px-5 py-4">Solicitante</th>
                                <th scope="col" class="px-5 py-4">Eje de trabajo</th>
                                <th scope="col" class="px-5 py-4">Estado</th>
                                <th scope="col" class="px-5 py-4">Fecha de registro</th>
                                <th scope="col" class="px-5 py-4"><span class="sr-only">Revisión</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($acciones as $accion)
                                <tr class="align-top">
                                    <th scope="row" class="whitespace-nowrap px-5 py-4 font-semibold text-uv-blue">{{ $accion->folio }}</th>
                                    <td class="px-5 py-4 font-semibold">{{ $accion->nombre }}</td>
                                    <td class="px-5 py-4 text-muted">{{ $accion->solicitante?->nombreCompleto ?? 'No disponible' }}</td>
                                    <td class="px-5 py-4 text-muted">{{ $accion->ejeTrabajo?->nombre ?? 'Sin eje asignado' }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full bg-uv-green-soft px-3 py-1 text-xs font-semibold text-uv-green-dark">{{ $accion->estadoAccion?->nombre?->etiqueta() ?? 'Sin estado' }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-muted">{{ $accion->fechaRegistroFormateada ?? 'No disponible' }}</td>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('evaluacion.propuestas.detalle', $accion) }}" class="inline-flex rounded font-semibold text-uv-blue hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-uv-blue">
                                            Revisar<span class="sr-only"> propuesta {{ $accion->folio }}</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{ $acciones->links() }}
    </div>
</x-layouts.app>
