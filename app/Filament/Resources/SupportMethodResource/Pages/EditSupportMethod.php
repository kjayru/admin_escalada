<?php

namespace App\Filament\Resources\SupportMethodResource\Pages;

use App\Filament\Resources\SupportMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupportMethod extends EditRecord
{
    protected static string $resource = SupportMethodResource::class;

    // Sin botón de eliminar — los bloques son fijos
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('← Volver al listado')
                ->url(SupportMethodResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
