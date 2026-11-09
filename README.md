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
    php artisan migrate
    npm run build
    php artisan serve
    ```

    La aplicación estará disponible en `http://localhost:8000`.

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

No versionar `.env`, `.env.testing` ni otras credenciales. El archivo `.env.example` debe contener únicamente valores de referencia, nunca contraseñas o secretos reales. La aplicación utiliza una cuenta MySQL propia y no la cuenta administrativa `root`.
