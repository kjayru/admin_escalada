<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ContactMessageResource\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessageResource\Pages\ViewContactMessage;
use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static string | \UnitEnum | null $navigationGroup = 'Mensajes';

    protected static ?string $modelLabel = 'Mensaje';

    protected static ?string $pluralModelLabel = 'Mensajes de Contacto';

    protected static ?int $navigationSort = 1;

    // Sin creación manual — los mensajes llegan del formulario web
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nombre')->disabled(),
                TextInput::make('email')->label('Email')->disabled(),
                TextInput::make('phone')->label('Teléfono')->disabled(),
                TextInput::make('subject')->label('Asunto')->disabled()->columnSpanFull(),
                Textarea::make('message')->label('Mensaje')->disabled()->rows(5)->columnSpanFull(),
                Select::make('status')
                    ->label('Estado')
                    ->options(['new' => 'Nuevo', 'read' => 'Leído', 'replied' => 'Respondido'])
                    ->required(),
            ])->columns(3);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del Remitente')
                    ->schema([
                        TextEntry::make('name')->label('Nombre'),
                        TextEntry::make('email')->label('Email'),
                        TextEntry::make('phone')->label('Teléfono'),
                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'new'     => 'danger',
                                'read'    => 'warning',
                                'replied' => 'success',
                                default   => 'gray',
                            }),
                    ])->columns(4),
                Section::make('Mensaje')
                    ->schema([
                        TextEntry::make('subject')->label('Asunto')->columnSpanFull(),
                        TextEntry::make('message')->label('Contenido')->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Recibido')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('subject')
                    ->label('Asunto')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('status')
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
                TextColumn::make('created_at')
                    ->label('Recibido')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(['new' => 'Nuevo', 'read' => 'Leído', 'replied' => 'Respondido']),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('mark_read')
                    ->label('Marcar Leído')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->action(fn (ContactMessage $record) => $record->update(['status' => 'read']))
                    ->visible(fn (ContactMessage $record) => $record->status === 'new'),
                Action::make('mark_replied')
                    ->label('Respondido')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (ContactMessage $record) => $record->update(['status' => 'replied']))
                    ->visible(fn (ContactMessage $record) => in_array($record->status, ['new', 'read'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_read_bulk')
                        ->label('Marcar Leídos')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each->update(['status' => 'read'])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view'  => ViewContactMessage::route('/{record}'),
        ];
    }
}
