<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Mensajes';

    protected static ?string $modelLabel = 'Mensaje';

    protected static ?string $pluralModelLabel = 'Mensajes de Contacto';

    protected static ?int $navigationSort = 1;

    // Sin creación manual — los mensajes llegan del formulario web
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nombre')->disabled(),
                Forms\Components\TextInput::make('email')->label('Email')->disabled(),
                Forms\Components\TextInput::make('phone')->label('Teléfono')->disabled(),
                Forms\Components\TextInput::make('subject')->label('Asunto')->disabled()->columnSpanFull(),
                Forms\Components\Textarea::make('message')->label('Mensaje')->disabled()->rows(5)->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options(['new' => 'Nuevo', 'read' => 'Leído', 'replied' => 'Respondido'])
                    ->required(),
            ])->columns(3);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Datos del Remitente')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')->label('Nombre'),
                        Infolists\Components\TextEntry::make('email')->label('Email'),
                        Infolists\Components\TextEntry::make('phone')->label('Teléfono'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'new'     => 'danger',
                                'read'    => 'warning',
                                'replied' => 'success',
                                default   => 'gray',
                            }),
                    ])->columns(4),
                Infolists\Components\Section::make('Mensaje')
                    ->schema([
                        Infolists\Components\TextEntry::make('subject')->label('Asunto')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('message')->label('Contenido')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Recibido')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Asunto')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'new'     => 'danger',
                        'read'    => 'warning',
                        'replied' => 'success',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'new'     => 'Nuevo',
                        'read'    => 'Leído',
                        'replied' => 'Respondido',
                        default   => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recibido')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(['new' => 'Nuevo', 'read' => 'Leído', 'replied' => 'Respondido']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('mark_read')
                    ->label('Marcar Leído')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->action(fn (ContactMessage $record) => $record->update(['status' => 'read']))
                    ->visible(fn (ContactMessage $record) => $record->status === 'new'),
                Tables\Actions\Action::make('mark_replied')
                    ->label('Respondido')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (ContactMessage $record) => $record->update(['status' => 'replied']))
                    ->visible(fn (ContactMessage $record) => in_array($record->status, ['new', 'read'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_read_bulk')
                        ->label('Marcar Leídos')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each->update(['status' => 'read'])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view'  => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
