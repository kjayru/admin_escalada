<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Media;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Contenido';
    
    protected static ?string $modelLabel = 'Página';
    
    protected static ?string $pluralModelLabel = 'Páginas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('URL amigable de la página'),
                        Forms\Components\Select::make('template')
                            ->label('Plantilla')
                            ->options([
                                'default' => 'Por Defecto',
                                'home' => 'Inicio',
                                'contact' => 'Contacto',
                                'landing' => 'Landing Page',
                            ])
                            ->required()
                            ->default('default'),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'published' => 'Publicado',
                            ])
                            ->required()
                            ->default('draft'),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Fecha de Publicación'),
                    ])->columns(2),
                
                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('Título SEO')
                            ->maxLength(255)
                            ->helperText('Máximo 60 caracteres'),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('Descripción SEO')
                            ->rows(3)
                            ->helperText('Máximo 160 caracteres'),
                    ])->collapsible(),
                
                Forms\Components\Section::make('Secciones')
                    ->schema([
                        Forms\Components\Repeater::make('sections')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Tipo de Sección')
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
                                Forms\Components\TextInput::make('heading')
                                    ->label('Encabezado')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('subheading')
                                    ->label('Subencabezado')
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('body')
                                    ->label('Contenido')
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('featured_media_id')
                                    ->label('Imagen principal')
                                    ->relationship('featuredMedia', 'file_name')
                                    ->searchable()
                                    ->preload()
                                    ->getSearchResultsUsing(fn (string $search) =>
                                        Media::where('mime_type', 'like', 'image/%')
                                            ->where('file_name', 'like', "%{$search}%")
                                            ->limit(20)
                                            ->pluck('file_name', 'id')
                                    )
                                    ->nullable()
                                    ->columnSpanFull(),
                                Forms\Components\KeyValue::make('settings')
                                    ->label('Configuraciones')
                                    ->helperText('Configuraciones adicionales en formato JSON')
                                    ->afterStateHydrated(function (Forms\Components\KeyValue $component, mixed $state): void {
                                        if (is_string($state)) {
                                            $decoded = json_decode($state, true);
                                            $component->state(is_array($decoded) ? $decoded : []);
                                        }
                                    }),
                                Forms\Components\Repeater::make('items')
                                    ->label('Ítems / Bullets')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Texto')
                                            ->required()
                                            ->maxLength(500),
                                        Forms\Components\TextInput::make('sort_order')
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
                                Forms\Components\CheckboxList::make('galleryMediaIds')
                                    ->label('Imágenes del mosaico (solo tipo gallery)')
                                    ->options(
                                        fn () => Media::where('mime_type', 'like', 'image/%')
                                            ->orderBy('file_name')
                                            ->pluck('file_name', 'id')
                                            ->toArray()
                                    )
                                    ->afterStateHydrated(function (Forms\Components\CheckboxList $component, $record) {
                                        if (! $record || ! $record->exists) {
                                            return;
                                        }
                                        $ids = $record->media()
                                            ->orderByPivot('sort_order')
                                            ->pluck('media.id')
                                            ->map(fn ($id) => (string) $id)
                                            ->toArray();
                                        $component->state($ids);
                                    })
                                    ->dehydrated(false)
                                    ->saveRelationshipsUsing(function (Forms\Components\CheckboxList $component) {
                                        $record = $component->getRecord();
                                        if (! $record) {
                                            return;
                                        }
                                        $selected = array_values(array_filter($component->getState() ?? []));
                                        $record->media()->detach();
                                        foreach ($selected as $i => $mediaId) {
                                            $record->media()->attach((int) $mediaId, [
                                                'collection' => 'gallery',
                                                'sort_order' => $i,
                                            ]);
                                        }
                                    })
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'gallery')
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->helperText('Marca las imágenes para el mosaico. El orden de sort_order sigue el orden numérico de los IDs seleccionados.'),
                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'active' => 'Activo',
                                        'inactive' => 'Inactivo',
                                    ])
                                    ->default('active'),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['heading'] ?? 'Nueva Sección')
                            ->collapsed()
                            ->collapsible()
                            ->orderColumn('sort_order')
                            ->reorderable()
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Sección'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Copiado!')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('template')
                    ->label('Plantilla')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'home' => 'success',
                        'landing' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sections_count')
                    ->label('Secciones')
                    ->counts('sections')
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                    ]),
                Tables\Filters\SelectFilter::make('template')
                    ->label('Plantilla')
                    ->options([
                        'default' => 'Por Defecto',
                        'home' => 'Inicio',
                        'contact' => 'Contacto',
                        'landing' => 'Landing Page',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
