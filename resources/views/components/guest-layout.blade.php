{{--
    resources/views/components/guest-layout.blade.php
    Layout base mínimo para el catálogo público (sin autenticación).

    NOTA: si tu proyecto ya usa Breeze / Jetstream y ya tiene un
    <x-guest-layout>, puedes borrar este archivo y la vista seguirá funcionando.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Catálogo de acciones de sustentabilidad · FEI' }}</title>
    <meta name="description" content="Consulta las iniciativas de sustentabilidad autorizadas para la comunidad universitaria y el público.">

    {{-- Tailwind: usa Vite si ya está configurado en el proyecto --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alternativa rápida solo para pruebas locales (descomenta y quita @vite):
    <script src="https://cdn.tailwindcss.com"></script>
    --}}
</head>
<body class="min-h-screen bg-[#F7F9F6] font-sans text-[#1B2924] antialiased">

    <a href="#contenido-principal"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-[#16382B] focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Saltar al contenido principal
    </a>

    {{ $slot }}

</body>
</html>
