<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class ActividadImagesSeeder extends Seeder
{
    public function run(): void
    {
        $baseUrl = 'http://escaladapro-api.test';

        // Registrar o actualizar media records para actividad1-9        $mediaIds = [];
        for ($i = 1; $i <= 9; $i++) {
            $fn   = "actividad{$i}.png";
            $path = storage_path("app/public/images/{$fn}");

            $m = Media::updateOrCreate(
                ['file_name' => $fn],
                [
                    'mime_type' => 'image/png',
                    'disk'      => 'public',
                    'path'      => "images/{$fn}",
                    'alt'       => "Actividad {$i}",
                    'size'      => file_exists($path) ? filesize($path) : 0,
                ]
            );

            $mediaIds[] = $m->id;
            $this->command->line("  actividad{$i}.png → media id:{$m->id}");
        }

        // Reemplazar mediables de la sección gallery (id=24)
        $section = PageSection::find(24);

        if (! $section) {
            $this->command->error('No se encontró la sección gallery id=24');
            return;
        }

        $section->media()->detach();

        foreach ($mediaIds as $idx => $mediaId) {
            $section->media()->attach($mediaId, [
                'collection' => 'gallery',
                'sort_order' => $idx,
            ]);
        }

        $count = $section->media()->count();
        $this->command->info("✓ Galería actualizada con {$count} imágenes.");
    }
}
