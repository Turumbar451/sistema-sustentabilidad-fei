<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dictamenes', function (Blueprint $table): void {
            $table->id('idDictamen');
            $table->string('estadoDictamen', 50);
            $table->text('comentario');
            $table->dateTime('fechaDictamen');
            $table->foreignId('idAccion')->constrained('acciones_sustentabilidad', 'idAccion')->cascadeOnDelete();
            $table->foreignId('idUsuarioEmisor')->constrained('usuarios_institucionales', 'idUsuario')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dictamenes');
    }
};
