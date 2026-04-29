<?php

namespace App\Filament\Resources\TimelineResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\TimelineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimeline extends EditRecord
{
    protected static string $resource = TimelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
