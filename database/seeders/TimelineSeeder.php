<?php

namespace Database\Seeders;

use App\Models\Timeline;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $milestones = [
            [
                'date' => 'AGOSTO 2016',
                'title' => 'Sucesos en la pared de escalada "Las ánimas"',
                'body' => 'Debido a ciertas inspecciones que realizó el Instituto Nacional de Antropología e Historia, el Instituto decidió intervenir en la zona. En una primer acercamiento, el INAH buscó cerrar el área para la escalada. Después de algunas reuniones entre escaladores, se formó un quasi-colectivo que negoció que la pared de las ánimas no fuera cerrada en su totalidad. Se colaboró con el instituto para la remoción de las plaquetas que se encontraban dentro del área delimitada.',
                'year' => 2016,
                'month' => 8,
                'order' => 0,
                'status' => 'published',
                'published_at' => '2016-08-15 10:00:00',
            ],
            [
                'date' => 'DICIEMBRE 2017',
                'title' => 'Constitución de Escalada Libre México A.C.',
                'body' => 'Después del conflicto suscitado con el INAH, algunos de los escaladores que apoyaron para negociar el cierre parcial de "Las Ánimas", constituyeron formalmente una asociación civil denominada "Escalada Libre México A.C.".',
                'year' => 2017,
                'month' => 12,
                'order' => 0,
                'status' => 'published',
                'published_at' => '2017-12-10 10:00:00',
            ],
            [
                'date' => 'JULIO 2018',
                'title' => 'Primera actividad – Plática en la Ciénega de González',
                'body' => 'Se buscó primeramente un acercamiento con los habitantes del poblado de la "Ciénega de González", para escuchar sus necesidades y opiniones respecto a la práctica de la escalada en la localidad.',
                'year' => 2018,
                'month' => 7,
                'order' => 0,
                'status' => 'published',
                'published_at' => '2018-07-20 10:00:00',
            ],
            [
                'date' => 'NOVIEMBRE 2018',
                'title' => 'Instalación del Primer letrero en la Huasteca',
                'body' => 'Después de resuelto el conflicto con el INAH, se pensaron en algunas de las necesidades del deporte en la localidad. Se decidió instalar letreros en diversas localidades y zonas de escalada. El primero lo fue en la Huasteca, en la pared de "Las Cazuelas".',
                'year' => 2018,
                'month' => 11,
                'order' => 0,
                'status' => 'published',
                'published_at' => '2018-11-15 10:00:00',
            ],
            [
                'date' => 'MARZO 2019',
                'title' => 'Primer reequipado',
                'body' => 'Habiendo muchas necesidades en torno a la escalada en el Estado de Nuevo León, se decidió que la asociación buscaría dar mantenimiento a las rutas de escalada en la región. Así, se llevó a cabo el primer reequipado de rutas en el parque "Potrero Chico", en el Municipio de Hidalgo. Se decidió intervenir la zona denominada "El Surf" con anclajes químicos de acero inoxidable.',
                'year' => 2019,
                'month' => 3,
                'order' => 0,
                'status' => 'published',
                'published_at' => '2019-03-10 10:00:00',
            ],
            [
                'date' => 'DICIEMBRE 2023',
                'title' => '100 años de escalada en México',
                'body' => 'A manera de conmemorar una fecha tan importante por los 100 años de escalada en el país, se realizó un evento en la Huasteca en el que se proyectaron cortometrajes, se dieron pláticas y hubo una convivencia.',
                'year' => 2023,
                'month' => 12,
                'order' => 0,
                'status' => 'published',
                'published_at' => '2023-12-05 10:00:00',
            ],
            [
                'date' => 'MARZO 2024',
                'title' => 'Recepción de autorización como donataria autorizada',
                'body' => 'En esta fecha Escalada Libre se convirtió en la primera asociación de escaladores que recibió la autorización para entregar donativos deducibles de impuestos, otorgada por el Servicio de Administración Tributaria. Se buscó esta autorización para formalizar e institucionalizar la asociación.',
                'year' => 2024,
                'month' => 3,
                'order' => 0,
                'status' => 'published',
                'published_at' => '2024-03-18 10:00:00',
            ],
        ];

        foreach ($milestones as $milestone) {
            Timeline::create($milestone);
        }
    }
}
