<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Evidencia', function (Blueprint $table): void {
            $table->increments('idEvidencia');
            $table->string('nombreArchivo', 255);
            $table->string('ubicacionArchivo', 500);
            $table->string('tipoArchivo', 100);
            $table->text('descripcion')->nullable();
            $table->dateTime('fechaCarga');
            $table->unsignedInteger('idAccion');
            $table->unsignedInteger('idUsuarioCarga');

            $table->index(
                ['idAccion', 'fechaCarga'],
                'evidencia_accion_fecha_idx',
            );

            $table->foreign('idAccion')->references('idAccion')
                ->on('AccionSustentabilidad')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('idUsuarioCarga')->references('idUsuario')
                ->on('UsuarioInstitucional')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Evidencia');
    }
};
