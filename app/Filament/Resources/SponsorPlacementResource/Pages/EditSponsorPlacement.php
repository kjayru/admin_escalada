<?php

namespace App\Filament\Resources\SponsorPlacementResource\Pages;

use App\Filament\Resources\SponsorPlacementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSponsorPlacement extends EditRecord
{
    protected static string $resource = SponsorPlacementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
