{{--
    resources/views/accion-sustentabilidad/registrar.blade.php
    CU-02 · Registrar propuesta de sustentabilidad
    Laravel 13 · Blade · Tailwind CSS v4

    Requiere:
      · <x-layouts.app> (resources/views/components/layouts/app.blade.php)
      · Ruta 'acciones.registrar' para el POST del formulario
      · $ejesTrabajo: colección de ejes de trabajo de la base de datos.
--}}
<x-layouts.app title="Registro de propuesta">

    <div class="mx-auto w-full max-w-7xl">

        {{-- ======================= HEADER DE PÁGINA ======================= --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-[27px]">
                    Nueva propuesta
                </h1>
                <p class="mt-1.5 text-sm text-gray-600">
                    Completa la información para registrar tu iniciativa.
                </p>
            </div>

            <span class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full border border-gray-200 bg-gray-100 px-3.5 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-gray-600">
                <span class="h-1.5 w-1.5 rounded-full bg-uv-blue" aria-hidden="true"></span>
                Nueva propuesta
            </span>
        </div>

        {{-- ========================== INDICADOR DE PASOS ========================== --}}
        <div class="mt-6 flex flex-col gap-4 rounded-xl border border-gray-200 bg-white px-4 py-4 sm:flex-row sm:items-center sm:gap-5 sm:px-5">
            <div class="flex items-center gap-3 sm:flex-1">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-uv-green text-[12px] font-bold text-white" aria-hidden="true">
                    1
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-[13px] font-semibold text-gray-900">Datos de la iniciativa</span>
                    <span class="block truncate text-[11px] text-gray-500">Completa los campos requeridos</span>
                </span>
            </div>

            <span class="hidden h-0.5 flex-1 rounded-full bg-gray-200 sm:block" aria-hidden="true"></span>

            <div class="flex items-center gap-3 sm:flex-1">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-[12px] font-bold text-gray-500" aria-hidden="true">
                    2
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-[13px] font-semibold text-gray-600">Registro de la iniciativa</span>
                    <span class="block truncate text-[11px] text-gray-500">Las propuestas de la Coordinadora se aprueban al registrarse</span>
                </span>
            </div>
        </div>

        {{-- ===================== CONTENIDO EN 2 COLUMNAS ===================== --}}
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- ---------------------- COLUMNA IZQUIERDA ---------------------- --}}
            <div class="lg:col-span-2">
                <form method="POST"
                      action="{{ route('acciones.registrar') }}"
                      enctype="multipart/form-data"
                      class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-7">

                    @csrf

                    @if ($errors->any())
                        <div role="alert" class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <p class="font-semibold">Revisa los campos indicados:</p>
                            <ul class="mt-2 list-inside list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Información general</h2>
                        <p class="mt-1 text-xs text-gray-500">
                            Describe la acción con claridad para facilitar su evaluación.
                        </p>
                    </div>

                    <div class="mt-6 space-y-5">

                        {{-- Nombre --}}
                        <div>
                            <label for="nombre" class="mb-1.5 block text-[13px] font-semibold text-gray-700">
                                Nombre de la iniciativa
                                <span class="text-red-600" aria-hidden="true">*</span>
                                <span class="sr-only">(obligatorio)</span>
                            </label>
                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                required
                                maxlength="200"
                                autocomplete="off"
                                placeholder="Ej. Jornada de recuperación de áreas verdes"
                                @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-xs transition placeholder:text-gray-400 focus:border-uv-green focus:outline-none focus:ring-2 focus:ring-[#28AD56]/25"
                            >
                            @error('nombre')
                                <p id="nombre-error" class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div>
                            <label for="descripcion" class="mb-1.5 block text-[13px] font-semibold text-gray-700">
                                Descripción
                                <span class="text-red-600" aria-hidden="true">*</span>
                                <span class="sr-only">(obligatorio)</span>
                            </label>
                            <textarea
                                id="descripcion"
                                name="descripcion"
                                rows="4"
                                required
                                maxlength="1200"
                                aria-describedby="descripcion-ayuda @error('descripcion') descripcion-error @enderror"
                                @error('descripcion') aria-invalid="true" @enderror
                                placeholder="¿Cuál es el objetivo y qué actividades se realizarán? Incluye el beneficio esperado para la comunidad."
                                class="w-full resize-y rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm leading-relaxed text-gray-900 shadow-xs transition placeholder:text-gray-400 focus:border-uv-green focus:outline-none focus:ring-2 focus:ring-[#28AD56]/25"
                            >{{ old('descripcion') }}</textarea>
                            <p id="descripcion-ayuda" class="mt-1.5 text-[11px] text-gray-500">
                                Menciona el objetivo, las actividades y el beneficio esperado.
                            </p>
                            @error('descripcion')
                                <p id="descripcion-error" class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Eje de trabajo + Modalidad --}}
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="idEjeTrabajo" class="mb-1.5 block text-[13px] font-semibold text-gray-700">
                                    Eje de trabajo
                                    <span class="text-red-600" aria-hidden="true">*</span>
                                    <span class="sr-only">(obligatorio)</span>
                                </label>
                                <div class="relative">
                                    <select
                                        id="idEjeTrabajo"
                                        name="idEjeTrabajo"
                                        required
                                        @error('idEjeTrabajo') aria-invalid="true" aria-describedby="eje-error" @enderror
                                        class="w-full appearance-none rounded-lg border border-gray-300 bg-white py-2.5 pl-3.5 pr-10 text-sm text-gray-900 shadow-xs transition focus:border-uv-green focus:outline-none focus:ring-2 focus:ring-[#28AD56]/25"
                                    >
                                        <option value="" disabled @selected(old('idEjeTrabajo') === null)>Seleccionar eje</option>
                                        @foreach ($ejesTrabajo as $eje)
                                            <option value="{{ $eje->getKey() }}"
                                                    @selected((string) old('idEjeTrabajo') === (string) $eje->getKey())>
                                                {{ $eje->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="m19.5 8.25-7.5 7.5-7.5-7.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                @error('idEjeTrabajo')
                                    <p id="eje-error" class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="modalidad" class="mb-1.5 block text-[13px] font-semibold text-gray-700">
                                    Modalidad
                                    <span class="text-red-600" aria-hidden="true">*</span>
                                    <span class="sr-only">(obligatorio)</span>
                                </label>
                                <div class="relative">
                                    <select
                                        id="modalidad"
                                        name="modalidad"
                                        required
                                        @error('modalidad') aria-invalid="true" aria-describedby="modalidad-error" @enderror
                                        class="w-full appearance-none rounded-lg border border-gray-300 bg-white py-2.5 pl-3.5 pr-10 text-sm text-gray-900 shadow-xs transition focus:border-uv-green focus:outline-none focus:ring-2 focus:ring-[#28AD56]/25"
                                    >
                                        <option value="" disabled @selected(old('modalidad') === null)>Seleccionar modalidad</option>
                                        <option value="Presencial" @selected(old('modalidad') === 'Presencial')>Presencial</option>
                                        <option value="Virtual" @selected(old('modalidad') === 'Virtual')>Virtual</option>
                                        <option value="Híbrida" @selected(old('modalidad') === 'Híbrida')>Híbrida</option>
                                    </select>
                                    <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="m19.5 8.25-7.5 7.5-7.5-7.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                @error('modalidad')
                                    <p id="modalidad-error" class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="formaTrabajo" class="mb-1.5 block text-[13px] font-semibold text-gray-700">
                                Forma de trabajo
                                <span class="text-red-600" aria-hidden="true">*</span>
                                <span class="sr-only">(obligatorio)</span>
                            </label>
                            <select id="formaTrabajo" name="formaTrabajo" required
                                    @error('formaTrabajo') aria-invalid="true" aria-describedby="formaTrabajo-error" @enderror
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-xs focus:border-uv-green focus:outline-none focus:ring-2 focus:ring-[#28AD56]/25">
                                <option value="" disabled @selected(old('formaTrabajo') === null)>Seleccionar forma de trabajo</option>
                                @foreach (\App\Enums\FormaTrabajoEnum::cases() as $formaTrabajo)
                                    <option value="{{ $formaTrabajo->value }}" @selected(old('formaTrabajo') === $formaTrabajo->value)>{{ $formaTrabajo->value }}</option>
                                @endforeach
                            </select>
                            @error('formaTrabajo')
                                <p id="formaTrabajo-error" class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Fechas --}}
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="fechaInicio" class="mb-1.5 block text-[13px] font-semibold text-gray-700">
                                    Fecha de inicio
                                    <span class="text-red-600" aria-hidden="true">*</span>
                                    <span class="sr-only">(obligatorio)</span>
                                </label>
                                <input
                                    type="date"
                                    id="fechaInicio"
                                    name="fechaInicio"
                                    value="{{ old('fechaInicio') }}"
                                    required
                                    @error('fechaInicio') aria-invalid="true" aria-describedby="fechaInicio-error" @enderror
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-xs transition focus:border-uv-green focus:outline-none focus:ring-2 focus:ring-[#28AD56]/25"
                                >
                                @error('fechaInicio')
                                    <p id="fechaInicio-error" class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="fechaTermino" class="mb-1.5 block text-[13px] font-semibold text-gray-700">
                                    Fecha de término
                                    <span class="text-red-600" aria-hidden="true">*</span>
                                    <span class="sr-only">(obligatorio)</span>
                                </label>
                                <input
                                    type="date"
                                    id="fechaTermino"
                                    name="fechaTermino"
                                    value="{{ old('fechaTermino') }}"
                                    required
                                    aria-describedby="fechaTermino-ayuda @error('fechaTermino') fechaTermino-error @enderror"
                                    @error('fechaTermino') aria-invalid="true" @enderror
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-xs transition focus:border-uv-green focus:outline-none focus:ring-2 focus:ring-[#28AD56]/25"
                                >
                                <p id="fechaTermino-ayuda" class="mt-1.5 text-[11px] text-gray-500">
                                    Debe ser igual o posterior a la fecha de inicio.
                                </p>
                                @error('fechaTermino')
                                    <p id="fechaTermino-error" class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Requerimientos --}}
                        <div>
                            <label for="requerimientos" class="mb-1.5 block text-[13px] font-semibold text-gray-700">
                                Requerimientos
                                <span class="ml-1 text-[11px] font-normal text-gray-400">(opcional)</span>
                            </label>
                            <textarea
                                id="requerimientos"
                                name="requerimientos"
                                rows="2"
                                maxlength="600"
                                placeholder="Materiales, espacios, apoyo logístico u otros recursos…"
                                class="w-full resize-y rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm leading-relaxed text-gray-900 shadow-xs transition placeholder:text-gray-400 focus:border-uv-green focus:outline-none focus:ring-2 focus:ring-[#28AD56]/25"
                            >{{ old('requerimientos') }}</textarea>
                            @error('requerimientos')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Evidencias --}}
                        <div>
                            <span class="mb-1.5 block text-[13px] font-semibold text-gray-700">
                                Evidencias
                                <span class="ml-1 text-[11px] font-normal text-gray-400">(opcional)</span>
                            </span>

                            <label for="evidencias"
                                   class="group flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/70 px-6 py-8 text-center transition hover:border-uv-green hover:bg-uv-green/5 focus-within:border-uv-green focus-within:bg-uv-green/5 focus-within:ring-2 focus-within:ring-[#28AD56]/25">
                                <svg class="h-8 w-8 text-gray-400 transition group-hover:text-uv-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                    <path d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                                <span class="text-sm font-medium text-gray-700">
                                    Arrastra tus archivos aquí o
                                    <span class="font-semibold text-[#1B7A40] underline underline-offset-2">selecciónalos</span>
                                </span>
                                <span class="text-[11px] text-gray-500">
                                    Adjuntar archivos opcionales de apoyo (PDF, JPG, PNG) · Máx. 5 MB por archivo
                                </span>

                                <input
                                    type="file"
                                    id="evidencias"
                                    name="evidencias[]"
                                    multiple
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="sr-only"
                                >
                            </label>

                            <p id="evidencias-lista" class="mt-2 text-[11px] font-medium text-gray-600" aria-live="polite"></p>
                            @error('evidencias.*')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="mt-8 flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('acciones.consultar') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-xs transition hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#28AD56] focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M17.25 6.75v13.5l-6.75-4.5-6.75 4.5V6.75A2.25 2.25 0 0 1 6 4.5h9a2.25 2.25 0 0 1 2.25 2.25Z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Cancelar
                        </a>

                        <button type="submit"
                                name="accion"
                                value="enviar"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-uv-green px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#229348] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#28AD56] focus-visible:ring-offset-2">
                            Enviar propuesta
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- ---------------------- COLUMNA DERECHA ---------------------- --}}
            <aside class="space-y-6 lg:col-span-1 lg:sticky lg:top-24 lg:self-start" aria-label="Ayuda para completar el formulario">

                {{-- Tarjeta 1 --}}
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-[17px] font-bold text-gray-900">Antes de enviar</h2>

                    <ul class="mt-4 space-y-4">
                        <li class="flex gap-3">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#E8F6EC]" aria-hidden="true">
                                <svg class="h-3.5 w-3.5 text-uv-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path d="m4.5 12.75 6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[12px] font-semibold text-gray-900">Describe el propósito</p>
                                <p class="mt-0.5 text-[11px] leading-relaxed text-gray-500">Indica qué problema aborda.</p>
                            </div>
                        </li>

                        <li class="flex gap-3">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#E8F6EC]" aria-hidden="true">
                                <svg class="h-3.5 w-3.5 text-uv-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path d="m4.5 12.75 6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[12px] font-semibold text-gray-900">Revisa las fechas</p>
                                <p class="mt-0.5 text-[11px] leading-relaxed text-gray-500">Comprueba que sean correctas.</p>
                            </div>
                        </li>

                        <li class="flex gap-3">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#E8F6EC]" aria-hidden="true">
                                <svg class="h-3.5 w-3.5 text-uv-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path d="m4.5 12.75 6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[12px] font-semibold text-gray-900">Agrega recursos</p>
                                <p class="mt-0.5 text-[11px] leading-relaxed text-gray-500">Señala qué apoyo necesitas.</p>
                            </div>
                        </li>
                    </ul>
                </section>

                {{-- Tarjeta 2 --}}
                <section class="rounded-xl border border-[#D9EFE0] bg-[#EFF8F1] p-6">
                    <h2 class="text-base font-bold text-gray-900">¿Qué sucede después?</h2>

                    <ol class="mt-4 space-y-3">
                        <li class="flex gap-3">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white text-[10px] font-bold text-[#1B7A40]" aria-hidden="true">1</span>
                            <p class="text-[12px] leading-relaxed text-gray-800">El sistema valida los datos.</p>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white text-[10px] font-bold text-[#1B7A40]" aria-hidden="true">2</span>
                            <p class="text-[12px] leading-relaxed text-gray-800">La propuesta de la Coordinadora queda aprobada directamente.</p>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white text-[10px] font-bold text-[#1B7A40]" aria-hidden="true">3</span>
                            <p class="text-[12px] leading-relaxed text-gray-800">La iniciativa aparece en el catálogo público.</p>
                        </li>
                    </ol>
                </section>

                <p class="px-1 text-[11px] leading-relaxed text-gray-500">
                    Los campos marcados como requeridos deben completarse antes del envío.
                </p>
            </aside>
        </div>
    </div>

    {{-- ====== Drag & drop real + nombres de archivos seleccionados ====== --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('evidencias');
            const lista = document.getElementById('evidencias-lista');
            const zona = input?.closest('label');
            if (!input || !zona) return;

            const pintar = () => {
                const n = input.files.length;
                lista.textContent = n === 0
                    ? ''
                    : n === 1
                        ? `1 archivo seleccionado: ${input.files[0].name}`
                        : `${n} archivos seleccionados`;
            };

            input.addEventListener('change', pintar);

            ['dragenter', 'dragover'].forEach(evt =>
                zona.addEventListener(evt, e => {
                    e.preventDefault();
                    zona.classList.add('border-[#28AD56]', 'bg-[#28AD56]/5');
                })
            );

            ['dragleave', 'drop'].forEach(evt =>
                zona.addEventListener(evt, e => {
                    e.preventDefault();
                    zona.classList.remove('border-[#28AD56]', 'bg-[#28AD56]/5');
                })
            );

            zona.addEventListener('drop', e => {
                if (!e.dataTransfer?.files?.length) return;
                input.files = e.dataTransfer.files;
                pintar();
            });
        });
    </script>

</x-layouts.app>
