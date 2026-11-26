<x-layouts.app title="Acceso local | FEI Sustentabilidad">
    <section class="mx-auto flex max-w-3xl flex-col gap-6" aria-labelledby="titulo-acceso-local">
        <div class="flex flex-col gap-2">
            <h1 id="titulo-acceso-local" class="text-2xl font-bold text-uv-blue">Acceso local</h1>
            <p class="text-sm text-muted">Acceso temporal de desarrollo. Selecciona una cuenta de prueba para iniciar sesión.</p>
            <a href="{{ route('acciones.consultar') }}"
               class="self-start text-sm font-semibold text-uv-blue underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-uv-blue">
                Volver al catálogo público
            </a>
        </div>

        @error('identificadorInstitucional')
            <p role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</p>
        @enderror

        <ul class="grid gap-4 sm:grid-cols-2">
            @forelse ($cuentas as $cuenta)
                <li class="flex flex-col gap-4 rounded-lg border border-line bg-white p-5">
                    <div class="flex flex-1 flex-col gap-1">
                        <h2 class="font-semibold text-ink">{{ $cuenta->nombreCompleto }}</h2>
                        <p class="text-sm text-muted">{{ $cuenta->identificadorInstitucional }}</p>
                        @foreach ($cuenta->rolesVigentes as $rol)
                            <p class="text-sm text-uv-green-dark">{{ $rol->nombre->value }}</p>
                        @endforeach
                    </div>
                    <form method="POST" action="{{ route('acceso-local.iniciar') }}">
                        @csrf
                        <input type="hidden" name="identificadorInstitucional" value="{{ $cuenta->identificadorInstitucional }}">
                        <button type="submit" class="w-full rounded-lg bg-uv-blue px-4 py-2 text-sm font-semibold text-white hover:bg-uv-blue/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-uv-blue">
                            Iniciar sesión <span class="sr-only">como {{ $cuenta->nombreCompleto }}</span>
                        </button>
                    </form>
                </li>
            @empty
                <li class="rounded-lg border border-line bg-white p-5 text-sm text-muted sm:col-span-2">Las cuentas de prueba aún no están disponibles. Ejecuta el seeder de desarrollo.</li>
            @endforelse
        </ul>
    </section>
</x-layouts.app>
