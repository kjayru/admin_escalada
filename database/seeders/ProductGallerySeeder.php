<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductGallerySeeder extends Seeder
{
    /**
     * Agrega imágenes de galería al producto "arnés de escalada sport pro".
     * Usa imágenes de escalada ya disponibles en storage/app/public.
     */
    public function run(): void
    {
        $product = Product::where('slug', 'arnes-escalada-sport-pro')->first();

        if (! $product) {
            $this->command->error('Producto arnes-escalada-sport-pro no encontrado.');
            return;
        }

        // Imágenes fuente: del proyecto Nuxt (mismas que usa SectionImagesSeeder)
        $nuxtPublic = '/Users/wiletinoco/VUE/escaladapro/web/public/images/';

        $galleryImages = [
            ['file' => 'pico-norte-1.png',    'alt' => 'Arnés en acción - vista frontal'],
            ['file' => 'potrero-1.png',        'alt' => 'Arnés de escalada sport - lateral'],
            ['file' => 'huasteca-41.png',      'alt' => 'Arnés – detalle mosquetón'],
            ['file' => 'producto.png',         'alt' => 'Arnés – portamateriales'],
            ['file' => 'producto1.png',        'alt' => 'Arnés – cintura y perneras'],
        ];

        // Primero limpiar galería existente
        DB::table('mediables')
            ->where('mediable_type', Product::class)
            ->where('mediable_id', $product->id)
            ->where('collection', 'gallery')
            ->delete();

        $order = 0;

        foreach ($galleryImages as $img) {
            $src         = $nuxtPublic . $img['file'];
            $dst         = 'products/arnes-gallery/' . $img['file'];
            $storagePath = storage_path('app/public/' . $dst);

            // Crear directorio si no existe
            if (! file_exists(dirname($storagePath))) {
                mkdir(dirname($storagePath), 0755, true);
            }

            // Copiar si no existe ya
            if (! file_exists($storagePath) && file_exists($src)) {
                copy($src, $storagePath);
                $this->command->info("Copiado: {$img['file']}");
            }

            if (! file_exists($storagePath)) {
                $this->command->warn("Imagen no encontrada: {$src} — omitida.");
                continue;
            }

            // Crear (o recuperar) registro Media
            $media = Media::firstOrCreate(
                ['path' => $dst, 'disk' => 'public'],
                [
                    'file_name' => $img['file'],
                    'mime_type' => 'image/png',
                    'size'      => filesize($storagePath),
                    'alt'       => $img['alt'],
                    'created_by' => 1,
                ]
            );

            // Vincular al producto en la collection 'gallery'
            DB::table('mediables')->insertOrIgnore([
                'media_id'       => $media->id,
                'mediable_type'  => Product::class,
                'mediable_id'    => $product->id,
                'collection'     => 'gallery',
                'sort_order'     => $order++,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            $this->command->info("Galería #{$order}: {$img['file']} → media #{$media->id}");
        }

        $this->command->info("✓ {$order} imágenes agregadas a la galería del arnés (ID {$product->id}).");
    }
}
