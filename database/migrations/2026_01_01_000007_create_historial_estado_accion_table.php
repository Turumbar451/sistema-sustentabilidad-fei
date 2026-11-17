<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('HistorialEstadoAccion', function (Blueprint $table): void {
            $table->increments('idHistorial');
            $table->unsignedInteger('idAccion');
            $table->unsignedInteger('idEstadoAccion');
            $table->unsignedInteger('idUsuarioResponsable')->nullable();
            $table->dateTime('fechaCambio');
            $table->text('motivo')->nullable();

            $table->index(
                ['idAccion', 'fechaCambio'],
                'historial_accion_fecha_idx',
            );

            $table->foreign('idAccion')->references('idAccion')
                ->on('AccionSustentabilidad')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('idEstadoAccion')->references('idEstadoAccion')
                ->on('EstadoAccion')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('idUsuarioResponsable')->references('idUsuario')
                ->on('UsuarioInstitucional')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('HistorialEstadoAccion');
    }
};
