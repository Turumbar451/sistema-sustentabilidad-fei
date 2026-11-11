<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\EstadoAccionEnum;
use App\Enums\EstadoDictamenEnum;
use App\Enums\ModalidadAccionEnum;
use App\Enums\RolSistemaEnum;
use App\Enums\TipoEvidenciaEnum;
use App\Models\AccionSustentabilidad;
use App\Models\Dictamen;
use App\Models\EjeTrabajo;
use App\Models\Evidencia;
use App\Models\Participacion;
use App\Models\Rol;
use App\Models\UsuarioInstitucional;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $roles = [];
            foreach (RolSistemaEnum::cases() as $r) {
                $roles[$r->value] = Rol::query()->create(['nombre' => $r, 'descripcion' => 'Rol del sistema SEMA.']);
            }
            $coord = $this->usuario('PERS-UV-0001', 'Ana Luz Polo Estrella', 'ana.luz@example.test', $roles[RolSistemaEnum::CoordinadoraSustentabilidad->value]);
            $this->usuario('PERS-UV-0002', 'Director de la FEI', 'director@example.test', $roles[RolSistemaEnum::Director->value]);
            $teachers = [];
            for ($i = 1; $i <= 3; $i++) {
                $teachers[] = $this->usuario(sprintf('PERS-UV-%04d', $i + 2), fake('es_MX')->name(), "maestro{$i}@example.test", $roles[RolSistemaEnum::Maestro->value]);
            }
            $students = [];
            for ($i = 1; $i <= 5; $i++) {
                $students[] = $this->usuario(sprintf('S-UV-202%d-%04d', 3 + ($i % 3), 100 + $i), fake('es_MX')->name(), "estudiante{$i}@example.test", $roles[RolSistemaEnum::Estudiante->value]);
            }
            $axisData = [
                ['Gestión integrada de áreas verdes', 'green'], ['Ecosalud', 'green'],
                ['Consumo y Gestión de Residuos', 'amber'], ['Gestión Integrada del Agua', 'blue'],
                ['Energía', 'amber'], ['Administración y tecnologías sustentables', 'blue'],
            ];
            $axes = [];
            foreach ($axisData as [$n,$c]) {
                $axes[] = EjeTrabajo::query()->create(['nombre' => $n, 'color_tema' => $c]);
            }
            $rows = [
                ['FEI-2026-018', 'Jornada de recuperación verde', 'Recuperación y mantenimiento de áreas verdes de la FEI.', 0, ModalidadAccionEnum::Presencial, EstadoAccionEnum::Aprobada],
                ['FEI-2026-017', 'Separación de residuos en el campus', 'Instalación y difusión de estaciones para separar residuos.', 2, ModalidadAccionEnum::Presencial, EstadoAccionEnum::Aprobada],
                ['FEI-2026-014', 'Campaña de ahorro de agua', 'Sensibilización para reducir el consumo de agua.', 3, ModalidadAccionEnum::Hibrida, EstadoAccionEnum::Realizada],
                ['FEI-2026-019', 'Huerto demostrativo universitario', 'Huerto para aprendizaje y biodiversidad.', 0, ModalidadAccionEnum::Presencial, EstadoAccionEnum::Aprobada],
                ['FEI-2026-020', 'Pausa activa y bienestar', 'Hábitos saludables en la comunidad.', 1, ModalidadAccionEnum::Presencial, EstadoAccionEnum::Realizada],
                ['FEI-2026-021', 'Diagnóstico de consumo eléctrico', 'Oportunidades para optimizar el uso de energía.', 4, ModalidadAccionEnum::Virtual, EstadoAccionEnum::Aprobada],
                ['FEI-2026-022', 'Reutilización de papel', 'Promoción del uso responsable del papel.', 2, ModalidadAccionEnum::Hibrida, EstadoAccionEnum::Aprobada],
                ['FEI-2026-023', 'Jardín para polinizadores', 'Espacio con plantas para polinizadores.', 0, ModalidadAccionEnum::Presencial, EstadoAccionEnum::Realizada],
                ['FEI-2026-024', 'Taller de consumo responsable', 'Decisiones de consumo con menor impacto.', 5, ModalidadAccionEnum::Virtual, EstadoAccionEnum::Aprobada],
                ['FEI-2026-025', 'Composta de residuos orgánicos', 'Aprovechamiento de residuos orgánicos.', 2, ModalidadAccionEnum::Presencial, EstadoAccionEnum::Cancelada],
                ['FEI-2026-026', 'Sistema de captación pluvial', 'Captación de lluvia para riego.', 3, ModalidadAccionEnum::Hibrida, EstadoAccionEnum::Propuesta],
                ['FEI-2026-027', 'Inventario de equipos eficientes', 'Identificación de oportunidades de eficiencia energética.', 4, ModalidadAccionEnum::Virtual, EstadoAccionEnum::Propuesta],
                ['FEI-2026-028', 'Punto de intercambio de libros', 'Reutilización comunitaria de materiales académicos.', 5, ModalidadAccionEnum::Presencial, EstadoAccionEnum::Propuesta],
                ['FEI-2026-029', 'Campaña de reducción de plásticos', 'Alternativas para reducir plásticos.', 2, ModalidadAccionEnum::Presencial, EstadoAccionEnum::PendientePorCorregir],
                ['FEI-2026-030', 'Instalación piloto de luminarias', 'Piloto para mejorar eficiencia de iluminación.', 4, ModalidadAccionEnum::Presencial, EstadoAccionEnum::NoAutorizada],
            ];
            foreach ($rows as $i => [$folio,$name,$desc,$axis,$mode,$state]) {
                $start = now()->addDays(10 + $i * 3)->startOfDay();
                $a = AccionSustentabilidad::query()->create([
                    'folio' => $folio, 'nombre' => $name, 'descripcion' => $desc, 'modalidad' => $mode,
                    'fechaRegistro' => now()->subDays(30 - $i), 'fechaInicio' => $start,
                    'fechaTermino' => (clone $start)->addDay(), 'requerimientos' => 'Materiales básicos y participación voluntaria.',
                    'estadoActual' => $state, 'idEjeTrabajo' => $axes[$axis]->idEjeTrabajo,
                    'idUsuarioSolicitante' => $teachers[$i % 3]->idUsuario,
                ]);
                if ($state !== EstadoAccionEnum::Propuesta) {
                    $ds = match ($state) {
                        EstadoAccionEnum::PendientePorCorregir => EstadoDictamenEnum::PendientePorCorregir,
                        EstadoAccionEnum::NoAutorizada => EstadoDictamenEnum::NoAutorizada,
                        default => EstadoDictamenEnum::Aprobada
                    };
                    Dictamen::query()->create(['estadoDictamen' => $ds, 'comentario' => 'Dictamen histórico de evaluación de la propuesta.', 'fechaDictamen' => now()->subDays(20 - $i), 'idAccion' => $a->idAccion, 'idUsuarioEmisor' => $coord->idUsuario]);
                }
                if ($state === EstadoAccionEnum::Realizada) {
                    Evidencia::query()->create(['archivo' => "evidencias/{$folio}-registro.jpg", 'tipoArchivo' => TipoEvidenciaEnum::Fotografia, 'descripcion' => 'Registro fotográfico de las actividades.', 'idAccion' => $a->idAccion]);
                    foreach (array_slice($students, 0, 2) as $student) {
                        Participacion::query()->create(['fechaInscripcion' => now()->subDays(12), 'idUsuario' => $student->idUsuario, 'idAccion' => $a->idAccion]);
                    }
                }
            }
        });
    }

    private function usuario(string $id, string $name, string $email, Rol $role): UsuarioInstitucional
    {
        $u = UsuarioInstitucional::query()->create(['identificadorInstitucional' => $id, 'nombreCompleto' => $name, 'correoInstitucional' => $email, 'password' => bcrypt('password')]);
        $u->roles()->attach($role->idRol, ['fechaAsignacion' => now()]);

        return $u;
    }
}
