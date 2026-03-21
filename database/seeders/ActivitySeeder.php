<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            // Actividades 2025
            [
                'name' => 'Limpieza Acantilados Potrero Chico',
                'year' => 2025,
                'order' => 1,
                'status' => 'published',
                'published_at' => '2025-02-15 10:00:00',
            ],
            [
                'name' => 'Mantenimiento Ruta El Toro',
                'year' => 2025,
                'order' => 2,
                'status' => 'published',
                'published_at' => '2025-03-20 10:00:00',
            ],
            [
                'name' => 'Requipado Sector La Huasteca',
                'year' => 2025,
                'order' => 3,
                'status' => 'published',
                'published_at' => '2025-05-10 10:00:00',
            ],
            [
                'name' => 'Taller de Seguridad en Escalada',
                'year' => 2025,
                'order' => 4,
                'status' => 'published',
                'published_at' => '2025-06-15 10:00:00',
            ],
            [
                'name' => 'Competencia Regional de Escalada',
                'year' => 2025,
                'order' => 5,
                'status' => 'published',
                'published_at' => '2025-08-22 10:00:00',
            ],
            [
                'name' => 'Reforestación Cañón de la Huasteca',
                'year' => 2025,
                'order' => 6,
                'status' => 'published',
                'published_at' => '2025-10-05 10:00:00',
            ],
            [
                'name' => 'Reunión CONANP - Conservación',
                'year' => 2025,
                'order' => 7,
                'status' => 'published',
                'published_at' => '2025-11-12 10:00:00',
            ],

            // Actividades 2026
            [
                'name' => 'Instalación Señalética Preventiva',
                'year' => 2026,
                'order' => 1,
                'status' => 'published',
                'published_at' => '2026-01-18 10:00:00',
            ],
            [
                'name' => 'Clínica de Escalada en Multilargos',
                'year' => 2026,
                'order' => 2,
                'status' => 'published',
                'published_at' => '2026-02-28 10:00:00',
            ],
            [
                'name' => 'Mantenimiento Senderos de Acceso',
                'year' => 2026,
                'order' => 3,
                'status' => 'published',
                'published_at' => '2026-03-15 10:00:00',
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}
