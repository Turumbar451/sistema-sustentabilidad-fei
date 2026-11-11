<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participaciones', function (Blueprint $table): void {
            $table->id('idParticipacion');
            $table->dateTime('fechaInscripcion');
            $table->foreignId('idUsuario')->constrained('usuarios_institucionales', 'idUsuario')->cascadeOnDelete();
            $table->foreignId('idAccion')->constrained('acciones_sustentabilidad', 'idAccion')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['idUsuario', 'idAccion'], 'participaciones_usuario_accion_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participaciones');
    }
};
