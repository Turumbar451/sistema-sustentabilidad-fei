<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //levantar migracion
    public function up(): void
    {
        //schema es un facade. un facade es una clase que proporciona una interfaz estatica para acceder a clases en el contenedor de servicios de laravel. 
        Schema::create('AccionSustentabilidad', function (Blueprint $table): void {
            //Blueprint da la interfaz para crear las columnas de la tabla
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

    //borrar migracion
    public function down(): void
    {
        Schema::dropIfExists('AccionSustentabilidad');
    }
};
