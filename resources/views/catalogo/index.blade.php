
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Sustentabilidad | FEI</title>
</head>
<body>

    <main>
        <h1>Catálogo de acciones de sustentabilidad</h1>

        @if ($acciones->isEmpty())
            <p>
                Actualmente no hay acciones de sustentabilidad disponibles.
            </p>
        @else
            <p>Acciones disponibles: {{ $acciones->count() }}</p>
        @endif
    </main>

</body>
</html>
