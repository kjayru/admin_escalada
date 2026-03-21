<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorResource\Pages;
use App\Models\Sponsor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SponsorResource extends Resource
{
    protected static ?string $model = Sponsor::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Patrocinadores';

    protected static ?string $modelLabel = 'Patrocinador';

    protected static ?string $pluralModelLabel = 'Patrocinadores';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ── Información básica ──────────────────────────────────────
                Forms\Components\Section::make('Información del Patrocinador')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del patrocinador')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Ej: climb-work → /patrocinador/climb-work'),
                        Forms\Components\Textarea::make('tagline')
                            ->label('Tagline')
                            ->rows(2)
                            ->maxLength(300)
                            ->placeholder('Vinculamos el mundo de la joyería con las montañas.')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción (cuerpo del artículo)')
                            ->rows(5)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(['active' => 'Activo', 'inactive' => 'Inactivo'])
                            ->required()
                            ->default('active'),
                    ])->columns(2),

                // ── Logo e imágenes ──────────────────────────────────────────
                Forms\Components\Section::make('Logo e imágenes')
                    ->schema([
                        Forms\Components\Select::make('logo_media_id')
                            ->label('Logo del patrocinador')
                            ->relationship('logo', 'file_name')
                            ->searchable()
                            ->nullable()
                            ->helperText('Imagen del logo que aparece en la cabecera de la página'),
                        Forms\Components\Select::make('slide_image_media_id')
                            ->label('Imagen de fondo (Home Banner)')
                            ->relationship('slideImage', 'file_name')
                            ->searchable()
                            ->nullable()
                            ->helperText('Imagen del banner principal en la Home'),
                    ])->columns(2),

                // ── Galería del slider ───────────────────────────────────────
                Forms\Components\Section::make('Galería del slider (hasta 4 imágenes)')
                    ->description('Estas imágenes aparecen en el slider de la página del patrocinador.')
                    ->schema([
                        Forms\Components\Select::make('gallery_1_media_id')
                            ->label('Imagen 1 (principal)')
                            ->relationship('gallery1', 'file_name')
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Select::make('gallery_2_media_id')
                            ->label('Imagen 2')
                            ->relationship('gallery2', 'file_name')
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Select::make('gallery_3_media_id')
                            ->label('Imagen 3')
                            ->relationship('gallery3', 'file_name')
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Select::make('gallery_4_media_id')
                            ->label('Imagen 4')
                            ->relationship('gallery4', 'file_name')
                            ->searchable()
                            ->nullable(),
                    ])->columns(2),

                // ── Tarjeta del representante ────────────────────────────────
                Forms\Components\Section::make('Tarjeta del representante')
                    ->description('Aparece como tarjeta lateral en la página del patrocinador.')
                    ->schema([
                        Forms\Components\TextInput::make('contact_name')
                            ->label('Nombre del representante')
                            ->maxLength(255)
                            ->placeholder('Ej: Uriel Torres'),
                        Forms\Components\TextInput::make('contact_title')
                            ->label('Cargo / Rol')
                            ->maxLength(255)
                            ->placeholder('Ej: Principal Sponsor'),
                        Forms\Components\Textarea::make('contact_text')
                            ->label('Texto del representante')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('contact_media_id')
                            ->label('Foto del representante (circular)')
                            ->relationship('contactMedia', 'file_name')
                            ->searchable()
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),

                // ── Redes sociales ───────────────────────────────────────────
                Forms\Components\Section::make('Redes sociales')
                    ->schema([
                        Forms\Components\TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://facebook.com/...')
                            ->prefixIcon('heroicon-o-globe-alt'),
                        Forms\Components\TextInput::make('twitter_url')
                            ->label('X (Twitter)')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://x.com/...')
                            ->prefixIcon('heroicon-o-globe-alt'),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('contacto@patrocinador.com')
                            ->prefixIcon('heroicon-o-envelope'),
                    ])->columns(3),
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
                Tables\Columns\TextColumn::make('website_url')
                    ->label('Sitio Web')
                    ->url(fn ($record) => $record->website_url)
                    ->openUrlInNewTab()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(['active' => 'Activo', 'inactive' => 'Inactivo']),
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
            ->defaultSort('name');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSponsors::route('/'),
            'create' => Pages\CreateSponsor::route('/create'),
            'edit'   => Pages\EditSponsor::route('/{record}/edit'),
        ];
    }
}
