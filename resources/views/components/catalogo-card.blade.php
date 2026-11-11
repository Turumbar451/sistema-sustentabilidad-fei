
@props(['item'])

@php
    // El componente recibe un arreglo preparado por el controlador.
    $id = data_get($item, 'id');
    $titulo = data_get($item, 'titulo', 'Iniciativa sin título');
    $descripcion = data_get($item, 'descripcion', '');
    $categoria = data_get($item, 'categoria', 'Sin categoría');
    $tema = data_get($item, 'color_tema', 'green');
    $icono = strtolower((string) data_get($item, 'icono', 'hoja'));
    $fecha = data_get($item, 'fechaInicio', data_get($item, 'fecha', 'Por definir'));
    $modalidad = data_get($item, 'modalidad', 'Por definir');

    $temas = [
        'green' => [
            'cabecera' => 'bg-[#DFEDE2]',
            'acento' => 'text-[#2A7B56]',
            'anillo' => 'focus-within:ring-[#2A7B56]',
        ],
        'amber' => [
            'cabecera' => 'bg-[#F5E9D9]',
            'acento' => 'text-[#B4762E]',
            'anillo' => 'focus-within:ring-[#B4762E]',
        ],
        'blue' => [
            'cabecera' => 'bg-[#E1EEF2]',
            'acento' => 'text-[#3D7D91]',
            'anillo' => 'focus-within:ring-[#3D7D91]',
        ],
    ];

    $estilo = $temas[$tema] ?? $temas['green'];
    $idTitulo = 'iniciativa-' . $id;

    // La ruta recibe el parámetro llamado "id".
    $urlDetalle = route('catalogo.show', ['id' => $id]);
@endphp

<article
    {{ $attributes->class([
        'group flex h-full min-h-[410px] flex-col overflow-hidden rounded-xl border border-[#E0E7E1] bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md',
        $estilo['anillo'],
    ]) }}
    aria-labelledby="{{ $idTitulo }}"
>
    {{-- Cabecera pastel e icono --}}
    <div class="flex h-32 shrink-0 items-center px-6 {{ $estilo['cabecera'] }}">
        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-sm" aria-hidden="true">
            @switch($icono)
                @case('reciclaje')
                @case('residuos')
                    <svg class="h-6 w-6 {{ $estilo['acento'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m7 7 2-3h6l2 3M5 10l-2 4 3 5h5M19 10l2 4-3 5h-5M8 7h8M7 7l-2 3m11-3 3 3M8 19l-1-4m9 4 1-4"/>
                    </svg>
                    @break

                @case('agua')
                    <svg class="h-6 w-6 {{ $estilo['acento'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3.2s6 6.1 6 9.9a6 6 0 0 1-12 0c0-3.8 6-9.9 6-9.9Z"/>
                        <path d="M9.2 13.8a2.9 2.9 0 0 0 2.8 2.9"/>
                    </svg>
                    @break

                @case('energia')
                    <svg class="h-6 w-6 {{ $estilo['acento'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2 4.5 13.5H10l-1 8.5 8.5-11.5H12L13 2Z"/>
                    </svg>
                    @break

                @case('comunidad')
                    <svg class="h-6 w-6 {{ $estilo['acento'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="8" r="3.4"/>
                        <path d="M2.8 20v-1.6A4.4 4.4 0 0 1 7.2 14h3.6a4.4 4.4 0 0 1 4.4 4.4V20M16.4 5.2a3.2 3.2 0 0 1 0 6.1M18 14.2a4.2 4.2 0 0 1 3.3 4.1V20"/>
                    </svg>
                    @break

                @default
                    <svg class="h-6 w-6 {{ $estilo['acento'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4.5 19.5C3.4 12.6 8.2 5.2 20 4c1.1 8.6-4.3 15.5-13.3 15.5H4.5Z"/>
                        <path d="M4.5 19.5c1.6-5.5 5.2-9.2 10.4-11.3"/>
                    </svg>
            @endswitch
        </span>
    </div>

    {{-- Cuerpo --}}
    <div class="flex flex-1 flex-col p-5">
        <span class="inline-flex w-fit max-w-full items-center truncate rounded-full bg-gray-100 px-3.5 py-1 text-xs font-semibold text-[#2A7B56]">
            {{ $categoria }}
        </span>

        <h2 id="{{ $idTitulo }}" class="mt-3 text-[17px] font-bold leading-snug text-[#16382B]">
            <a href="{{ $urlDetalle }}" class="rounded transition hover:text-[#2A7B56] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                {{ $titulo }}
            </a>
        </h2>

        @if ($descripcion !== '')
            <p class="mt-2 line-clamp-3 text-[12px] leading-relaxed text-[#68766F]">
                {{ $descripcion }}
            </p>
        @endif

        <dl class="mt-auto grid grid-cols-2 gap-4 pt-6">
            <div class="min-w-0">
                <dt class="text-[10px] font-bold uppercase tracking-[0.7px] text-[#68766F]">Fecha</dt>
                <dd class="mt-1 truncate text-xs font-semibold text-[#16382B]">{{ $fecha }}</dd>
            </div>

            <div class="min-w-0">
                <dt class="text-[10px] font-bold uppercase tracking-[0.4px] text-[#68766F]">Modalidad</dt>
                <dd class="mt-1 truncate text-xs font-semibold text-[#16382B]">{{ $modalidad }}</dd>
            </div>
        </dl>

        <div class="mt-5 border-t border-[#E0E7E1] pt-4">
            <a
                href="{{ $urlDetalle }}"
                aria-label="Consultar detalle de {{ $titulo }}"
                class="flex items-center justify-between rounded text-[13px] font-bold text-[#2A7B56] transition hover:text-[#16382B] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2"
            >
                <span>Consultar detalle</span>
                <span aria-hidden="true" class="transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5">↗</span>
            </a>
        </div>
    </div>
</article>
