
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'FEI Sustentabilidad')</title>

    {{-- Carga de CSS y JavaScript mediante Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="min-h-screen antialiased">

    {{-- Aquí se inyecta el contenido de cada vista --}}
    @yield('content')

</body>

</html>
