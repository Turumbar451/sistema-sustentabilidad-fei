<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\EstadoAccionEnum;
use App\Enums\FormaTrabajoEnum;
use App\Enums\RolSistemaEnum;
use App\Models\AccionSustentabilidad;
use App\Models\EjeTrabajo;
use App\Models\EstadoAccion;
use App\Models\Evidencia;
use App\Models\HistorialEstadoAccion;
use App\Models\Participacion;
use App\Models\Rol;
use App\Models\UsuarioInstitucional;
use App\Models\UsuarioRol;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CatalogosBaseSeeder::class);

        $alta = CarbonImmutable::create(2026, 1, 1, 8, 0, 0, 'UTC');
        $coordinadora = $this->crearUsuario(
            'P0001',
            'Ana Luz Polo Estrella',
            'coordinacion@fei.example.test',
            RolSistemaEnum::CoordinadoraSustentabilidad,
            $alta,
        );
        $director = $this->crearUsuario(
            'P0002',
            'Carlos Hernández López',
            'direccion@fei.example.test',
            RolSistemaEnum::Director,
            $alta,
        );

        /** @var Collection<int, UsuarioInstitucional> $maestros */
        $maestros = new Collection;

        foreach (['María García Pérez', 'Luis Martínez Díaz', 'Elena Sánchez Ruiz'] as $indice => $nombre) {
            $numero = $indice + 1;
            $maestros->push($this->crearUsuario(
                'M'.str_pad((string) $numero, 4, '0', STR_PAD_LEFT),
                $nombre,
                'maestro.'.$numero.'@fei.example.test',
                RolSistemaEnum::Maestro,
                $alta,
            ));
        }

        /** @var Collection<int, UsuarioInstitucional> $estudiantes */
        $estudiantes = new Collection;

        foreach ([
            'Sofía López Ramírez',
            'Diego Pérez Torres',
            'Valeria Flores Cruz',
            'Daniel Morales Vega',
            'Camila Ortiz Reyes',
            'Mateo Castillo Silva',
            'Isabella Rivera Ríos',
            'Santiago Jiménez León',
        ] as $indice => $nombre) {
            $numero = $indice + 1;
            $estudiantes->push($this->crearUsuario(
                'S2026'.str_pad((string) $numero, 4, '0', STR_PAD_LEFT),
                $nombre,
                'estudiante.'.$numero.'@fei.example.test',
                RolSistemaEnum::Estudiante,
                $alta,
            ));
        }

        // El mismo maestro conserva una asignación cerrada y otra vigente.
        UsuarioRol::create([
            'idUsuario' => $maestros[0]->getKey(),
            'idRol' => Rol::idPara(RolSistemaEnum::Maestro),
            'fechaAsignacion' => $alta->subYear(),
            'fechaFin' => $alta->subSecond(),
        ]);

        foreach ($this->definicionesAcciones() as $indice => $definicion) {
            $registro = CarbonImmutable::create(2026, 1, 5, 9, 0, 0, 'UTC')->addDays($indice);
            $inicio = $registro->addDays(14)->startOfDay();
            $termino = $inicio->addDays(2);
            $solicitante = $maestros[$indice % $maestros->count()];
            $folio = 'FEI-2026-'.str_pad((string) ($indice + 1), 3, '0', STR_PAD_LEFT);

            $accion = AccionSustentabilidad::factory()->pendienteDeRevision()->create([
                'folio' => $folio,
                'nombre' => $definicion['nombre'],
                'descripcion' => 'Acción de ejemplo de la FEI: '.$definicion['nombre'].'.',
                'modalidad' => ['Presencial', 'Virtual', 'Híbrida'][$indice % 3],
                'formaTrabajo' => $indice % 2 === 0
                    ? FormaTrabajoEnum::EquipoConformado
                    : FormaTrabajoEnum::Individual,
                'fechaRegistro' => $registro,
                'fechaInicio' => $inicio->toDateString(),
                'fechaTermino' => $termino->toDateString(),
                'requerimientos' => 'Coordinación del espacio y materiales reutilizables.',
                'idEjeTrabajo' => EjeTrabajo::idPara(
                    CatalogosBaseSeeder::EJES[$indice % count(CatalogosBaseSeeder::EJES)],
                ),
                'idUsuarioSolicitante' => $solicitante->getKey(),
            ]);

            HistorialEstadoAccion::factory()->create([
                'idAccion' => $accion->getKey(),
                'idEstadoAccion' => EstadoAccion::idPara(EstadoAccionEnum::PendienteDeRevision),
                'idUsuarioResponsable' => $solicitante->getKey(),
                'fechaCambio' => $registro,
                'motivo' => 'Se registra la propuesta para revisión.',
            ]);

            $estado = $definicion['estado'];

            if ($indice === 0 || $estado === EstadoAccionEnum::CorreccionesSolicitadas) {
                $this->cambiarEstado(
                    $accion,
                    EstadoAccionEnum::CorreccionesSolicitadas,
                    $coordinadora,
                    $registro->addDay(),
                    'Precisar los materiales y el número de participantes.',
                );
            }

            if ($estado->esVisibleEnCatalogo()) {
                $this->cambiarEstado(
                    $accion,
                    EstadoAccionEnum::Aprobada,
                    $coordinadora,
                    $registro->addDays(3),
                    'Se autoriza la propuesta con sus requerimientos completos.',
                );

                if ($estado !== EstadoAccionEnum::Aprobada) {
                    $this->cambiarEstado(
                        $accion,
                        EstadoAccionEnum::EnEjecucion,
                        $solicitante,
                        $inicio,
                        'Comienza la ejecución de la acción.',
                    );
                }

                if ($estado === EstadoAccionEnum::Finalizada) {
                    $this->cambiarEstado(
                        $accion,
                        EstadoAccionEnum::Finalizada,
                        $coordinadora,
                        $termino->setHour(18),
                        'Se concluye la acción y se registran sus resultados.',
                    );
                }

                for ($participante = 0; $participante < 3; $participante++) {
                    Participacion::factory()->create([
                        'idAccion' => $accion->getKey(),
                        'idUsuario' => $estudiantes[($indice + $participante) % $estudiantes->count()]->getKey(),
                        'fechaInscripcion' => $registro->addDays(3)->setHour(11),
                    ]);
                }
            } elseif ($estado === EstadoAccionEnum::Rechazada) {
                $this->cambiarEstado(
                    $accion,
                    EstadoAccionEnum::Rechazada,
                    $director,
                    $registro->addDays(2),
                    'Los requerimientos exceden los recursos disponibles.',
                );
            }

            if ($estado === EstadoAccionEnum::Finalizada) {
                foreach (['jpg' => 'image/jpeg', 'pdf' => 'application/pdf'] as $extension => $tipo) {
                    $archivo = strtolower($folio).'-resultado.'.$extension;
                    Evidencia::factory()->create([
                        'nombreArchivo' => $archivo,
                        'ubicacionArchivo' => 'demo/evidencias/'.$archivo,
                        'tipoArchivo' => $tipo,
                        'fechaCarga' => $termino->addDay()->setHour(10),
                        'idAccion' => $accion->getKey(),
                        'idUsuarioCarga' => $solicitante->getKey(),
                    ]);
                }
            }
        }
    }

    private function crearUsuario(
        string $identificador,
        string $nombre,
        string $correo,
        RolSistemaEnum $rol,
        CarbonImmutable $alta,
    ): UsuarioInstitucional {
        $usuario = UsuarioInstitucional::query()->updateOrCreate([
            'identificadorInstitucional' => $identificador,
        ], [
            'nombreCompleto' => $nombre,
            'correoInstitucional' => $correo,
            'telefonoContacto' => null,
        ]);
        $usuario->asignarRol($rol, $alta);

        return $usuario;
    }

    private function cambiarEstado(
        AccionSustentabilidad $accion,
        EstadoAccionEnum $estado,
        UsuarioInstitucional $responsable,
        CarbonImmutable $fecha,
        string $motivo,
    ): void {
        Date::withTestNow($fecha, static function () use ($accion, $estado, $responsable, $motivo): void {
            $accion->registrarCambioEstado(
                EstadoAccion::idPara($estado),
                (int) $responsable->getKey(),
                $motivo,
            );
        });
    }

    /** @return list<array{nombre: string, estado: EstadoAccionEnum}> */
    private function definicionesAcciones(): array
    {
        return [
            ['nombre' => 'Reforestación participativa de la FEI', 'estado' => EstadoAccionEnum::Aprobada],
            ['nombre' => 'Jornada de bienestar y movilidad activa', 'estado' => EstadoAccionEnum::Aprobada],
            ['nombre' => 'Separación responsable de residuos', 'estado' => EstadoAccionEnum::Aprobada],
            ['nombre' => 'Detección y reporte de fugas de agua', 'estado' => EstadoAccionEnum::Aprobada],
            ['nombre' => 'Diagnóstico de consumo eléctrico', 'estado' => EstadoAccionEnum::EnEjecucion],
            ['nombre' => 'Reducción de impresiones administrativas', 'estado' => EstadoAccionEnum::EnEjecucion],
            ['nombre' => 'Mantenimiento de jardines universitarios', 'estado' => EstadoAccionEnum::Finalizada],
            ['nombre' => 'Taller de alimentación saludable', 'estado' => EstadoAccionEnum::Finalizada],
            ['nombre' => 'Acopio de residuos electrónicos', 'estado' => EstadoAccionEnum::Finalizada],
            ['nombre' => 'Campaña de ahorro de agua', 'estado' => EstadoAccionEnum::PendienteDeRevision],
            ['nombre' => 'Monitoreo de iluminación eficiente', 'estado' => EstadoAccionEnum::PendienteDeRevision],
            ['nombre' => 'Inventario de equipos reutilizables', 'estado' => EstadoAccionEnum::CorreccionesSolicitadas],
            ['nombre' => 'Huerto universitario con indicadores', 'estado' => EstadoAccionEnum::CorreccionesSolicitadas],
            ['nombre' => 'Encuentro de ecosalud con recursos externos', 'estado' => EstadoAccionEnum::Rechazada],
            ['nombre' => 'Feria de intercambio de materiales', 'estado' => EstadoAccionEnum::Aprobada],
        ];
    }
}
