
@props(['item'])


@php

    /*
    |--------------------------------------------------------------------------
    | Recuperación de atributos
    |--------------------------------------------------------------------------
    | data_get() permite trabajar tanto con arreglos como con modelos Eloquent.
    */

    $id = data_get($item, 'id');

    $titulo = data_get($item, 'titulo', 'Iniciativa sin título');

    $descripcion = data_get($item, 'descripcion', '');

    $categoria = data_get($item, 'categoria', 'Sin categoría');

    $tema = data_get($item, 'color_tema', 'green');

    $icono = strtolower((string) data_get($item, 'icono', 'hoja'));

    $fecha = data_get(
        $item,
        'fecha',
        data_get($item, 'meta_inferior', 'Por definir')
    );

    $modalidad = data_get($item, 'modalidad', 'Por definir');


    /*
    |--------------------------------------------------------------------------
    | Colores dinámicos
    |--------------------------------------------------------------------------
    | Se utilizan clases completas para que Tailwind pueda detectarlas
    | durante la compilación.
    */

    $colorCabecera = match ($tema) {

        'green' => 'bg-[#DDEBDD]',

        'amber' => 'bg-[#F5E9D7]',

        'blue' => 'bg-[#DCEBF0]',

        default => 'bg-[#DDEBDD]',

    };


    $colorIcono = $tema === 'blue'
        ? 'text-[#3D7D91]'
        : 'text-[#287A55]';


    /*
    |--------------------------------------------------------------------------
    | URL del detalle
    |--------------------------------------------------------------------------
    | Se utiliza /catalogo/{id} como URI de ejemplo.
    */

    $urlDetalle = url('/catalogo/' . $id);

@endphp



<article class="flex min-h-[420px] w-full max-w-[310px] flex-col overflow-hidden rounded-[14px] border border-[#E0E7E1] bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">


    {{-- ============================================================
         CABECERA DE LA TARJETA
    ============================================================= --}}

    <div class="relative flex h-[142px] shrink-0 items-start p-[24px] {{ $colorCabecera }}">

        {{-- Círculo blanco --}}
        <div class="flex h-[60px] w-[60px] items-center justify-center rounded-full bg-white">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-[32px] w-[32px] {{ $colorIcono }}"
                viewBox="0 0 40 40"
                fill="none"
                aria-hidden="true"
            >

                {{-- Icono de hoja --}}
                @if (in_array($icono, ['hoja', 'leaf']))

                    <path
                        d="M12 31 C6 21 15 9 31 10 C32 24 23 34 12 31 Z"
                        fill="currentColor"
                    />

                    <path
                        d="M12 31 Q20 21 29 13"
                        stroke="white"
                        stroke-width="2"
                        stroke-linecap="round"
                    />


                {{-- Icono de reciclaje --}}
                @elseif (in_array($icono, ['residuos', 'reciclaje', 'recycle']))

                    <path
                        d="M20 7 L25 15 H21 L18 11"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M25 15 H31 L34 21"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M34 21 L28 22 L30 27"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M29 29 H17 L13 23"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M13 23 L12 29 L7 26"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M8 23 L14 12 L19 12"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />


                {{-- Icono de agua --}}
                @elseif (in_array($icono, ['agua', 'water']))

                    <path
                        d="M20 5 C16 11 9 18 9 24 A11 11 0 0 0 31 24 C31 18 24 11 20 5 Z"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M15 25 C15 28 17 30 20 30"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />


                {{-- Icono genérico --}}
                @else

                    <circle
                        cx="20"
                        cy="20"
                        r="11"
                        stroke="currentColor"
                        stroke-width="2.5"
                    />

                    <path
                        d="M20 13 V21 L25 24"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                    />

                @endif

            </svg>

        </div>

    </div>



    {{-- ============================================================
         CUERPO DE LA TARJETA
    ============================================================= --}}

    <div class="flex flex-1 flex-col px-[22px] pb-6 pt-5">


        {{-- Categoría --}}
        <div class="mb-4">

            <span class="inline-flex max-w-full truncate rounded-full bg-[#EEF4EF] px-3 py-[6px] text-xs font-semibold text-[#287A55]">

                {{ $categoria }}

            </span>

        </div>



        {{-- Título --}}
        <h3 class="min-h-[44px] text-[17px] font-extrabold leading-[22px] text-[#153B35]">

            {{ $titulo }}

        </h3>



        {{-- Descripción --}}
        <p class="mt-2 max-h-[54px] min-h-[54px] overflow-hidden text-[11px] leading-[18px] text-[#68766F]">

            {{ $descripcion }}

        </p>



        {{-- ========================================================
             METADATOS: FECHA Y MODALIDAD
        ========================================================= --}}

        <div class="mt-auto grid grid-cols-2 gap-3 pt-6">


            {{-- Fecha --}}
            <div class="min-w-0">

                <p class="text-[10px] font-bold uppercase tracking-[0.7px] text-[#68766F]">
                    Fecha
                </p>

                <p class="mt-2 truncate text-xs font-semibold text-[#153B35]">
                    {{ $fecha }}
                </p>

            </div>


            {{-- Modalidad --}}
            <div class="min-w-0">

                <p class="text-[10px] font-bold uppercase tracking-[0.4px] text-[#68766F]">
                    Modalidad
                </p>

                <p class="mt-2 truncate text-xs font-semibold text-[#153B35]">
                    {{ $modalidad }}
                </p>

            </div>

        </div>



        {{-- ========================================================
             PIE DE TARJETA
        ========================================================= --}}

        <div class="mt-4 flex items-center justify-between border-t border-[#E0E7E1] pt-[26px]">

            <a
                href="{{ $urlDetalle }}"
                aria-label="Consultar detalle de {{ $titulo }}"
                class="text-[13px] font-bold text-[#287A55] transition hover:text-[#153B35]"
            >
                Consultar detalle
            </a>


            <a
                href="{{ $urlDetalle }}"
                aria-label="Ver iniciativa: {{ $titulo }}"
                class="text-[19px] font-bold text-[#287A55] transition hover:translate-x-1"
            >
                ↗
            </a>

        </div>

    </div>

</article>
