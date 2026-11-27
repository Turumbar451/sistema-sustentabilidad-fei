<x-layouts.app title="Mis propuestas | FEI Sustentabilidad">
    <div class="mx-auto flex max-w-7xl flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold text-uv-blue sm:text-3xl">Mis propuestas</h1>
                <p class="text-sm text-muted">Consulta el estado y las observaciones de tus propuestas de sustentabilidad.</p>
            </div>
            <a href="{{ route('acciones.formulario-registro') }}" class="rounded-lg bg-uv-blue px-4 py-3 text-sm font-semibold text-white hover:bg-uv-blue/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-uv-blue">
                Registrar propuesta
            </a>
        </div>

        @if ($acciones->isEmpty())
            <p class="rounded-xl border border-line bg-white p-6 text-sm text-muted">Aún no has registrado propuestas.</p>
        @else
            <div class="overflow-x-auto rounded-xl border border-line bg-white shadow-sm">
                <table class="w-full text-left text-sm">
                    <caption class="sr-only">Propuestas registradas por ti y su estado actual</caption>
                    <thead class="border-b border-line bg-uv-blue-soft text-uv-blue">
                        <tr>
                            <th scope="col" class="px-5 py-4 font-semibold">Folio</th>
                            <th scope="col" class="px-5 py-4 font-semibold">Nombre</th>
                            <th scope="col" class="px-5 py-4 font-semibold">Eje</th>
                            <th scope="col" class="px-5 py-4 font-semibold">Estado</th>
                            <th scope="col" class="px-5 py-4 font-semibold">Fecha de registro</th>
                            <th scope="col" class="px-5 py-4 font-semibold">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($acciones as $accion)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-4 font-semibold">{{ $accion->folio }}</td>
                                <td class="px-5 py-4 font-medium">{{ $accion->nombre }}</td>
                                <td class="px-5 py-4 text-muted">{{ $accion->ejeTrabajo?->nombre ?? 'Sin eje asignado' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-uv-blue-soft px-3 py-1 text-xs font-semibold text-uv-blue">{{ $accion->estadoAccion?->nombre?->etiqueta() ?? 'Sin estado' }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-muted">{{ $accion->fechaRegistroFormateada ?? 'No disponible' }}</td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('acciones.mis-propuestas.detalle', $accion) }}" class="inline-flex rounded font-semibold text-uv-blue hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-uv-blue" aria-label="Ver detalle de {{ $accion->folio }}">
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{ $acciones->links() }}
    </div>
</x-layouts.app>
