<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('UsuarioRol', function (Blueprint $table): void {
            $table->increments('idUsuarioRol');
            $table->unsignedInteger('idUsuario');
            $table->unsignedInteger('idRol');
            $table->dateTime('fechaAsignacion');
            $table->dateTime('fechaFin')->nullable();

            $table->index(['idUsuario', 'idRol'], 'usuario_rol_usuario_rol_idx');
            $table->index(
                ['idUsuario', 'idRol', 'fechaFin'],
                'usuario_rol_vigencia_idx',
            );

            $table->foreign('idUsuario')->references('idUsuario')
                ->on('UsuarioInstitucional')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('idRol')->references('idRol')
                ->on('Rol')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('UsuarioRol');
    }
};
