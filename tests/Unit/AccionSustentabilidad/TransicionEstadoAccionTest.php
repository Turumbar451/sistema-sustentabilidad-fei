<?php

declare(strict_types=1);

use App\Enums\EstadoAccionEnum as Estado;
use App\Enums\RolSistemaEnum as Rol;
use App\Support\AccionSustentabilidad\TransicionEstadoAccion;

it('permite registrar y reenviar propuestas sin exigir motivo', function (
    ?Estado $origen,
    Estado $destino,
    Rol $rol,
): void {
    expect(fn () => TransicionEstadoAccion::validar($origen, $destino, $rol))
        ->not->toThrow(DomainException::class);
})->with([
    'registro directo de coordinadora' => [null, Estado::Aprobada, Rol::CoordinadoraSustentabilidad],
    'registro de estudiante' => [null, Estado::PendienteDeRevision, Rol::Estudiante],
    'registro de maestro' => [null, Estado::PendienteDeRevision, Rol::Maestro],
    'reenvío de estudiante' => [Estado::CorreccionesSolicitadas, Estado::PendienteDeRevision, Rol::Estudiante],
    'reenvío de maestro' => [Estado::CorreccionesSolicitadas, Estado::PendienteDeRevision, Rol::Maestro],
]);

it('permite a la coordinadora dictaminar con un motivo justificativo', function (
    Estado $destino,
    string $motivo,
): void {
    expect(fn () => TransicionEstadoAccion::validar(
        Estado::PendienteDeRevision,
        $destino,
        Rol::CoordinadoraSustentabilidad,
        $motivo,
    ))->not->toThrow(DomainException::class);
})->with([
    'aprobar' => Estado::Aprobada,
    'solicitar correcciones' => Estado::CorreccionesSolicitadas,
    'rechazar' => Estado::Rechazada,
])->with([
    'comentario justificativo' => 'Se revisó la propuesta y se documentó el dictamen.',
    'comentario con espacios alrededor' => '  Dictamen justificado.  ',
    'cero es texto no vacío' => '0',
]);

it('rechaza los tres dictámenes cuando el motivo está ausente o en blanco', function (
    Estado $destino,
    ?string $motivo,
): void {
    expect(fn () => TransicionEstadoAccion::validar(
        Estado::PendienteDeRevision,
        $destino,
        Rol::CoordinadoraSustentabilidad,
        $motivo,
    ))->toThrow(DomainException::class, 'El motivo es obligatorio para dictaminar una propuesta.');
})->with([
    'aprobar' => Estado::Aprobada,
    'solicitar correcciones' => Estado::CorreccionesSolicitadas,
    'rechazar' => Estado::Rechazada,
])->with([
    'nulo' => null,
    'vacío' => '',
    'espacios' => '   ',
    'saltos de línea y tabulaciones' => "\n\r\t",
]);

it('impide dictaminar a los roles sin permiso aunque proporcionen un motivo', function (
    Estado $destino,
    Rol $rol,
): void {
    expect(fn () => TransicionEstadoAccion::validar(
        Estado::PendienteDeRevision,
        $destino,
        $rol,
        'Motivo presente que no concede permisos.',
    ))->toThrow(DomainException::class, 'El rol no puede ejecutar esta transición de estado.');
})->with([
    'aprobar' => Estado::Aprobada,
    'solicitar correcciones' => Estado::CorreccionesSolicitadas,
    'rechazar' => Estado::Rechazada,
])->with([
    'estudiante' => Rol::Estudiante,
    'maestro' => Rol::Maestro,
    'director' => Rol::Director,
]);

it('impide registrar o reenviar con un rol no autorizado para el movimiento', function (
    ?Estado $origen,
    Estado $destino,
    Rol $rol,
): void {
    expect(fn () => TransicionEstadoAccion::validar(
        $origen,
        $destino,
        $rol,
        'Comentario que no concede permisos.',
    ))->toThrow(DomainException::class, 'El rol no puede ejecutar esta transición de estado.');
})->with([
    'estudiante no se aprueba al registrar' => [null, Estado::Aprobada, Rol::Estudiante],
    'maestro no se aprueba al registrar' => [null, Estado::Aprobada, Rol::Maestro],
    'director no registra aprobada' => [null, Estado::Aprobada, Rol::Director],
    'director no registra pendiente' => [null, Estado::PendienteDeRevision, Rol::Director],
    'coordinadora registra directamente aprobada' => [null, Estado::PendienteDeRevision, Rol::CoordinadoraSustentabilidad],
    'director no reenvía correcciones' => [Estado::CorreccionesSolicitadas, Estado::PendienteDeRevision, Rol::Director],
    'coordinadora no reenvía correcciones' => [Estado::CorreccionesSolicitadas, Estado::PendienteDeRevision, Rol::CoordinadoraSustentabilidad],
]);

it('rechaza repetir el estado actual aunque el rol sea coordinadora', function (Estado $estado): void {
    expect(fn () => TransicionEstadoAccion::validar(
        $estado,
        $estado,
        Rol::CoordinadoraSustentabilidad,
        'Motivo presente.',
    ))->toThrow(DomainException::class, 'La transición de estado no está permitida.');
})->with([
    'pendiente' => Estado::PendienteDeRevision,
    'correcciones' => Estado::CorreccionesSolicitadas,
    'aprobada' => Estado::Aprobada,
    'rechazada' => Estado::Rechazada,
    'en ejecución' => Estado::EnEjecucion,
    'finalizada' => Estado::Finalizada,
]);

it('rechaza saltos de estado y reaperturas sin una regla explícita', function (
    ?Estado $origen,
    Estado $destino,
): void {
    expect(fn () => TransicionEstadoAccion::validar(
        $origen,
        $destino,
        Rol::CoordinadoraSustentabilidad,
        'Motivo presente.',
    ))->toThrow(DomainException::class, 'La transición de estado no está permitida.');
})->with([
    'registro en correcciones' => [null, Estado::CorreccionesSolicitadas],
    'registro rechazado' => [null, Estado::Rechazada],
    'registro en ejecución' => [null, Estado::EnEjecucion],
    'registro finalizado' => [null, Estado::Finalizada],
    'pendiente a ejecución' => [Estado::PendienteDeRevision, Estado::EnEjecucion],
    'pendiente a finalizada' => [Estado::PendienteDeRevision, Estado::Finalizada],
    'correcciones a aprobada sin reenvío' => [Estado::CorreccionesSolicitadas, Estado::Aprobada],
    'correcciones a rechazada sin reenvío' => [Estado::CorreccionesSolicitadas, Estado::Rechazada],
    'correcciones a ejecución' => [Estado::CorreccionesSolicitadas, Estado::EnEjecucion],
    'correcciones a finalizada' => [Estado::CorreccionesSolicitadas, Estado::Finalizada],
    'aprobada a pendiente' => [Estado::Aprobada, Estado::PendienteDeRevision],
    'aprobada a ejecución sin regla definida' => [Estado::Aprobada, Estado::EnEjecucion],
    'aprobada a finalizada' => [Estado::Aprobada, Estado::Finalizada],
    'rechazada a pendiente' => [Estado::Rechazada, Estado::PendienteDeRevision],
    'rechazada a aprobada' => [Estado::Rechazada, Estado::Aprobada],
    'en ejecución a aprobada' => [Estado::EnEjecucion, Estado::Aprobada],
    'en ejecución a finalizada sin regla definida' => [Estado::EnEjecucion, Estado::Finalizada],
    'finalizada a pendiente' => [Estado::Finalizada, Estado::PendienteDeRevision],
    'finalizada a ejecución' => [Estado::Finalizada, Estado::EnEjecucion],
]);
