<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('UsuarioInstitucional', function (Blueprint $table): void {
            $table->increments('idUsuario');
            $table->string('identificadorInstitucional', 100)->unique();
            $table->string('nombreCompleto', 150);
            $table->string('correoInstitucional', 255)->unique();
            $table->string('telefonoContacto', 30)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('UsuarioInstitucional');
    }
};
