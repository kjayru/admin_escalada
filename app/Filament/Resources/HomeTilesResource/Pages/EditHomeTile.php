<?php

namespace App\Filament\Resources\HomeTilesResource\Pages;

use App\Filament\Resources\HomeTilesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeTile extends EditRecord
{
    protected static string $resource = HomeTilesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
