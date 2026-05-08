<?php

namespace App\Filament\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
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
                        'featured' => 'Sección Destacada (Número)',
                        'split'    => 'Split (Texto + Imagen)',
                        'join'     => 'Únete al equipo',
                        'map'      => 'Mapa',
                    ])
                    ->required()
                    ->live(),
                // ── Campos exclusivos del tipo "Sección Destacada (Número)" ──────────
                // IMPORTANTE: No usar settings.X como state path — conflicto con
                // KeyValueStateCast que convierte settings a formato pairs [{key,value}].
                // Usamos state paths independientes (settings_number, etc.) y guardamos
                // via el mutador featuredSettingsData en el modelo PageSection.
                Section::make('Configuración de Sección Destacada')
                    ->schema([
                        TextInput::make('settings_number')
                            ->label('Número decorativo (ej: 01)')
                            ->maxLength(10)
                            ->helperText('Número grande que aparece de fondo, ej: 01, 02, 03')
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component) {
                                $record = $component->getRecord();
                                $component->state($record?->settings['number'] ?? null);
                            }),
                        TextInput::make('settings_tag')
                            ->label('Texto amarillo (ej: NOSOTROS)')
                            ->maxLength(80)
                            ->helperText('Etiqueta en mayúsculas que aparece sobre el título')
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component) {
                                $record = $component->getRecord();
                                $component->state($record?->settings['tag'] ?? null);
                            }),
                        TextInput::make('settings_link_url')
                            ->label('URL del "Ver más"')
                            ->maxLength(500)
                            ->helperText('Ruta interna (/nosotros) o URL externa (https://...)')
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component) {
                                $record = $component->getRecord();
                                $component->state($record?->settings['link_url'] ?? null);
                            }),
                        Select::make('settings_image_position')
                            ->label('Posición de la imagen')
                            ->options([
                                'right' => 'Derecha (texto a la izquierda)',
                                'left'  => 'Izquierda (texto a la derecha)',
                            ])
                            ->default('right')
                            ->helperText('Determina en qué lado se muestra la imagen en escritorio')
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component) {
                                $record = $component->getRecord();
                                $component->state($record?->settings['image_position'] ?? 'right');
                            }),
                    ])
                    ->columns(2)
                    ->visible(fn (Get $get): bool => $get('type') === 'featured'),
                // Campo virtual que recoge los 4 campos featured y los guarda en settings
                // via el mutador PageSection::setFeaturedSettingsDataAttribute()
                Hidden::make('featured_settings_data')
                    ->dehydrated(fn (Get $get): bool => $get('type') === 'featured')
                    ->dehydrateStateUsing(fn (Get $get): array => [
                        'number'         => $get('settings_number'),
                        'tag'            => $get('settings_tag'),
                        'link_url'       => $get('settings_link_url'),
                        'image_position' => $get('settings_image_position') ?? 'right',
                    ]),
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
                        // Load from record attribute if Livewire snapshot has blank state
                        if (blank($state)) {
                            $record = $component->getRecord();
                            if ($record) {
                                $state = $record->getAttribute($component->getName());
                            }
                        }

                        // Non-scalar/non-array objects → clear
                        if (! is_null($state) && ! is_scalar($state) && ! is_array($state)) {
                            $component->state(null);
                            return;
                        }

                        // Scalar (int/string) → pass through state() so FileUploadStateCast::set()
                        // wraps it as {uuid=>'86'}, enabling FilePond's getUploadedFiles() to work.
                        if (is_scalar($state) && ! blank($state)) {
                            $component->state((string) $state);
                            return;
                        }

                        // Array → extract first non-empty scalar value and pass through state()
                        if (is_array($state) && filled($state)) {
                            $val = array_values(array_filter(array_map(
                                fn($v) => is_scalar($v) ? (string) $v : null,
                                $state
                            )))[0] ?? null;
                            if ($val) {
                                $component->state($val);
                                return;
                            }
                        }

                        $component->state(null);
                    })
                    ->dehydrateStateUsing(function (MediaPicker $component, $state) {
                        // $state can be null here because Schema::getState() calls validate() which
                        // runs pruneStateToMatchKeys() using only validated fields as template.
                        // Since MediaPicker.getValidationRules() = [], featured_media_id is absent
                        // from the validated template and gets pruned out. dehydrateStateUsing then
                        // receives null from Arr::get($prunedState, statePath) → FileUploadStateCast::get(null).
                        //
                        // Fix: fall back to getRawState() which reads directly from $livewire->data,
                        // bypassing the pruning. getRawState() reflects intentional changes (clear/select)
                        // because removeUploadedFile/MediaPicker updates $livewire->data directly.
                        $source = filled($state) ? $state : $component->getRawState();
                        $values = array_values(
                            array_filter(array_map(
                                fn($v) => is_scalar($v) ? (string) $v : null,
                                (array) ($source ?? [])
                            ))
                        );
                        return $values[0] ?? null;
                    })
                    ->saveRelationshipsUsing(function (MediaPicker $component, $state): void {
                        // Safety net: saveRelationshipsUsing fires because isSaved=true by default.
                        // The Repeater's save (via $item->getState) now correctly uses dehydrateStateUsing
                        // with getRawState() fallback. This override ensures the value is also correct
                        // if saveRelationships runs independently (e.g. for new records).
                        $record = $component->getRecord();
                        if (! $record) {
                            return;
                        }
                        $source = filled($state) ? $state : $component->getRawState();
                        $values = array_values(
                            array_filter(array_map(
                                fn($v) => is_scalar($v) ? (string) $v : null,
                                (array) ($source ?? [])
                            ))
                        );
                        $id = $values[0] ?? null;
                        $name = $component->getName();
                        if ($record->{$name} != $id) {
                            $record->{$name} = $id;
                            $record->save();
                        }
                    }),
                MediaPicker::make('mobile_image_id')
                    ->label('Imagen para móviles')
                    ->helperText('Imagen de fondo que se mostrará en dispositivos móviles')
                    ->nullable()
                    ->rule('nullable')
                    ->columnSpanFull()
                    ->afterStateHydrated(function (MediaPicker $component, mixed $state): void {
                        if (blank($state)) {
                            $record = $component->getRecord();
                            if ($record) {
                                $state = $record->getAttribute($component->getName());
                            }
                        }
                        if (! is_null($state) && ! is_scalar($state) && ! is_array($state)) {
                            $component->state(null);
                            return;
                        }
                        if (is_scalar($state) && ! blank($state)) {
                            $component->state((string) $state);
                            return;
                        }
                        if (is_array($state) && filled($state)) {
                            $val = array_values(array_filter(array_map(
                                fn($v) => is_scalar($v) ? (string) $v : null,
                                $state
                            )))[0] ?? null;
                            if ($val) {
                                $component->state($val);
                                return;
                            }
                        }
                        $component->state(null);
                    })
                    ->dehydrateStateUsing(function (MediaPicker $component, $state) {
                        $source = filled($state) ? $state : $component->getRawState();
                        $values = array_values(
                            array_filter(array_map(
                                fn($v) => is_scalar($v) ? (string) $v : null,
                                (array) ($source ?? [])
                            ))
                        );
                        return $values[0] ?? null;
                    })
                    ->saveRelationshipsUsing(function (MediaPicker $component, $state): void {
                        $record = $component->getRecord();
                        if (! $record) {
                            return;
                        }
                        $source = filled($state) ? $state : $component->getRawState();
                        $values = array_values(
                            array_filter(array_map(
                                fn($v) => is_scalar($v) ? (string) $v : null,
                                (array) ($source ?? [])
                            ))
                        );
                        $id = $values[0] ?? null;
                        $name = $component->getName();
                        if ($record->{$name} != $id) {
                            $record->{$name} = $id;
                            $record->save();
                        }
                    }),
                KeyValue::make('settings')
                    ->label('Configuraciones')
                    ->helperText('Configuraciones adicionales en formato JSON')
                    ->visible(fn (Get $get): bool => $get('type') !== 'featured')
                    ->dehydrated(fn (Get $get): bool => $get('type') !== 'featured')
                    ->afterStateHydrated(function (KeyValue $component, mixed $state): void {
                        if (is_string($state)) {
                            $decoded = json_decode($state, true);
                            $component->state(is_array($decoded) ? $decoded : []);
                        } elseif (is_array($state)) {
                            // When model cast already decoded the JSON to a PHP array,
                            // we must still call $component->state() so Filament's KeyValue
                            // processes it into the format Alpine expects (JS array, not object).
                            // Without this, the raw PHP assoc array becomes a JS object and
                            // Alpine's key-value.js crashes with "Alpine.raw(...).map is not a function".
                            $component->state($state);
                        } else {
                            $component->state([]);
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
