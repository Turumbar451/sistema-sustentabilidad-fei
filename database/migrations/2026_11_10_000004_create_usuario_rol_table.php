<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_rol', function (Blueprint $table): void {
            $table->id('idUsuarioRol');
            $table->foreignId('idUsuario')->constrained('usuarios_institucionales', 'idUsuario')->cascadeOnDelete();
            $table->foreignId('idRol')->constrained('roles', 'idRol')->cascadeOnDelete();
            $table->dateTime('fechaAsignacion')->useCurrent();
            $table->unique(['idUsuario', 'idRol']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_rol');
    }
};
