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
                        'join'     => 'Únete al equipo',
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
