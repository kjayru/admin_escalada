<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorPlacementResource\Pages;
use App\Models\SponsorPlacement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SponsorPlacementResource extends Resource
{
    protected static ?string $model = SponsorPlacement::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Patrocinadores';

    protected static ?string $modelLabel = 'Ubicación de Patrocinador';

    protected static ?string $pluralModelLabel = 'Sponsor Placements';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asignación')
                    ->schema([
                        Forms\Components\Select::make('sponsor_id')
                            ->label('Patrocinador')
                            ->relationship('sponsor', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('placement')
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
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('body')
                            ->label('Texto / Descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Banner y Enlace')
                    ->schema([
                        Forms\Components\Select::make('banner_media_id')
                            ->label('Banner / Imagen')
                            ->relationship('bannerMedia', 'file_name')
                            ->searchable()
                            ->nullable(),
                        Forms\Components\TextInput::make('link_url')
                            ->label('URL de Enlace')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Visibilidad')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_at')
                            ->label('Inicio'),
                        Forms\Components\DateTimePicker::make('end_at')
                            ->label('Fin'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sponsor.name')
                    ->label('Patrocinador')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('placement')
                    ->label('Ubicación')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Inicio')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('placement')
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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSponsorPlacements::route('/'),
            'create' => Pages\CreateSponsorPlacement::route('/create'),
            'edit'   => Pages\EditSponsorPlacement::route('/{record}/edit'),
        ];
    }
}
