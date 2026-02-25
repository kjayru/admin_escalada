<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorPlacementResource\Pages;
use App\Filament\Resources\SponsorPlacementResource\RelationManagers;
use App\Models\SponsorPlacement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SponsorPlacementResource extends Resource
{
    protected static ?string $model = SponsorPlacement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sponsor_id')
                    ->relationship('sponsor', 'name')
                    ->required(),
                Forms\Components\TextInput::make('placement')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('body')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('banner_media_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('link_url')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DateTimePicker::make('start_at'),
                Forms\Components\DateTimePicker::make('end_at'),
                Forms\Components\TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sponsor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('placement')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('banner_media_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('link_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSponsorPlacements::route('/'),
            'create' => Pages\CreateSponsorPlacement::route('/create'),
            'edit' => Pages\EditSponsorPlacement::route('/{record}/edit'),
        ];
    }
}
