{{--
    resources/views/components/catalogo-card.blade.php
    Componente anónimo: tarjeta del catálogo público.

    Uso:
      <x-catalogo-card
          titulo="Jornada de recuperación verde"
          descripcion="..."
          categoria="Áreas verdes"
          tema="green"         (green | amber | blue)
          icono="hoja"         (hoja | reciclaje | agua | energia | comunidad)
          fecha="22 NOV 2026"
          modalidad="Presencial"
          url="/catalogo/1"
      />
--}}

@props([
    'titulo' => '',
    'descripcion' => '',
    'categoria' => null,
    'tema' => 'green',
    'icono' => 'hoja',
    'fecha' => null,
    'modalidad' => null,
    'url' => null,
])

@php
    // Cabecera pastel según el tema de la iniciativa
    $temas = [
        'green' => ['cabecera' => 'bg-[#DFEDE2]', 'acento' => 'text-[#2A7B56]', 'anillo' => 'ring-[#DFEDE2]'],
        'amber' => ['cabecera' => 'bg-[#F5E9D9]', 'acento' => 'text-[#B4762E]', 'anillo' => 'ring-[#F5E9D9]'],
        'blue'  => ['cabecera' => 'bg-[#E1EEF2]', 'acento' => 'text-[#3D7D91]', 'anillo' => 'ring-[#E1EEF2]'],
    ];

    $estilo = $temas[$tema] ?? $temas['green'];
    $enlace = $url ?: '#';
    $idTitulo = 'card-titulo-'.\Illuminate\Support\Str::slug($titulo.'-'.$fecha);
@endphp

<article
    {{ $attributes->merge([
        'class' => 'group flex h-full flex-col overflow-hidden rounded-xl border border-[#E0E7E1] bg-white transition duration-200 hover:-translate-y-0.5 hover:shadow-lg focus-within:ring-2 focus-within:ring-offset-2 '.$estilo['anillo'],
    ]) }}
    aria-labelledby="{{ $idTitulo }}"
>
    {{-- Cabecera con color pastel dinámico + ícono en círculo blanco --}}
    <div class="flex h-32 shrink-0 items-center justify-start px-5 {{ $estilo['cabecera'] }}">
        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-sm">
            @switch($icono)
                @case('reciclaje')
                    <svg class="h-6 w-6 {{ $estilo['acento'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20.5 12a8.5 8.5 0 1 1-2.49-6.01"/>
                        <path d="M20.5 4.5V10h-5.5"/>
                    </svg>
                    @break

                @case('agua')
                    <svg class="h-6 w-6 {{ $estilo['acento'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 3.2c0 0 6 6.1 6 9.9a6 6 0 0 1-12 0c0-3.8 6-9.9 6-9.9Z"/>
                        <path d="M9.2 13.8a2.9 2.9 0 0 0 2.8 2.9"/>
                    </svg>
                    @break

                @case('energia')
                    <svg class="h-6 w-6 {{ $estilo['acento'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M13 2 4.5 13.5H10l-1 8.5 8.5-11.5H12l1-8.5Z"/>
                    </svg>
                    @break

                @case('comunidad')
                    <svg class="h-6 w-6 {{ $estilo['acento'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="9" cy="8" r="3.4"/>
                        <path d="M2.8 20v-1.6A4.4 4.4 0 0 1 7.2 14h3.6a4.4 4.4 0 0 1 4.4 4.4V20"/>
                        <path d="M16.4 5.2a3.2 3.2 0 0 1 0 6.1"/>
                        <path d="M18 14.2a4.2 4.2 0 0 1 3.3 4.1V20"/>
                    </svg>
                    @break

                @default
                    {{-- Hoja ecológica (por defecto) --}}
                    <svg class="h-6 w-6 {{ $estilo['acento'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4.5 19.5C3.4 12.6 8.2 5.2 20 4c1.1 8.6-4.3 15.5-13.3 15.5H4.5Z"/>
                        <path d="M4.5 19.5c1.6-5.5 5.2-9.2 10.4-11.3"/>
                    </svg>
            @endswitch
        </span>
    </div>

    {{-- Cuerpo blanco --}}
    <div class="flex flex-1 flex-col p-5">
        @if ($categoria)
            <span class="inline-flex w-fit items-center rounded-full bg-gray-100 px-3.5 py-1 text-[12px] font-semibold text-[#2A7B56]">
                {{ $categoria }}
            </span>
        @endif

        <h3 id="{{ $idTitulo }}" class="mt-3 text-[17px] font-bold leading-snug text-[#16382B]">
            <a href="{{ $enlace }}" class="rounded transition hover:text-[#2A7B56] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                {{ $titulo }}
            </a>
        </h3>

        @if ($descripcion)
            <p class="mt-2 line-clamp-3 text-[11px] leading-relaxed text-[#68766F]">
                {{ $descripcion }}
            </p>
        @endif

        {{-- Metadatos --}}
        @if ($fecha || $modalidad)
            <dl class="mt-auto grid grid-cols-2 gap-4 pt-6">
                @if ($fecha)
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-[0.7px] text-[#68766F]">Fecha</dt>
                        <dd class="mt-1 text-[12px] font-semibold text-[#16382B]">{{ $fecha }}</dd>
                    </div>
                @endif

                @if ($modalidad)
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-[0.4px] text-[#68766F]">Modalidad</dt>
                        <dd class="mt-1 text-[12px] font-semibold uppercase text-[#16382B]">{{ $modalidad }}</dd>
                    </div>
                @endif
            </dl>
        @endif

        {{-- Divisoria + acción --}}
        <div class="mt-5 border-t border-[#E0E7E1] pt-4">
            <a href="{{ $enlace }}"
               class="flex items-center justify-between rounded text-[13px] font-bold text-[#2A7B56] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                <span>Consultar detalle</span>
                <span aria-hidden="true" class="transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5">↗</span>
            </a>
        </div>
    </div>
</article>
