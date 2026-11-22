<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

// un facade proporciona una interfaz estatica para interactuar con clases en el contenedor de servicios de laravel. En este caso interactuamos con la clase Schema que nos permite crear y modificar tablas en la base de datos
use Illuminate\Support\Facades\Schema;

//clase anonima
return new class extends Migration
{
    public function up(): void
    {
        //nombre de tabla y callback que define columans de tabla
        Schema::create('HistorialEstadoAccion', function (Blueprint $table): void {
            $table->increments('idHistorial');
            $table->unsignedInteger('idAccion');
            $table->unsignedInteger('idEstadoAccion');
            $table->unsignedInteger('idUsuarioResponsable')->nullable();
            $table->dateTime('fechaCambio');
            $table->text('motivo')->nullable();


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
