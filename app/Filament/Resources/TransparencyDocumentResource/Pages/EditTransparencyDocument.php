<?php

namespace App\Filament\Resources\TransparencyDocumentResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\TransparencyDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransparencyDocument extends EditRecord
{
    protected static string $resource = TransparencyDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
