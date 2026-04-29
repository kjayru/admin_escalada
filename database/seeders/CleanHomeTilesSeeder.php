<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use App\Models\SponsorPlacement;
use Illuminate\Database\Seeder;

class CleanHomeTilesSeeder extends Seeder
{
    public function run(): void
    {
        $realSlugs = ['exposure', 'climb-work', 'black-diamond'];
        $realIds = Sponsor::whereIn('slug', $realSlugs)->pluck('id');
        $deleted = SponsorPlacement::where('placement', 'home_tiles')
            ->whereNotIn('sponsor_id', $realIds)
            ->delete();
        $this->command->info("Eliminados: $deleted placements de prueba");
        $this->command->info('Restantes: ' . SponsorPlacement::where('placement', 'home_tiles')->count());
    }
}
