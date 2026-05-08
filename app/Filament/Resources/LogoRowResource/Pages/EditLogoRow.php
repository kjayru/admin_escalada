<?php

namespace App\Filament\Resources\LogoRowResource\Pages;

use App\Filament\Resources\LogoRowResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLogoRow extends EditRecord
{
    protected static string $resource = LogoRowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
