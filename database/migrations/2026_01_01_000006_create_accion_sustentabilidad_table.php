<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('AccionSustentabilidad', function (Blueprint $table): void {
            $table->increments('idAccion');
            $table->string('folio', 50)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion');
            $table->string('modalidad', 100);
            $table->string('formaTrabajo', 30);
            $table->dateTime('fechaRegistro');
            $table->date('fechaInicio')->nullable();
            $table->date('fechaTermino')->nullable();
            $table->text('requerimientos')->nullable();
            $table->unsignedInteger('idEjeTrabajo');
            $table->unsignedInteger('idUsuarioSolicitante');
            $table->unsignedInteger('idEstadoAccion');

            $table->index(
                ['idEstadoAccion', 'idEjeTrabajo', 'modalidad', 'formaTrabajo'],
                'idx_accion_filtros',
            );

            $table->index(
                ['idEstadoAccion', 'fechaRegistro'],
                'accion_estado_fecha_idx',
            );

            $table->foreign('idEjeTrabajo')->references('idEjeTrabajo')
                ->on('EjeTrabajo')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('idUsuarioSolicitante')->references('idUsuario')
                ->on('UsuarioInstitucional')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('idEstadoAccion')->references('idEstadoAccion')
                ->on('EstadoAccion')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('AccionSustentabilidad');
    }
};
