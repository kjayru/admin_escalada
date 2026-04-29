<?php

namespace App\Filament\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Slimani\MediaManager\Form\MediaPicker;

class ContentBlockSchema
{
    /**
     * Retorna el Repeater de bloques/secciones reutilizable en cualquier Resource.
     *
     * @param  string  $relationship  Nombre de la relación Eloquent (default: 'sections')
     */
    public static function repeater(string $relationship = 'sections'): Repeater
    {
        return Repeater::make($relationship)
            ->label('')
            ->relationship($relationship)
            ->schema([
                Select::make('type')
                    ->label('Tipo de Bloque')
                    ->options([
                        'hero'     => 'Hero',
                        'text'     => 'Texto',
                        'gallery'  => 'Galería',
                        'cards'    => 'Tarjetas',
                        'timeline' => 'Línea de Tiempo',
                        'cta'      => 'Call to Action',
                        'split'    => 'Split (Texto + Imagen)',
                        'map'      => 'Mapa',
                    ])
                    ->required(),
                TextInput::make('heading')
                    ->label('Encabezado')
                    ->maxLength(255),
                TextInput::make('subheading')
                    ->label('Subencabezado')
                    ->maxLength(255),
                RichEditor::make('body')
                    ->label('Contenido')
                    ->columnSpanFull(),
                MediaPicker::make('featured_media_id')
                    ->label('Imagen principal')
                    ->nullable()
                    ->rule('nullable')
                    ->columnSpanFull()
                    ->afterStateHydrated(function (MediaPicker $component, mixed $state): void {
                        // Sanitize: if state ended up as an object (e.g. LocalFilesystemAdapter),
                        // reset to null to prevent str_starts_with TypeError on PHP 8.4+
                        if (! is_null($state) && ! is_scalar($state) && ! is_array($state)) {
                            $component->state(null);
                        }
                    }),
                KeyValue::make('settings')
                    ->label('Configuraciones')
                    ->helperText('Configuraciones adicionales en formato JSON')
                    ->afterStateHydrated(function (KeyValue $component, mixed $state): void {
                        if (is_string($state)) {
                            $decoded = json_decode($state, true);
                            $component->state(is_array($decoded) ? $decoded : []);
                        }
                    }),
                Repeater::make('items')
                    ->label('Ítems / Bullets')
                    ->relationship()
                    ->schema([
                        TextInput::make('title')
                            ->label('Texto')
                            ->required()
                            ->maxLength(500),
                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->reorderable('sort_order')
                    ->collapsible()
                    ->collapsed()
                    ->addActionLabel('Agregar ítem')
                    ->columnSpanFull(),
                MediaPicker::make('galleryFiles')
                    ->label('Imágenes del mosaico (solo tipo gallery)')
                    ->relationship('galleryFiles')
                    ->multiple()
                    ->visible(fn (Get $get) => $get('type') === 'gallery')
                    ->columnSpanFull()
                    ->helperText('Selecciona las imágenes para el mosaico desde la Biblioteca de Medios.'),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'active'   => 'Activo',
                        'inactive' => 'Inactivo',
                    ])
                    ->default('active'),
            ])
            ->itemLabel(fn (array $state): ?string => $state['heading'] ?? 'Nuevo Bloque')
            ->collapsed()
            ->collapsible()
            ->orderColumn('sort_order')
            ->reorderable()
            ->defaultItems(0)
            ->addActionLabel('Agregar Bloque');
    }
}
