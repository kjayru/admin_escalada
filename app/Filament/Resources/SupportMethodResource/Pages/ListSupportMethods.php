<?php

namespace App\Filament\Resources\SupportMethodResource\Pages;

use App\Filament\Resources\SupportMethodResource;
use Filament\Resources\Pages\ListRecords;

class ListSupportMethods extends ListRecords
{
    protected static string $resource = SupportMethodResource::class;

    // Sin botón "Crear nuevo" — los bloques son fijos
    protected function getHeaderActions(): array
    {
        return [];
    }
}
