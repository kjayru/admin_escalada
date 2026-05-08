<?php

namespace App\Filament\Resources\LogoRowResource\Pages;

use App\Filament\Resources\LogoRowResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLogoRows extends ListRecords
{
    protected static string $resource = LogoRowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
