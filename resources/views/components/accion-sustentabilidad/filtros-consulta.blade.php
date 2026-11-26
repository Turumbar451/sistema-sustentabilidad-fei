@props(['ejes', 'filtros', 'modalidades', 'formasTrabajo', 'estados'])

<form method="GET" action="{{ route('acciones.consultar') }}"
      {{ $attributes->class('rounded-xl border border-line bg-white p-5 sm:p-6') }}
      aria-label="Filtros de consulta">
    <h2 class="text-base font-bold text-ink">Filtrar resultados</h2>

    @if ($errors->any())
        <div role="alert" class="mt-4 rounded-lg bg-uv-blue-soft p-3 text-sm text-uv-blue-dark">
            <p class="font-semibold">Revisa los filtros de búsqueda:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-5">
        <label for="busqueda" class="block text-sm font-semibold text-ink">Buscar por nombre o folio</label>
        <input type="search" id="busqueda" name="q" value="{{ $filtros['q'] ?? '' }}"
               placeholder="Nombre o folio" maxlength="200"
               class="mt-3 w-full rounded-lg border border-line bg-white px-3 py-2 text-sm text-ink placeholder:text-muted focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue">
    </div>

    <fieldset class="mt-6 border-t border-line pt-5">
        <legend class="text-sm font-semibold text-ink">Eje de trabajo</legend>
        <div class="mt-3 space-y-3">
            @foreach ($ejes as $eje)
                <div class="flex items-start gap-3">
                    <input type="checkbox" id="eje-{{ $eje->getKey() }}" name="ejes[]" value="{{ $eje->getKey() }}"
                           @checked(in_array((int) $eje->getKey(), $filtros['idEjeTrabajo'] ?? [], true))
                           class="mt-0.5 h-4 w-4 shrink-0 accent-uv-blue focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue focus-visible:ring-offset-2">
                    <label for="eje-{{ $eje->getKey() }}" class="cursor-pointer text-xs leading-relaxed text-ink">{{ $eje->nombre }}</label>
                </div>
            @endforeach
        </div>
    </fieldset>

    <div class="mt-6 border-t border-line pt-5">
        <label for="modalidad" class="block text-sm font-semibold text-ink">Modalidad</label>
        <select id="modalidad" name="modalidad"
                class="mt-3 w-full rounded-lg border border-line bg-white px-3 py-2 text-sm text-ink focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue">
            <option value="">Todas</option>
            @foreach ($modalidades as $modalidad)
                <option value="{{ $modalidad }}" @selected(($filtros['modalidad'] ?? '') === $modalidad)>{{ $modalidad }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-5">
        <label for="formaTrabajo" class="block text-sm font-semibold text-ink">Forma de trabajo</label>
        <select id="formaTrabajo" name="formaTrabajo"
                class="mt-3 w-full rounded-lg border border-line bg-white px-3 py-2 text-sm text-ink focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue">
            <option value="">Todas</option>
            @foreach ($formasTrabajo as $formaTrabajo)
                <option value="{{ $formaTrabajo }}" @selected(($filtros['formaTrabajo'] ?? '') === $formaTrabajo)>{{ $formaTrabajo }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-5">
        <label for="estado" class="block text-sm font-semibold text-ink">Estado de la acción de sustentabilidad</label>
        <select id="estado" name="estado"
                class="mt-3 w-full rounded-lg border border-line bg-white px-3 py-2 text-sm text-ink focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue">
            <option value="">Todos</option>
            @foreach ($estados as $estado)
                <option value="{{ $estado }}" @selected(($filtros['estado'] ?? '') === $estado)>{{ \App\Enums\EstadoAccionEnum::from($estado)->etiqueta() }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-5">
        <label for="orden" class="block text-sm font-semibold text-ink">Ordenar</label>
        <select id="orden" name="orden"
                class="mt-3 w-full rounded-lg border border-line bg-white px-3 py-2 text-sm text-ink focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue">
            <option value="recientes" @selected(($filtros['orden'] ?? 'recientes') === 'recientes')>Más recientes</option>
            <option value="proximas" @selected(($filtros['orden'] ?? '') === 'proximas')>Próximas a realizarse</option>
            <option value="az" @selected(($filtros['orden'] ?? '') === 'az')>Título (A–Z)</option>
        </select>
    </div>

    <button type="submit"
            class="mt-6 w-full rounded-lg bg-uv-blue px-4 py-2.5 text-sm font-semibold text-white hover:bg-uv-blue-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue focus-visible:ring-offset-2">
        Aplicar filtros
    </button>
    <a href="{{ route('acciones.consultar') }}"
       class="mt-3 flex w-full items-center justify-center rounded-lg border border-line px-4 py-2 text-sm font-semibold text-uv-blue hover:bg-canvas focus:outline-none focus-visible:ring-2 focus-visible:ring-uv-blue focus-visible:ring-offset-2">
        Limpiar filtros
    </a>
</form>
