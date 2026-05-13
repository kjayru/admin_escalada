<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Slimani\MediaManager\Form\MediaPicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\HomeTilesResource\Pages\ListHomeTiles;
use App\Filament\Resources\HomeTilesResource\Pages\CreateHomeTile;
use App\Filament\Resources\HomeTilesResource\Pages\EditHomeTile;
use App\Models\SponsorPlacement;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HomeTilesResource extends Resource
{
    protected static ?string $model = SponsorPlacement::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string | \UnitEnum | null $navigationGroup = 'Patrocinadores';

    protected static ?string $modelLabel = 'Tile de Inicio';

    protected static ?string $pluralModelLabel = 'Tiles de Inicio (Home)';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('placement', 'home_tiles');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('placement')->default('home_tiles'),

                Section::make('Patrocinador')
                    ->schema([
                        Select::make('sponsor_id')
                            ->label('Patrocinador')
                            ->relationship('sponsor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('link_url')
                            ->label('URL de enlace (opcional)')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Imagen del Tile')
                    ->description('Si se sube un banner, se mostrará en lugar del logo del patrocinador.')
                    ->schema([
                        MediaPicker::make('banner_media_id')
                            ->label('Banner / Imagen del Tile')
                            ->nullable()
                            ->dehydrated(fn ($state) => filled($state)),
                    ]),

                Section::make('Visibilidad')
                    ->schema([
                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sponsor.name')
                    ->label('Patrocinador')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('link_url')
                    ->label('URL')
                    ->toggleable()
                    ->limit(40),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListHomeTiles::route('/'),
            'create' => CreateHomeTile::route('/create'),
            'edit'   => EditHomeTile::route('/{record}/edit'),
        ];
    }
}
