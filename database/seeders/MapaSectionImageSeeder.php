<?php

namespace Database\Seeders;

use App\Models\LegacyMedia;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class MapaSectionImageSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Copiar imagen al storage y registrar LegacyMedia
        $srcPath = base_path('../escaladapro/web/public/images/home-mapa-desktop.jpg');
        $destRelative = 'pages/inicio/home-mapa-desktop.jpg';
        $destAbsolute = storage_path('app/public/' . $destRelative);

        if (!file_exists(dirname($destAbsolute))) {
            mkdir(dirname($destAbsolute), 0775, true);
        }

        if (file_exists($srcPath)) {
            copy($srcPath, $destAbsolute);
            $this->command->info('Imagen copiada al storage.');
        } else {
            $this->command->warn('No se encontró la imagen en: ' . $srcPath);
            $this->command->warn('Asegúrate de copiarla manualmente a: ' . $destAbsolute);
        }

        $media = LegacyMedia::firstOrCreate(
            ['path' => $destRelative],
            [
                'disk'      => 'public',
                'file_name' => 'home-mapa-desktop.jpg',
                'mime_type' => 'image/jpeg',
                'size'      => file_exists($destAbsolute) ? filesize($destAbsolute) : 0,
                'alt'       => 'Mapa de trabajo - Nuevo León',
                'title'     => 'Mapa de trabajo - Nuevo León',
            ]
        );

        $imageUrl = $media->url;
        $this->command->info('URL de imagen: ' . $imageUrl);

        // 2. Buscar la página inicio y la sección mapa
        $page = Page::where('slug', 'inicio')->first();
        if (!$page) {
            $this->command->error('No se encontró la página con slug "inicio".');
            return;
        }

        $section = PageSection::where('page_id', $page->id)
            ->whereJsonContains('settings->key', 'mapa')
            ->first();

        if (!$section) {
            // Intentar buscar por tipo
            $section = PageSection::where('page_id', $page->id)
                ->where(function ($q) {
                    $q->where('type', 'mapa')
                      ->orWhere('heading', 'like', '%Dónde%')
                      ->orWhere('heading', 'like', '%Donde%');
                })
                ->first();
        }

        if ($section) {
            $settings = $section->settings ?? [];
            $settings['image'] = $imageUrl;
            $section->update(['settings' => $settings]);
            $this->command->info('Sección mapa actualizada con la nueva imagen.');
        } else {
            $this->command->warn('No se encontró la sección "mapa" en la página inicio.');
            $this->command->warn('URL de imagen disponible: ' . $imageUrl);
            $this->command->warn('Actualiza manualmente el setting "image" de la sección mapa.');
        }
    }
}
