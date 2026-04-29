<?php

namespace App\Filament\Resources\MemberGroupResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Slimani\MediaManager\Form\MediaPicker;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Miembros';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre completo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('role')
                    ->label('Cargo / Rol')
                    ->maxLength(100)
                    ->placeholder('Ej. Presidente, Secretario...'),
                Textarea::make('bio')
                    ->label('Descripción / Bio')
                    ->rows(3)
                    ->nullable()
                    ->columnSpanFull(),
                Toggle::make('featured_home')
                    ->label('Destacar en home')
                    ->default(false)
                    ->helperText('Aparecerá en la sección del equipo en la página principal'),
                MediaPicker::make('featured_media_id')
                    ->label('Foto')
                    ->nullable()
                    ->helperText('Selecciona o sube una imagen desde la Biblioteca de Medios')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'active'   => 'Activo',
                        'inactive' => 'Inactivo',
                    ])
                    ->default('active'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Cargo')
                    ->toggleable(),
                IconColumn::make('featured_home')
                    ->label('Home')
                    ->boolean(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'inactive' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
