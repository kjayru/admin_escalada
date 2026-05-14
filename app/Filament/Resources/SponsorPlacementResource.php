<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Slimani\MediaManager\Form\MediaPicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\SponsorPlacementResource\Pages\ListSponsorPlacements;
use App\Filament\Resources\SponsorPlacementResource\Pages\CreateSponsorPlacement;
use App\Filament\Resources\SponsorPlacementResource\Pages\EditSponsorPlacement;
use App\Filament\Resources\SponsorPlacementResource\Pages;
use App\Models\SponsorPlacement;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SponsorPlacementResource extends Resource
{
    protected static ?string $model = SponsorPlacement::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-photo';

    protected static string | \UnitEnum | null $navigationGroup = 'Patrocinadores';

    protected static ?string $modelLabel = 'Ubicación de Patrocinador';

    protected static ?string $pluralModelLabel = 'Sponsor Placements';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Asignación')
                    ->schema([
                        Select::make('sponsor_id')
                            ->label('Patrocinador')
                            ->relationship('sponsor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('placement')
                            ->label('Ubicación')
                            ->options([
                                'hero'                 => 'Hero (slider principal)',
                                'otros_patrocinadores' => 'Otros Patrocinadores (Home)',
                                'home_tiles'           => 'Tiles de Inicio',
                                'logo_row'             => 'Fila de Logos (Home)',
                                'prefooter_card'       => 'Cards Pre-Footer (Home)',
                                'sidebar'              => 'Barra Lateral',
                                'footer'               => 'Pie de Página',
                                'blog'                 => 'Blog',
                                'como_apoyar'          => 'Cómo Apoyar',
                            ])
                            ->required(),
                        TextInput::make('title')
                            ->label('Título')
                            ->maxLength(255),
                        Textarea::make('body')
                            ->label('Texto / Descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Banner y Enlace')
                    ->schema([
                        MediaPicker::make('banner_media_id')
                            ->label('Banner / Imagen')
                            ->nullable()
                            ->dehydrated(fn ($state) => filled($state)),
                        TextInput::make('link_url')
                            ->label('URL de Enlace')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Visibilidad')
                    ->schema([
                        DateTimePicker::make('start_at')
                            ->label('Inicio')
                            ->suffixAction(
                                Action::make('clear_start_at')
                                    ->icon('heroicon-o-x-mark')
                                    ->tooltip('Limpiar fecha')
                                    ->action(fn ($set) => $set('start_at', null))
                            ),
                        DateTimePicker::make('end_at')
                            ->label('Fin')
                            ->suffixAction(
                                Action::make('clear_end_at')
                                    ->icon('heroicon-o-x-mark')
                                    ->tooltip('Limpiar fecha')
                                    ->action(fn ($set) => $set('end_at', null))
                            ),
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
                TextColumn::make('sponsor.name')
                    ->label('Patrocinador')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('placement')
                    ->label('Ubicación')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('start_at')
                    ->label('Inicio')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('end_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('placement')
                    ->label('Ubicación')
                    ->options([
                        'hero'                 => 'Hero',
                        'otros_patrocinadores' => 'Otros Patrocinadores (Home)',
                        'home_tiles'           => 'Tiles de Inicio',
                        'logo_row'             => 'Fila de Logos (Home)',
                        'prefooter_card'       => 'Cards Pre-Footer (Home)',
                        'sidebar'              => 'Barra Lateral',
                        'footer'               => 'Pie de Página',
                        'blog'                 => 'Blog',
                        'como_apoyar'          => 'Cómo Apoyar',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Activo'),
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
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListSponsorPlacements::route('/'),
            'create' => CreateSponsorPlacement::route('/create'),
            'edit'   => EditSponsorPlacement::route('/{record}/edit'),
        ];
    }
}
