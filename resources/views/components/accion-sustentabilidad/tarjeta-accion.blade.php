@props(['accion'])

@php
    $eje = $accion->ejeTrabajo?->nombre ?? 'Sin eje asignado';
    $estado = $accion->estadoAccion?->nombre;
    $tema = match (true) {
        str_contains(mb_strtolower($eje), 'agua'),
        str_contains(mb_strtolower($eje), 'energía'),
        str_contains(mb_strtolower($eje), 'energia') => ['cabecera' => 'bg-uv-blue-soft', 'acento' => 'text-uv-blue'],
        default => ['cabecera' => 'bg-uv-green-soft', 'acento' => 'text-uv-green-dark'],
    };
    $estiloEstado = match ($estado) {
        \App\Enums\EstadoAccionEnum::Aprobada => 'bg-uv-green-soft text-uv-green-dark',
        \App\Enums\EstadoAccionEnum::EnEjecucion => 'bg-uv-blue text-white',
        \App\Enums\EstadoAccionEnum::Finalizada => 'bg-white text-uv-blue',
        default => 'bg-white text-ink',
    };
    $idTitulo = 'accion-' . $accion->getKey();
    $urlDetalle = route('acciones.detalle', ['accionSustentabilidad' => $accion->getKey()]);
@endphp

<article
    {{ $attributes->class('group flex h-full flex-col overflow-hidden rounded-xl border border-line bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md motion-reduce:transform-none') }}
    aria-labelledby="{{ $idTitulo }}"
>
    <div class="flex min-h-28 items-center justify-between gap-3 px-5 py-6 {{ $tema['cabecera'] }}">
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white {{ $tema['acento'] }}" aria-hidden="true">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4.5 19.5C3.4 12.6 8.2 5.2 20 4c1.1 8.6-4.3 15.5-13.3 15.5H4.5Z" />
                <path d="M4.5 19.5c1.6-5.5 5.2-9.2 10.4-11.3" />
            </svg>
        </span>
        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $estiloEstado }}">
            {{ $estado?->value ?? 'Sin estado' }}
        </span>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <p class="text-xs font-semibold {{ $tema['acento'] }}">{{ $eje }}</p>
        <p class="mt-2 text-xs text-muted">{{ $accion->folio }}</p>

        <h2 id="{{ $idTitulo }}" class="mt-2 text-lg font-bold leading-snug text-ink">
            <a href="{{ $urlDetalle }}" class="rounded hover:text-uv-blue focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue focus-visible:ring-offset-2">
                {{ $accion->nombre }}
            </a>
        </h2>

        <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-muted">{{ $accion->descripcion }}</p>

        <dl class="mt-auto grid grid-cols-2 gap-4 pt-6 text-xs">
            <div>
                <dt class="font-semibold text-muted">Inicio</dt>
                <dd class="mt-1 font-semibold text-ink">{{ $accion->fechaInicioFormateada ?? 'Por definir' }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-muted">Modalidad</dt>
                <dd class="mt-1 font-semibold text-ink">{{ $accion->modalidad ?? 'Por definir' }}</dd>
            </div>
            <div class="col-span-2">
                <dt class="font-semibold text-muted">Forma de trabajo</dt>
                <dd class="mt-1 font-semibold text-ink">{{ $accion->formaTrabajo?->value ?? 'Por definir' }}</dd>
            </div>
        </dl>

        <a href="{{ $urlDetalle }}"
           aria-label="Consultar detalle de {{ $accion->nombre }}"
           class="mt-5 flex items-center justify-between rounded border-t border-line pt-4 text-sm font-semibold text-uv-blue hover:text-uv-blue-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue focus-visible:ring-offset-2">
            <span>Consultar detalle</span>
            <span aria-hidden="true">↗</span>
        </a>
    </div>
</article>
