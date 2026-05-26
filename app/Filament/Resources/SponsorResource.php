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
use Filament\Tables\Columns\IconColumn;
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
                // ── 1 & 5. Información básica ────────────────────────────────
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
                            ->label('Tagline (texto debajo del logo en slider)')
                            ->rows(2)
                            ->maxLength(300)
                            ->placeholder('Vinculamos el mundo de la joyería con las montañas.')
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Texto extenso (landing del patrocinador)')
                            ->rows(6)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label('Estado')
                            ->options(['active' => 'Activo', 'inactive' => 'Inactivo'])
                            ->required()
                            ->default('active'),
                        TextInput::make('website_url')
                            ->label('Sitio web')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://www.patrocinador.com'),
                        TextInput::make('buy_url')
                            ->label('URL botón "Comprar aquí"')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://tienda.patrocinador.com/producto')
                            ->helperText('URL a la que apunta el botón "Comprar aquí" en el landing'),
                    ])->columns(2),

                // ── 1. Logo para boxes rectangulares ────────────────────────
                Section::make('Logos')
                    ->description('Logos del patrocinador para diferentes secciones del sitio.')
                    ->schema([
                        MediaPicker::make('logo_media_id')
                            ->label('1. Logo para boxes rectangulares')
                            ->nullable()
                            ->helperText('Dimensiones recomendadas: 600×400 px (horizontal) o 400×600 px (vertical). Aparece en boxes de 547px altura en la Home.'),
                        MediaPicker::make('circle_logo_media_id')
                            ->label('2. Logo circular para el slider')
                            ->nullable()
                            ->helperText('Dimensiones recomendadas: 256×256 px (cuadrado). Se mostrará a 256px de ancho en el slider de la Home.'),
                        MediaPicker::make('section_logo_media_id')
                            ->label('5. Logo de la sección (cabecera del landing)')
                            ->nullable()
                            ->helperText('Dimensiones recomendadas: ancho variable × 94px altura. Aparece centrado en la parte superior del landing.'),
                    ])->columns(2),

                // ── 4. Fondo del slider ──────────────────────────────────────
                Section::make('Fondo del slider de patrocinadores (Home)')
                    ->description('Imagen de fondo que aparece detrás del logo y el texto en el slider de la Home.')
                    ->schema([
                        MediaPicker::make('slide_image_media_id')
                            ->label('4. Imagen de fondo del slider')
                            ->nullable()
                            ->helperText('Dimensiones recomendadas: 1920×1080 px o superior. Formato panorámico para hero/slider full width.')
                            ->columnSpanFull(),
                    ]),

                // ── 6 & 7. Galería principal ─────────────────────────────────
                Section::make('Galería de imágenes (landing del patrocinador)')
                    ->description('Imágenes del slider principal debajo del logo en el landing. La imagen destacada aparece en el box "¿Te gustó este producto?".')
                    ->schema([
                        MediaPicker::make('gallery_1_media_id')
                            ->label('Imagen 1 (principal)')
                            ->nullable()
                            ->helperText('Dimensiones recomendadas: 1127×670 px. Imagen principal del slider de producto.'),
                        MediaPicker::make('gallery_2_media_id')
                            ->label('Imagen 2')
                            ->nullable()
                            ->helperText('Dimensiones recomendadas: 1127×670 px. Segunda imagen del slider.'),
                        MediaPicker::make('gallery_3_media_id')
                            ->label('Imagen 3')
                            ->nullable()
                            ->helperText('Dimensiones recomendadas: 1127×670 px. Tercera imagen del slider.'),
                        MediaPicker::make('gallery_4_media_id')
                            ->label('Imagen 4')
                            ->nullable()
                            ->helperText('Dimensiones recomendadas: 1127×670 px. Cuarta imagen del slider.'),
                        MediaPicker::make('highlight_media_id')
                            ->label('7. Imagen destacada para box "¿Te gustó este producto?"')
                            ->nullable()
                            ->helperText('Dimensiones recomendadas: 220×220 px (cuadrado). Se mostrará circular. Si no se define, usa la Imagen 1.')
                            ->columnSpanFull(),
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

                // ── SEO / Open Graph ─────────────────────────────────────────
                Section::make('SEO / Open Graph')
                    ->description('Configura cómo se verá esta página al compartirla en redes sociales (Facebook, WhatsApp, Twitter, etc.).')
                    ->schema([
                        TextInput::make('og_title')
                            ->label('og:title')
                            ->maxLength(255)
                            ->placeholder('Ej: Valley Plastic & Paper Recycling – Patrocinador Escalada Libre')
                            ->helperText('Título que aparece al compartir en redes. Si se deja vacío, se usa el nombre del patrocinador.')
                            ->columnSpanFull(),
                        Textarea::make('og_description')
                            ->label('og:description')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Ej: Descubre cómo Valley Plastic apoya la escalada libre y el cuidado del medio ambiente.')
                            ->helperText('Descripción que aparece al compartir en redes. Si se deja vacío, se usa el tagline o la descripción.')
                            ->columnSpanFull(),
                        MediaPicker::make('og_image_media_id')
                            ->label('og:image')
                            ->nullable()
                            ->helperText('Imagen para redes sociales. Recomendado: 1200×630 px (horizontal). Si se deja vacío, se usa el logo del patrocinador.')
                            ->columnSpanFull(),
                        TextInput::make('og_image_width')
                            ->label('og:image:width')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(9999)
                            ->placeholder('1200')
                            ->helperText('Ancho en píxeles de la imagen OG.'),
                        TextInput::make('og_image_height')
                            ->label('og:image:height')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(9999)
                            ->placeholder('630')
                            ->helperText('Alto en píxeles de la imagen OG.'),
                    ])->columns(2),
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
                IconColumn::make('slide_image_media_id')
                    ->label('En slider')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->slide_image_media_id))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
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
