<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class ActivityGallerySeeder extends Seeder
{
    public function run(): void
    {
        // Las 9 imágenes del mosaico (orden = posición en el grid)
        $srcBase  = '/Users/wiletinoco/VUE/escaladapro/web/public/images/';
        $dstBase  = 'images/';
        $storBase = storage_path('app/public/images/');

        $files = [
            ['file' => 'n-1.png',                            'alt' => 'Actividades de escalada'],
            ['file' => 'img-20200308-wa-00051.png',          'alt' => 'Actividad escalada'],
            ['file' => 'reforestacion-casualas-1.png',       'alt' => 'Actividad al aire libre'],
            ['file' => 'huasteca-41.png',                    'alt' => 'Montaña escalada'],
            ['file' => 'potrero-1.png',                      'alt' => 'Escalada en roca'],
            ['file' => 'slide1.png',                         'alt' => 'Comunidad de escalada'],
            ['file' => 'patrocinador1.png',                  'alt' => 'Equipo escalada'],
            ['file' => 'screen-shot-20241119-at-64211-pm-1.png', 'alt' => 'Escalada actividad'],
            ['file' => 'source-pico-norte.png',              'alt' => 'Escalada historia'],
        ];

        $page = Page::where('slug', 'actividades')->firstOrFail();

        // Eliminar sección gallery previa si existe
        $section = PageSection::firstOrCreate(
            ['page_id' => $page->id, 'type' => 'gallery'],
            [
                'heading'    => 'Mosaico de Actividades',
                'sort_order' => 0,
                'status'     => 'active',
            ]
        );

        // Desasociar todos los media previos de la colección gallery
        $section->media()->wherePivot('collection', 'gallery')->detach();

        foreach ($files as $i => $img) {
            $dst  = $dstBase . $img['file'];
            $full = $storBase . $img['file'];

            if (!file_exists($full)) {
                copy($srcBase . $img['file'], $full);
                $this->command->info("Copiado: {$img['file']}");
            }

            $media = Media::firstOrCreate(
                ['path' => $dst, 'disk' => 'public'],
                [
                    'file_name' => $img['file'],
                    'mime_type' => 'image/png',
                    'size'      => filesize($full),
                    'alt'       => $img['alt'],
                ]
            );

            // Adjuntar a la sección con sort_order para mantener posición del mosaico
            $section->media()->attach($media->id, [
                'collection' => 'gallery',
                'sort_order' => $i,
            ]);

            $this->command->info("Posición {$i}: media #{$media->id} ({$img['file']})");
        }

        $this->command->info("✓ Sección gallery id={$section->id} con " . count($files) . " imágenes.");
    }
}
