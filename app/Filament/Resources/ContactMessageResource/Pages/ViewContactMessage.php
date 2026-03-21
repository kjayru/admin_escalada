<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_read')
                ->label('Marcar como Leído')
                ->icon('heroicon-o-eye')
                ->color('warning')
                ->action(fn () => $this->record->update(['status' => 'read']))
                ->visible(fn () => $this->record->status === 'new'),
            Actions\Action::make('mark_replied')
                ->label('Marcar como Respondido')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(fn () => $this->record->update(['status' => 'replied']))
                ->visible(fn () => in_array($this->record->status, ['new', 'read'])),
            Actions\DeleteAction::make(),
        ];
    }
}
