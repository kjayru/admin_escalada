<?php

namespace App\Filament\Resources\HomeTilesResource\Pages;

use App\Filament\Resources\HomeTilesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeTiles extends ListRecords
{
    protected static string $resource = HomeTilesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
