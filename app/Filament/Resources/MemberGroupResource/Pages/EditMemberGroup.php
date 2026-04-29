<?php

namespace App\Filament\Resources\MemberGroupResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\MemberGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemberGroup extends EditRecord
{
    protected static string $resource = MemberGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
