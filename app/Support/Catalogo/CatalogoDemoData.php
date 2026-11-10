<?php

namespace App\Support\Catalogo;

use Illuminate\Support\Collection;

/**
 *clase tempo0ral que simula la base de datos
 */
final class CatalogoDemoData
{
    /**
     * categorias disponibles en el filtro del catalogo
     * @return array<int, string>
     */
    public static function categorias(): array
    {
        return [
            'Áreas verdes y biodiversidad',
            'Agua',
            'Residuos',
            'Energía y consumo',
        ];
    }

    /**
     *modalidades disponibles en el filtro del catalogo
     * @return array<int, string>
     */
    public static function modalidades(): array
    {
        return [
            'Presencial',
            'Virtual',
            'Híbrida',
        ];
    }

    /**
     * iniciativs ficticias para la interfaz.
     *
     * @return Collection<int, array<string, int|string>>
     */
    public static function items(): Collection
    {
        return collect([
            [
                'id' => 1,
                'titulo' => 'Jornada de recuperación verde',
                'descripcion' => 'Recuperación de espacios comunes con especies nativas y participación voluntaria de la comunidad universitaria.',
                'categoria' => 'Áreas verdes y biodiversidad',
                'color_tema' => 'green',
                'icono' => 'hoja',
                'fecha' => '22 NOV 2026',
                'fecha_iso' => '2026-11-22',
                'modalidad' => 'Presencial',
            ],
            [
                'id' => 2,
                'titulo' => 'Separación de residuos en el campus',
                'descripcion' => 'Promover la separación responsable y el aprovechamiento de materiales reciclables en cada edificio.',
                'categoria' => 'Residuos',
                'color_tema' => 'amber',
                'icono' => 'reciclaje',
                'fecha' => '25 NOV 2026',
                'fecha_iso' => '2026-11-25',
                'modalidad' => 'Presencial',
            ],
            [
                'id' => 3,
                'titulo' => 'Campaña de ahorro de agua',
                'descripcion' => 'Acciones informativas para reducir el consumo y cuidar los recursos hídricos del campus.',
                'categoria' => 'Agua',
                'color_tema' => 'blue',
                'icono' => 'agua',
                'fecha' => '02 DIC 2026',
                'fecha_iso' => '2026-12-02',
                'modalidad' => 'Híbrida',
            ],
            [
                'id' => 4,
                'titulo' => 'Eficiencia energética en laboratorios',
                'descripcion' => 'Diagnóstico y buenas prácticas para reducir el consumo eléctrico en laboratorios y talleres.',
                'categoria' => 'Energía y consumo',
                'color_tema' => 'amber',
                'icono' => 'energia',
                'fecha' => '09 DIC 2026',
                'fecha_iso' => '2026-12-09',
                'modalidad' => 'Virtual',
            ],
            [
                'id' => 5,
                'titulo' => 'Monitoreo comunitario del agua',
                'descripcion' => 'Taller abierto para aprender a medir la calidad del agua en cuerpos cercanos al campus.',
                'categoria' => 'Agua',
                'color_tema' => 'blue',
                'icono' => 'agua',
                'fecha' => '12 DIC 2026',
                'fecha_iso' => '2026-12-12',
                'modalidad' => 'Híbrida',
            ],
            [
                'id' => 6,
                'titulo' => 'Ruta segura hacia el campus',
                'descripcion' => 'Acompañamiento y señalización de rutas peatonales y ciclistas para llegar de forma segura.',
                'categoria' => 'Áreas verdes y biodiversidad',
                'color_tema' => 'green',
                'icono' => 'comunidad',
                'fecha' => '15 DIC 2026',
                'fecha_iso' => '2026-12-15',
                'modalidad' => 'Presencial',
            ],
            [
                'id' => 7,
                'titulo' => 'Compostaje de residuos orgánicos',
                'descripcion' => 'Instalación de composteras comunitarias y capacitación para su operación diaria.',
                'categoria' => 'Residuos',
                'color_tema' => 'amber',
                'icono' => 'reciclaje',
                'fecha' => '18 DIC 2026',
                'fecha_iso' => '2026-12-18',
                'modalidad' => 'Presencial',
            ],
            [
                'id' => 8,
                'titulo' => 'Senderos interpretativos del campus',
                'descripcion' => 'Recorridos guiados para reconocer la flora y fauna que habita en las áreas verdes.',
                'categoria' => 'Áreas verdes y biodiversidad',
                'color_tema' => 'green',
                'icono' => 'hoja',
                'fecha' => '10 ENE 2027',
                'fecha_iso' => '2027-01-10',
                'modalidad' => 'Presencial',
            ],
            [
                'id' => 9,
                'titulo' => 'Reto de movilidad sin auto',
                'descripcion' => 'Semana de retos para promover traslados peatonales, ciclistas y compartidos.',
                'categoria' => 'Energía y consumo',
                'color_tema' => 'blue',
                'icono' => 'comunidad',
                'fecha' => '14 ENE 2027',
                'fecha_iso' => '2027-01-14',
                'modalidad' => 'Virtual',
            ],
            [
                'id' => 10,
                'titulo' => 'Reparación de fugas en sanitarios',
                'descripcion' => 'Brigada de revisión y mantenimiento para reducir pérdidas de agua en edificios.',
                'categoria' => 'Agua',
                'color_tema' => 'blue',
                'icono' => 'agua',
                'fecha' => '20 ENE 2027',
                'fecha_iso' => '2027-01-20',
                'modalidad' => 'Presencial',
            ],
            [
                'id' => 11,
                'titulo' => 'Papel cero en trámites escolares',
                'descripcion' => 'Digitalización de formatos y firmas para disminuir el consumo de papel.',
                'categoria' => 'Residuos',
                'color_tema' => 'amber',
                'icono' => 'reciclaje',
                'fecha' => '25 ENE 2027',
                'fecha_iso' => '2027-01-25',
                'modalidad' => 'Virtual',
            ],
            [
                'id' => 12,
                'titulo' => 'Paneles solares en áreas comunes',
                'descripcion' => 'Instalación demostrativa de energía solar para iluminación de espacios compartidos.',
                'categoria' => 'Energía y consumo',
                'color_tema' => 'amber',
                'icono' => 'energia',
                'fecha' => '02 FEB 2027',
                'fecha_iso' => '2027-02-02',
                'modalidad' => 'Híbrida',
            ],
        ]);
    }
}
