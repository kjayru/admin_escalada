<?php

namespace Database\Seeders;

use App\Models\TransparencyDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransparencyDocumentSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia documentos con tipos viejos para re-sembrar con los correctos
        TransparencyDocument::whereIn('type', ['annual', 'financial', 'legal', 'operations', 'other'])
            ->delete();

        $now = now();
        $docs = [
            // ── ASAMBLEAS ────────────────────────────────────────────
            [
                'title'        => 'Acta constitutiva',
                'slug'         => 'acta-constitutiva',
                'type'         => 'asambleas',
                'year'         => null,
                'description'  => 'Acta constitutiva de la Asociación Escalada Libre México A.C.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Asamblea 2019',
                'slug'         => 'asamblea-2019',
                'type'         => 'asambleas',
                'year'         => 2019,
                'description'  => 'Acta de asamblea general ordinaria 2019.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Reglamento',
                'slug'         => 'reglamento-2020',
                'type'         => 'asambleas',
                'year'         => 2020,
                'description'  => 'Reglamento interno de la asociación.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Asamblea General Ordinaria 2020',
                'slug'         => 'asamblea-general-ordinaria-2020',
                'type'         => 'asambleas',
                'year'         => 2020,
                'description'  => 'Acta de asamblea general ordinaria 2020.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Lista de asistencia asamblea ordinaria 2020',
                'slug'         => 'lista-asistencia-asamblea-2020',
                'type'         => 'asambleas',
                'year'         => 2020,
                'description'  => 'Lista de asistencia a la asamblea ordinaria 2020.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Asamblea extraordinaria 2022',
                'slug'         => 'asamblea-extraordinaria-2022',
                'type'         => 'asambleas',
                'year'         => 2022,
                'description'  => 'Acta de asamblea extraordinaria 2022.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Asamblea 2021',
                'slug'         => 'asamblea-2021',
                'type'         => 'asambleas',
                'year'         => 2021,
                'description'  => 'Acta de asamblea general ordinaria 2021.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Asamblea 2023',
                'slug'         => 'asamblea-2023',
                'type'         => 'asambleas',
                'year'         => 2023,
                'description'  => 'Acta de asamblea general ordinaria 2023.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Asamblea 2024',
                'slug'         => 'asamblea-2024',
                'type'         => 'asambleas',
                'year'         => 2024,
                'description'  => 'Acta de asamblea general ordinaria 2024.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Asamblea 2025',
                'slug'         => 'asamblea-2025',
                'type'         => 'asambleas',
                'year'         => 2025,
                'description'  => 'Acta de asamblea general ordinaria 2025.',
                'status'       => 'published',
                'published_at' => $now,
            ],

            // ── REPORTES ─────────────────────────────────────────────
            [
                'title'        => 'Informe Anual 2024',
                'slug'         => 'informe-anual-2024-reportes',
                'type'         => 'reportes',
                'year'         => 2024,
                'description'  => 'Informe anual de actividades y logros 2024.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Informe de Operaciones 2024',
                'slug'         => 'informe-operaciones-2024-reportes',
                'type'         => 'reportes',
                'year'         => 2024,
                'description'  => 'Informe de operaciones y programas 2024.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Informe Anual 2023',
                'slug'         => 'informe-anual-2023-reportes',
                'type'         => 'reportes',
                'year'         => 2023,
                'description'  => 'Informe anual de actividades y logros 2023.',
                'status'       => 'published',
                'published_at' => $now,
            ],

            // ── ESTADOS DE CUENTA ────────────────────────────────────
            [
                'title'        => 'Estado Financiero 2024',
                'slug'         => 'estado-financiero-2024-estados',
                'type'         => 'estados',
                'year'         => 2024,
                'description'  => 'Balance general y estado de resultados 2024.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Estado Financiero 2023',
                'slug'         => 'estado-financiero-2023-estados',
                'type'         => 'estados',
                'year'         => 2023,
                'description'  => 'Balance general y estado de resultados 2023.',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'title'        => 'Estado Financiero 2022',
                'slug'         => 'estado-financiero-2022-estados',
                'type'         => 'estados',
                'year'         => 2022,
                'description'  => 'Balance general y estado de resultados 2022.',
                'status'       => 'published',
                'published_at' => $now,
            ],
        ];

        foreach ($docs as $doc) {
            TransparencyDocument::firstOrCreate(
                ['slug' => $doc['slug']],
                $doc
            );
        }

        $this->command->info('Documentos de transparencia sembrados: ' . count($docs));
    }
}
