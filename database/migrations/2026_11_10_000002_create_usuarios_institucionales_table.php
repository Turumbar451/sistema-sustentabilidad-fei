<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios_institucionales', function (Blueprint $table): void {
            $table->id('idUsuario');
            $table->string('identificadorInstitucional', 50)->unique();
            $table->string('nombreCompleto', 150);
            $table->string('correoInstitucional', 150)->unique();
            $table->string('telefonoContacto', 20)->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_institucionales');
    }
};
