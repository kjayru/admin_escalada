<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class SectionImagesSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            ['file' => 'img-33661.png',                'alt' => 'Infraestructura - Escalada',    'section_id' => 20],
            ['file' => 'img-20200308-wa-00051.png',    'alt' => 'Acceso - Reunión',              'section_id' => 21],
            ['file' => 'reforestacion-casualas-1.png', 'alt' => 'Conservación - Reforestación',  'section_id' => 22],
            ['file' => 'unrioenelrio-home-1.png',      'alt' => 'Conocimiento - Río',            'section_id' => 23],
        ];

        foreach ($images as $img) {
            $src         = '/Users/wiletinoco/VUE/escaladapro/web/public/images/' . $img['file'];
            $dst         = 'images/' . $img['file'];
            $storagePath = storage_path('app/public/' . $dst);

            if (!file_exists($storagePath)) {
                copy($src, $storagePath);
                $this->command->info("Copiado: {$img['file']}");
            }

            $media = Media::firstOrCreate(
                ['path' => $dst, 'disk' => 'public'],
                [
                    'file_name' => $img['file'],
                    'mime_type' => 'image/png',
                    'size'      => filesize($storagePath),
                    'alt'       => $img['alt'],
                ]
            );

            PageSection::where('id', $img['section_id'])
                ->update(['featured_media_id' => $media->id]);

            $this->command->info("Sección {$img['section_id']} → media #{$media->id} ({$img['file']})");
        }
    }
}
