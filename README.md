# Sistema web para la gestión de iniciativas de sustentabilidad en la FEI

Proyecto de trabajo recepcional orientado a apoyar el registro, evaluación y consulta de iniciativas de sustentabilidad de la Facultad de Estadística e Informática (FEI).

## Tecnologías

- Laravel 13 y PHP 8.5
- MySQL 8.4
- Blade, Tailwind CSS y JavaScript
- Composer y npm
- PHPUnit para pruebas automatizadas

## Funcionalidades de la primera iteración

- **CU-01:** Autenticación institucional.
- **CU-02:** Registro de propuestas de acciones de sustentabilidad.
- **CU-03:** Evaluación y dictamen de propuestas.
- **CU-04:** Consulta del catálogo público de acciones de sustentabilidad.

## Requisitos

Se requiere PHP con la extensión `pdo_mysql`, Composer, Node.js/npm y MySQL 8.4.

## Instalación local

1. Clonar el repositorio y entrar en la carpeta del proyecto.

    ```powershell
    git clone URL_DEL_REPOSITORIO
    cd sistema-sustentabilidad-fei
    ```

2. Instalar dependencias y crear la configuración local.

    ```powershell
    composer install
    npm install
    Copy-Item .env.example .env
    php artisan key:generate
    ```

3. Configurar en `.env` la conexión MySQL (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`). Crear previamente la base de datos de desarrollo.

4. Ejecutar las migraciones y arrancar la aplicación.

    ```powershell
    php artisan migrate --seed
    npm run build
    php artisan serve
    ```

## Base de datos DI-02

El esquema contiene nueve tablas del dominio: `UsuarioInstitucional`, `Rol`, `UsuarioRol`, `EjeTrabajo`, `EstadoAccion`, `AccionSustentabilidad`, `HistorialEstadoAccion`, `Evidencia` y `Participacion`. Laravel conserva además su tabla técnica `migrations`.

Sesiones y caché usan archivos. Si ya existe una configuración local, establecer `SESSION_DRIVER=file`, `CACHE_STORE=file` y `QUEUE_CONNECTION=sync`; no se requieren las tablas `sessions`, `cache` ni `jobs`.

Para reemplazar el esquema anterior en una base **local de desarrollo**, el siguiente comando elimina todas sus tablas y carga los datos de ejemplo:

```powershell
php artisan migrate:fresh --seed
```

La autenticación de sesión utiliza `UsuarioInstitucional`, sin columnas locales de contraseña o token de recuerdo. La conexión con el proveedor institucional sigue pendiente.

## Organización MVC

El controlador coordina las peticiones y devuelve las vistas o redirecciones. Las consultas y el registro se ejecutan en la capa de modelo, sin dependencias de Blade ni respuestas HTTP.

- `app/Http/Requests/AccionSustentabilidad/ConsultarAccionesRequest.php`: valida los parámetros de consulta y normaliza sus filtros y alias.
- `app/Builders/AccionSustentabilidadBuilder.php`: reúne los filtros, el orden y las relaciones de las consultas Eloquent del catálogo.
- `app/Models/AccionSustentabilidad.php`: define atributos, relaciones y operaciones del registro; ofrece consultas completas o paginadas utilizando su Builder.
- `app/Actions/AccionSustentabilidad/RegistrarPropuesta.php`: coordina el registro, la elección del estado inicial, el historial y las evidencias dentro de una transacción; elimina los archivos nuevos si la operación falla.

El controlador recibe una instancia de `RegistrarPropuesta` mediante la inyección de dependencias de Laravel y llama a su método `ejecutar()`. La operación se trasladó desde `AccionSustentabilidad::registrarNuevaPropuesta()`. Los listados conservan `obtenerAccionesPublicadas()` y la interfaz usa `paginarAccionesPublicadas()` para cargar solo los registros de la página solicitada.

`RegistrarAccionRequest` conserva la autorización y las reglas del formulario. `EjeTrabajo::obtenerTodos()` proporciona los ejes ordenados por nombre. El controlador usa estos métodos sin construir consultas de base de datos.

## Acceso temporal de la Coordinadora

En `local`, todas las rutas web usan automáticamente a Ana Luz Polo Estrella (`P0001`) como Coordinadora de Sustentabilidad. El middleware `ForzarCoordinadoraLocal`, aplicado en `routes/web.php`, asigna la identidad en memoria para cada petición; no crea usuarios, roles ni inicios de sesión persistentes. Fuera de `local` no asigna identidades.

Abrir `/acciones/registrar`, completar el formulario y pulsar **Enviar propuesta**. Las propuestas de la Coordinadora se guardan como **Aprobada**, con o sin evidencias, y aparecen en el catálogo público. `Realizada` no es un estado del DI-02 vigente.

El DI-02 exige que el solicitante y quien carga evidencias existan en `UsuarioInstitucional`. Se reutiliza el ID existente de `P0001`, incluido en los datos de ejemplo. Si falta, es posible navegar con la identidad en memoria, pero el guardado devuelve un error de validación sin crear usuarios ni alterar las claves foráneas.

Para retirar el acceso temporal al integrar CU-01, eliminar el middleware y su aplicación al grupo de rutas. La aprobación directa de propuestas de la Coordinadora es una regla del dominio independiente de este acceso temporal.

## Pruebas automatizadas

Las pruebas utilizan una base de datos MySQL independiente de la base de desarrollo.

1. Crear previamente la base de datos de pruebas.
2. Crear `.env.testing` a partir de `.env` y cambiar `APP_ENV=testing` y `DB_DATABASE` por el nombre de la base de datos de pruebas. Mantener las credenciales locales correspondientes.
3. Ejecutar las pruebas:

    ```powershell
    php artisan test
    ```

Para ejecutar una prueba o clase específica:

```powershell
php artisan test --filter=NombreDeLaPrueba
```

## Seguridad

No versionar `.env`, `.env.testing` ni otras credenciales. El archivo `.env.example` debe contener únicamente valores de referencia, nunca contraseñas o secretos reales.
