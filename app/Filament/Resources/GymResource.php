<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GymResource\Pages\CreateGym;
use App\Filament\Resources\GymResource\Pages\EditGym;
use App\Filament\Resources\GymResource\Pages\ListGyms;
use App\Models\Gym;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Slimani\MediaManager\Form\MediaPicker;

class GymResource extends Resource
{
    protected static ?string $model = Gym::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|\UnitEnum|null $navigationGroup = 'Cómo Apoyar';

    protected static ?string $modelLabel = 'Gimnasio';

    protected static ?string $pluralModelLabel = 'Gimnasios';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información del Gimnasio')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre del gimnasio')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('address')
                        ->label('Dirección')
                        ->maxLength(500),
                    TextInput::make('website_url')
                        ->label('URL del sitio web')
                        ->url()
                        ->placeholder('https://ejemplo.com')
                        ->maxLength(500),
                    Toggle::make('is_active')
                        ->label('Activo')
                        ->default(true),
                ])
                ->columns(2),

            Section::make('Logo')
                ->schema([
                    MediaPicker::make('logo_media_id')
                        ->label('Logo del gimnasio')
                        ->nullable()
                        ->helperText('Sube el logo del gimnasio. Se mostrará en la lista de la página /como-apoyar/gyms.'),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Dirección')
                    ->limit(50),
                TextColumn::make('website_url')
                    ->label('Sitio web')
                    ->limit(40),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListGyms::route('/'),
            'create' => CreateGym::route('/create'),
            'edit'   => EditGym::route('/{record}/edit'),
        ];
    }
}
