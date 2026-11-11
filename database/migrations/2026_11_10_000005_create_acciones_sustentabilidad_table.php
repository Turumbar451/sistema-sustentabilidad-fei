<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acciones_sustentabilidad', function (Blueprint $table): void {
            $table->id('idAccion');
            $table->string('folio', 50)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion');
            $table->string('modalidad', 100);
            $table->dateTime('fechaRegistro');
            $table->date('fechaInicio');
            $table->date('fechaTermino');
            $table->text('requerimientos')->nullable();
            $table->string('estadoActual', 50)->default('Propuesta');
            $table->foreignId('idEjeTrabajo')->constrained('ejes_trabajo', 'idEjeTrabajo')->restrictOnDelete();
            $table->foreignId('idUsuarioSolicitante')->constrained('usuarios_institucionales', 'idUsuario')->restrictOnDelete();
            $table->timestamps();
            $table->index(['estadoActual', 'fechaRegistro']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acciones_sustentabilidad');
    }
};
