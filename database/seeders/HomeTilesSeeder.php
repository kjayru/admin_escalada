<?php

namespace Database\Seeders;

use App\Models\LegacyMedia;
use App\Models\Sponsor;
use App\Models\SponsorPlacement;
use Illuminate\Database\Seeder;

class HomeTilesSeeder extends Seeder
{
    public function run(): void
    {
        $tiles = [
            ['file' => 'exposure_box.svg',          'mime' => 'image/svg+xml', 'slug' => 'exposure',      'name' => 'Exposure',      'order' => 1],
            ['file' => 'logo-climbwork-box.png',     'mime' => 'image/png',    'slug' => 'climb-work',    'name' => 'ClimbWork',     'order' => 2],
            ['file' => 'black-diamond-logo-box.png', 'mime' => 'image/png',    'slug' => 'black-diamond', 'name' => 'Black Diamond', 'order' => 3],
        ];

        foreach ($tiles as $t) {
            $path = 'sponsors/logos/' . $t['file'];

            $media = LegacyMedia::firstOrCreate(
                ['path' => $path],
                [
                    'disk'      => 'public',
                    'file_name' => $t['file'],
                    'mime_type' => $t['mime'],
                    'size'      => filesize(storage_path('app/public/' . $path)) ?: 0,
                    'alt'       => $t['name'],
                    'title'     => $t['name'],
                ]
            );

            $sponsor = Sponsor::firstOrCreate(
                ['slug' => $t['slug']],
                ['name' => $t['name'], 'status' => 'active']
            );
            $sponsor->update(['logo_media_id' => $media->id]);

            $exists = SponsorPlacement::where('sponsor_id', $sponsor->id)
                ->where('placement', 'home_tiles')
                ->exists();

            if (!$exists) {
                SponsorPlacement::create([
                    'sponsor_id' => $sponsor->id,
                    'placement'  => 'home_tiles',
                    'sort_order' => $t['order'],
                    'is_active'  => true,
                ]);
                $this->command->info('Creado: ' . $t['name']);
            } else {
                $this->command->info('Ya existía: ' . $t['name']);
            }
        }
    }
}
