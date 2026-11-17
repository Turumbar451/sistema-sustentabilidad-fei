<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Participacion', function (Blueprint $table): void {
            $table->increments('idParticipacion');
            $table->dateTime('fechaInscripcion');
            $table->unsignedInteger('idUsuario');
            $table->unsignedInteger('idAccion');

            $table->unique(
                ['idUsuario', 'idAccion'],
                'participacion_usuario_accion_unique',
            );

            $table->foreign('idUsuario')->references('idUsuario')
                ->on('UsuarioInstitucional')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('idAccion')->references('idAccion')
                ->on('AccionSustentabilidad')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Participacion');
    }
};
