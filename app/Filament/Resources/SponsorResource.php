<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Slimani\MediaManager\Form\MediaPicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\SponsorResource\Pages\ListSponsors;
use App\Filament\Resources\SponsorResource\Pages\CreateSponsor;
use App\Filament\Resources\SponsorResource\Pages\EditSponsor;
use App\Filament\Resources\SponsorResource\Pages;
use App\Models\Sponsor;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SponsorResource extends Resource
{
    protected static ?string $model = Sponsor::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-star';

    protected static string | \UnitEnum | null $navigationGroup = 'Patrocinadores';

    protected static ?string $modelLabel = 'Patrocinador';

    protected static ?string $pluralModelLabel = 'Patrocinadores';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Información básica ──────────────────────────────────────
                Section::make('Información del Patrocinador')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del patrocinador')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Ej: climb-work → /patrocinador/climb-work'),
                        Textarea::make('tagline')
                            ->label('Tagline')
                            ->rows(2)
                            ->maxLength(300)
                            ->placeholder('Vinculamos el mundo de la joyería con las montañas.')
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Descripción (cuerpo del artículo)')
                            ->rows(5)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label('Estado')
                            ->options(['active' => 'Activo', 'inactive' => 'Inactivo'])
                            ->required()
                            ->default('active'),
                    ])->columns(2),

                // ── Logo e imágenes ──────────────────────────────────────────
                Section::make('Logo e imágenes')
                    ->schema([
                        MediaPicker::make('logo_media_id')
                            ->label('Logo del patrocinador')
                            ->nullable()
                            ->helperText('Imagen del logo que aparece en la cabecera y en el slider de la Home'),
                        MediaPicker::make('slide_image_media_id')
                            ->label('Imagen de fondo (Slider Home)')
                            ->nullable()
                            ->helperText('Imagen de fondo que aparece en el slider de patrocinadores en la Home'),
                    ])->columns(2),

                // ── Galería del slider ───────────────────────────────────────
                Section::make('Galería del slider (hasta 4 imágenes)')
                    ->description('Estas imágenes aparecen en el slider de la página del patrocinador.')
                    ->schema([
                        MediaPicker::make('gallery_1_media_id')
                            ->label('Imagen 1 (principal)')
                            ->nullable(),
                        MediaPicker::make('gallery_2_media_id')
                            ->label('Imagen 2')
                            ->nullable(),
                        MediaPicker::make('gallery_3_media_id')
                            ->label('Imagen 3')
                            ->nullable(),
                        MediaPicker::make('gallery_4_media_id')
                            ->label('Imagen 4')
                            ->nullable(),
                    ])->columns(2),

                // ── Tarjeta del representante ────────────────────────────────
                Section::make('Tarjeta del representante')
                    ->description('Aparece como tarjeta lateral en la página del patrocinador.')
                    ->schema([
                        TextInput::make('contact_name')
                            ->label('Nombre del representante')
                            ->maxLength(255)
                            ->placeholder('Ej: Uriel Torres'),
                        TextInput::make('contact_title')
                            ->label('Cargo / Rol')
                            ->maxLength(255)
                            ->placeholder('Ej: Principal Sponsor'),
                        Textarea::make('contact_text')
                            ->label('Texto del representante')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        MediaPicker::make('contact_media_id')
                            ->label('Foto del representante (circular)')
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),

                // ── Redes sociales ───────────────────────────────────────────
                Section::make('Redes sociales')
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://facebook.com/...')
                            ->prefixIcon('heroicon-o-globe-alt'),
                        TextInput::make('twitter_url')
                            ->label('X (Twitter)')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://x.com/...')
                            ->prefixIcon('heroicon-o-globe-alt'),
                        TextInput::make('email')
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
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('website_url')
                    ->label('Sitio Web')
                    ->url(fn ($record) => $record->website_url)
                    ->openUrlInNewTab()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(['active' => 'Activo', 'inactive' => 'Inactivo']),
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
            ->defaultSort('name');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListSponsors::route('/'),
            'create' => CreateSponsor::route('/create'),
            'edit'   => EditSponsor::route('/{record}/edit'),
        ];
    }
}
