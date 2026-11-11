<?php

// pequeña prueba que verifica laravel se ejecute en modo testing
// php artisan test --filter=TestingEnvironmentTest

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TestingEnvironmentTest extends TestCase
{
    public function test_automated_tests_use_mysql_testing_database(): void
    {
        $connection = DB::connection(); // obtenemos el objeto de conexion a la bd, prepara el objeto para hacer consultas usando las herramientas de laravel

        // comprobar que laravel puede conectarse a la base de datos
        $connection->getPdo(); // obtiene instancia php Data OBjects, abre la conexion real, aqui saltara la excepcion

        // comprobar que el motor es mysql
        $this->assertSame('mysql', $connection->getDriverName());

        // comprobar que la base es la de pruebas.
        $this->assertSame(
            'sistema_sustentabilidad_test',
            $connection->getDatabaseName()
        );

        // comprobar que laravel esta en modo testing
        $this->assertSame('testing', app()->environment());
        // app() es una funcion auxiliar que devuelve la instancia principal de la aplicacion. Solicitamos el entorno y comprobamos que sea testing.
    }
}
