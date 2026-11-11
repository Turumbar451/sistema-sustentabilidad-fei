<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ejes_trabajo', function (Blueprint $table): void {
            $table->id('idEjeTrabajo');
            $table->string('nombre', 150)->unique();
            $table->string('color_tema', 30)->default('green');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejes_trabajo');
    }
};
