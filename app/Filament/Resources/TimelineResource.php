<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Slimani\MediaManager\Form\MediaPicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\TimelineResource\Pages\ListTimelines;
use App\Filament\Resources\TimelineResource\Pages\CreateTimeline;
use App\Filament\Resources\TimelineResource\Pages\EditTimeline;
use App\Filament\Resources\TimelineResource\Pages;
use App\Filament\Resources\TimelineResource\RelationManagers;
use App\Models\Timeline;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimelineResource extends Resource
{
    protected static ?string $model = Timeline::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected static string | \UnitEnum | null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'Hito Histórico';

    protected static ?string $pluralModelLabel = 'Línea del Tiempo';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Hito')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Descripción')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Fecha')
                    ->schema([
                        TextInput::make('date')
                            ->label('Fecha (Texto)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: MARZO 2024')
                            ->helperText('Texto que se mostrará como fecha (ej: "MARZO 2024", "DICIEMBRE 2023")'),
                        TextInput::make('year')
                            ->label('Año')
                            ->required()
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100)
                            ->default(date('Y')),
                        Select::make('month')
                            ->label('Mes')
                            ->required()
                            ->options([
                                1 => 'Enero',
                                2 => 'Febrero',
                                3 => 'Marzo',
                                4 => 'Abril',
                                5 => 'Mayo',
                                6 => 'Junio',
                                7 => 'Julio',
                                8 => 'Agosto',
                                9 => 'Septiembre',
                                10 => 'Octubre',
                                11 => 'Noviembre',
                                12 => 'Diciembre',
                            ])
                            ->default(1)
                            ->helperText('Para ordenar cronológicamente'),
                        TextInput::make('order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->helperText('Orden manual dentro del mismo mes (menor = primero)'),
                    ])->columns(2),

                Section::make('Imagen')
                    ->schema([
                        MediaPicker::make('media_id')
                            ->label('Imagen')
                            ->nullable()
                            ->helperText('Selecciona la imagen desde la Biblioteca de Medios')
                            ->afterStateHydrated(function (MediaPicker $component, mixed $state): void {
                                if (! is_null($state) && ! is_scalar($state) && ! is_array($state)) {
                                    $component->state(null);
                                }
                            }),
                    ]),

                Section::make('Publicación')
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'published' => 'Publicado',
                            ])
                            ->required()
                            ->default('draft')
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state === 'published') {
                                    $set('published_at', now()->toDateTimeString());
                                }
                            }),
                        DateTimePicker::make('published_at')
                            ->label('Fecha de Publicación'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Fecha')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('year')
                    ->label('Año')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('month')
                    ->label('Mes')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => [
                        1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
                        5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
                        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
                    ][$state] ?? $state),
                ImageColumn::make('media.url')
                    ->label('Imagen')
                    ->square(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'draft' ? 'Borrador' : 'Publicado')
                    ->searchable(),
                TextColumn::make('order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Año')
                    ->options(fn () => Timeline::distinct()->pluck('year', 'year')->toArray()),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                    ]),
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
            ->defaultSort('year', 'desc')
            ->defaultSort('month', 'desc');
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
            'index' => ListTimelines::route('/'),
            'create' => CreateTimeline::route('/create'),
            'edit' => EditTimeline::route('/{record}/edit'),
        ];
    }
}
