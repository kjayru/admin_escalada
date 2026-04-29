<?php

namespace App\Filament\Resources\TransparencyDocumentResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\TransparencyDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransparencyDocuments extends ListRecords
{
    protected static string $resource = TransparencyDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
