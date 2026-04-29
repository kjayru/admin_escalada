<?php

namespace Database\Seeders;

use App\Models\LegacyMedia;
use App\Models\Sponsor;
use App\Models\SponsorPlacement;
use Illuminate\Database\Seeder;

class LogoRowSponsorsSeeder extends Seeder
{
    public function run(): void
    {
        $logos = [
            [
                'slug'      => 'pico-norte',
                'name'      => 'Pico Norte Climbing',
                'file'      => 'pico-norte-1.png',
                'mime'      => 'image/png',
                'website'   => null,
            ],
            [
                'slug'      => 'source-climbing',
                'name'      => 'Source Climbing',
                'file'      => 'n-1.png',
                'mime'      => 'image/png',
                'website'   => null,
            ],
            [
                'slug'      => 'mad-rock',
                'name'      => 'Mad Rock',
                'file'      => 'mad-rock-1.png',
                'mime'      => 'image/png',
                'website'   => null,
            ],
            [
                'slug'      => 'medi-lab',
                'name'      => 'Medi Lab',
                'file'      => 'img-33661.png',
                'mime'      => 'image/png',
                'website'   => null,
            ],
            [
                'slug'      => 'clinica-everest-ipeth',
                'name'      => 'Clínica Everest IPETH',
                'file'      => 'clinica-everest-1.png',
                'mime'      => 'image/png',
                'website'   => null,
            ],
            [
                'slug'      => 'hanuman-cafe',
                'name'      => 'Hanuman Café',
                'file'      => 'hanuman-cafe-1.png',
                'mime'      => 'image/png',
                'website'   => null,
            ],
            [
                'slug'      => 'mountain-bites',
                'name'      => 'Mountain Bites',
                'file'      => 'mountain-bites-1.png',
                'mime'      => 'image/png',
                'website'   => null,
            ],
            [
                'slug'      => 'monkey-hands',
                'name'      => 'Monkey Hands',
                'file'      => 'monkey-hands-1.png',
                'mime'      => 'image/png',
                'website'   => null,
            ],
            [
                'slug'      => 'la-cumbre-cotidiana',
                'name'      => 'La Cumbre Cotidiana AC',
                'file'      => 'cumbre-cotidiana-1.png',
                'mime'      => 'image/png',
                'website'   => null,
            ],
            [
                'slug'      => 'papeleria-morelos',
                'name'      => 'Papelería Morelos',
                'file'      => 'photo-202409191744391.png',
                'mime'      => 'image/png',
                'website'   => null,
            ],
            [
                'slug'      => 'altrac',
                'name'      => 'ALTRAC Técnicos en Alturas',
                'file'      => 'altrac-vertical-1.png',
                'mime'      => 'image/png',
                'website'   => null,
            ],
        ];

        foreach ($logos as $order => $data) {
            // 1. Crear/recuperar LegacyMedia
            $path = 'sponsors/logos/' . $data['file'];

            $media = LegacyMedia::firstOrCreate(
                ['path' => $path],
                [
                    'disk'      => 'public',
                    'file_name' => $data['file'],
                    'mime_type' => $data['mime'],
                    'size'      => filesize(storage_path('app/public/' . $path)) ?: 0,
                    'alt'       => $data['name'],
                    'title'     => $data['name'],
                ]
            );

            // 2. Crear/recuperar Sponsor con logo
            $sponsor = Sponsor::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'name'          => $data['name'],
                    'slug'          => $data['slug'],
                    'logo_media_id' => $media->id,
                    'website_url'   => $data['website'],
                    'status'        => 'active',
                ]
            );

            // Asignar logo si el sponsor ya existía sin él
            if (!$sponsor->logo_media_id) {
                $sponsor->update(['logo_media_id' => $media->id]);
            }

            // 3. Crear SponsorPlacement logo_row (evitar duplicados)
            $exists = SponsorPlacement::where('sponsor_id', $sponsor->id)
                ->where('placement', 'logo_row')
                ->exists();

            if (!$exists) {
                SponsorPlacement::create([
                    'sponsor_id' => $sponsor->id,
                    'placement'  => 'logo_row',
                    'sort_order' => $order + 1,
                    'is_active'  => true,
                ]);
            }
        }

        $this->command->info('Logo row sponsors sembrados correctamente (' . count($logos) . ' sponsors).');
    }
}
