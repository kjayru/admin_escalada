<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimelineResource\Pages;
use App\Filament\Resources\TimelineResource\RelationManagers;
use App\Models\Timeline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimelineResource extends Resource
{
    protected static ?string $model = Timeline::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'Hito Histórico';

    protected static ?string $pluralModelLabel = 'Línea del Tiempo';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Hito')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('body')
                            ->label('Descripción')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Fecha')
                    ->schema([
                        Forms\Components\TextInput::make('date')
                            ->label('Fecha (Texto)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: MARZO 2024')
                            ->helperText('Texto que se mostrará como fecha (ej: "MARZO 2024", "DICIEMBRE 2023")'),
                        Forms\Components\TextInput::make('year')
                            ->label('Año')
                            ->required()
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100)
                            ->default(date('Y')),
                        Forms\Components\Select::make('month')
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
                        Forms\Components\TextInput::make('order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->helperText('Orden manual dentro del mismo mes (menor = primero)'),
                    ])->columns(2),

                Forms\Components\Section::make('Imagen')
                    ->schema([
                        Forms\Components\Select::make('media_id')
                            ->label('Imagen')
                            ->relationship('media', 'file_name')
                            ->searchable()
                            ->required()
                            ->helperText('Selecciona la imagen desde la Biblioteca de Medios'),
                    ]),

                Forms\Components\Section::make('Publicación')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'published' => 'Publicado',
                            ])
                            ->required()
                            ->default('draft')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state === 'published') {
                                    $set('published_at', now()->toDateTimeString());
                                }
                            }),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Fecha de Publicación'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('month')
                    ->label('Mes')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => [
                        1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
                        5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
                        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
                    ][$state] ?? $state),
                Tables\Columns\ImageColumn::make('media.url')
                    ->label('Imagen')
                    ->square(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'draft' ? 'Borrador' : 'Publicado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Año')
                    ->options(fn () => Timeline::distinct()->pluck('year', 'year')->toArray()),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                    ]),
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
            'index' => Pages\ListTimelines::route('/'),
            'create' => Pages\CreateTimeline::route('/create'),
            'edit' => Pages\EditTimeline::route('/{record}/edit'),
        ];
    }
}
