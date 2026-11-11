<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencias', function (Blueprint $table): void {
            $table->id('idEvidencia');
            $table->string('archivo', 500);
            $table->string('tipoArchivo', 100);
            $table->text('descripcion');
            $table->foreignId('idAccion')->constrained('acciones_sustentabilidad', 'idAccion')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};
