@extends('layouts.guest')

@section('title', data_get($item, 'titulo', 'Detalle de iniciativa') . ' | FEI Sustentabilidad')
@section('meta_description', 'Información de la iniciativa de sustentabilidad.')

@section('content')
    <header class="border-b border-[#E0E7E1] bg-white">
        <div class="mx-auto flex h-21.5 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('catalogo.index') }}" class="flex items-center gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A7B56] focus-visible:ring-offset-2">
                <span class="flex h-8.5 w-8.5 items-center justify-center rounded-[10px] bg-[#2A7B56]" aria-hidden="true">
                    <span class="h-4 w-4 rounded-full bg-[#DFECC6]"></span>
                </span>
                <span class="flex items-baseline gap-2">
                    <span class="text-[19px] font-extrabold tracking-tight text-[#16382B]">FEI</span>
                    <span class="hidden text-[13px] font-medium text-[#68766F] sm:inline">Sustentabilidad</span>
                </span>
            </a>

            <a href="{{ route('catalogo.index') }}" class="text-sm font-semibold text-[#2A7B56] hover:underline">
                Volver al catálogo
            </a>
        </div>
    </header>

    <main id="contenido-principal" class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <a href="{{ route('catalogo.index') }}" class="text-sm font-semibold text-[#2A7B56] hover:underline">
            ← Regresar a las iniciativas
        </a>

        <article class="mt-6 overflow-hidden rounded-2xl border border-[#E0E7E1] bg-white shadow-sm">
            <div class="bg-[#DFEDE2] px-8 py-10">
                <p class="text-xs font-bold uppercase tracking-wider text-[#2A7B56]">{{ data_get($item, 'categoria') }}</p>
                <h1 class="mt-3 text-3xl font-extrabold leading-tight text-[#16382B]">
                    {{ data_get($item, 'titulo') }}
                </h1>
            </div>

            <div class="p-8">
                <h2 class="text-sm font-bold uppercase tracking-wide text-[#68766F]">Descripción</h2>
                <p class="mt-3 leading-relaxed text-[#1B2924]">{{ data_get($item, 'descripcion') }}</p>

                <dl class="mt-8 grid gap-6 border-t border-[#E0E7E1] pt-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-[#68766F]">Fecha</dt>
                        <dd class="mt-2 font-semibold text-[#16382B]">{{ data_get($item, 'fecha') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-[#68766F]">Modalidad</dt>
                        <dd class="mt-2 font-semibold text-[#16382B]">{{ data_get($item, 'modalidad') }}</dd>
                    </div>
                </dl>

                <p class="mt-8 rounded-lg bg-[#F7F9F6] p-4 text-sm text-[#68766F]">
                    Esta ficha utiliza información demostrativa. El detalle definitivo se conectará con los datos de la iniciativa cuando se implemente la base de datos.
                </p>
            </div>
        </article>
    </main>
@endsection
