
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item['titulo'] }} | Sustentabilidad FEI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#F7F9F7] text-[#16382B]">

    <header class="border-b border-[#E0E7E1] bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-5">
            <h1 class="font-bold text-[#16382B]">
                Sustentabilidad FEI
            </h1>

            <a
                href="{{ route('catalogo.index') }}"
                class="text-sm font-semibold text-[#2A7B56] hover:underline"
            >
                Volver al catálogo
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-10">

        <a
            href="{{ route('catalogo.index') }}"
            class="mb-6 inline-flex text-sm font-semibold text-[#2A7B56] hover:underline"
        >
            ← Regresar a las iniciativas
        </a>

        <article class="overflow-hidden rounded-2xl border border-[#E0E7E1] bg-white shadow-sm">

            <div class="bg-[#DFEDE2] px-8 py-10">
                <p class="text-xs font-bold uppercase tracking-wider text-[#2A7B56]">
                    {{ $item['folio'] }}
                </p>

                <h2 class="mt-3 text-2xl font-bold leading-tight sm:text-3xl">
                    {{ $item['titulo'] }}
                </h2>

                <p class="mt-3 text-sm font-medium text-[#2A7B56]">
                    {{ $item['ejeTrabajo'] }}
                </p>
            </div>

            <div class="space-y-8 p-8">

                <section>
                    <h3 class="text-lg font-bold">Descripción de la iniciativa</h3>

                    <p class="mt-3 whitespace-pre-line leading-relaxed text-[#68766F]">
                        {{ $item['descripcion'] ?: 'No hay una descripción disponible.' }}
                    </p>
                </section>

                @if (!empty($item['requerimientos']))
                    <section>
                        <h3 class="text-lg font-bold">Requerimientos</h3>

                        <p class="mt-3 whitespace-pre-line leading-relaxed text-[#68766F]">
                            {{ $item['requerimientos'] }}
                        </p>
                    </section>
                @endif

                <section>
                    <h3 class="text-lg font-bold">Información general</h3>

                    <dl class="mt-4 grid gap-5 sm:grid-cols-2">

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-[#68766F]">
                                Estado
                            </dt>
                            <dd class="mt-1 font-semibold">
                                {{ $item['estado'] }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-[#68766F]">
                                Modalidad
                            </dt>
                            <dd class="mt-1 font-semibold">
                                {{ $item['modalidad'] ?? 'No especificada' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-[#68766F]">
                                Fecha de inicio
                            </dt>
                            <dd class="mt-1 font-semibold">
                                {{ $item['fechaInicio'] ?? 'Por definir' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-[#68766F]">
                                Fecha de finalización
                            </dt>
                            <dd class="mt-1 font-semibold">
                                {{ $item['fechaFin'] ?? 'Por definir' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-[#68766F]">
                                Fecha de registro
                            </dt>
                            <dd class="mt-1 font-semibold">
                                {{ $item['fechaRegistro'] ?? 'No disponible' }}
                            </dd>
                        </div>

                    </dl>
                </section>

            </div>
        </article>

    </main>

</body>
</html>
