<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ActivityResource;
use Filament\Resources\Pages\EditRecord;

class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
