<?php

namespace App\Filament\Resources\HomeTilesResource\Pages;

use App\Filament\Resources\HomeTilesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeTile extends CreateRecord
{
    protected static string $resource = HomeTilesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['placement'] = 'home_tiles';
        return $data;
    }
}
