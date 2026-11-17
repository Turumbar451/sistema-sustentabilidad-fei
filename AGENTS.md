# Contexto del proyecto

## Identidad

Proyecto: Sistema web para la gestión de iniciativas de sustentabilidad en la FEI.

Es un proyecto de tesis de Ingeniería en Sistemas.

## Tecnologías

- PHP y Laravel 13.
- Arquitectura MVC de Laravel.
- Eloquent ORM para persistencia.
- Blade para las vistas.
- Tailwind CSS para estilos.
- Base de datos relacional existente.

## Objetivo

Permitir la gestión de iniciativas de sustentabilidad de la FEI.

## Casos de uso prioritarios

- CU-01: Autenticación institucional.
- CU-02: Registro de propuestas.
- CU-03: Autorización de propuestas.
- CU-04: Catálogo público.

## Reglas de desarrollo

1. Revisar el código existente antes de modificarlo.
2. Respetar la arquitectura MVC de Laravel.
3. Reutilizar modelos, relaciones y migraciones existentes.
4. No inventar tablas, columnas o relaciones.
5. No introducir DAO ni patrones adicionales sin justificarlo y solicitar aprobación.
6. Priorizar las convenciones oficiales de Laravel.
7. Evitar dependencias nuevas sin autorización.
8. No modificar archivos .env ni exponer credenciales.
9. No ejecutar migraciones destructivas sin aprobación.
10. No realizar commits automáticamente.

## Forma de colaboración

El desarrollador está aprendiendo Laravel.

Antes de implementar cambios importantes:

- Explicar qué archivos se modificarán.
- Explicar por qué son necesarios.
- Solicitar aprobación cuando cambie la arquitectura.

Después de cada implementación:

- Resumir los archivos modificados.
- Explicar las decisiones técnicas.
- Indicar cómo verificar el funcionamiento.
- Señalar los errores o pruebas pendientes.

## Metodología

El desarrollo sigue Crystal Clear.
Priorizar funcionalidades pequeñas y verificables.
