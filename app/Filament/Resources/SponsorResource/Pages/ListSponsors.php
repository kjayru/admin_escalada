<?php

namespace App\Filament\Resources\SponsorResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\SponsorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSponsors extends ListRecords
{
    protected static string $resource = SponsorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
