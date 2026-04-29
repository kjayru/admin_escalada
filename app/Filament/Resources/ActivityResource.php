<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ActivityResource\Pages\ListActivities;
use App\Filament\Resources\ActivityResource\Pages\CreateActivity;
use App\Filament\Resources\ActivityResource\Pages\EditActivity;
use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use App\Models\Activity;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string | \UnitEnum | null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'Actividad';

    protected static ?string $pluralModelLabel = 'Actividades';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Actividad')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre de la Actividad')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('year')
                            ->label('Año')
                            ->required()
                            ->numeric()
                            ->default(date('Y'))
                            ->minValue(2000)
                            ->maxValue(2100),
                        TextInput::make('order')
                            ->label('Orden')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Orden de aparición dentro del año (menor = primero)'),
                    ])->columns(2),

                Section::make('Archivo PDF')
                    ->schema([
                        FileUpload::make('pdf_path')
                            ->label('Archivo PDF')
                            ->disk('public')
                            ->directory('activities/pdfs')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(20480)
                            ->downloadable()
                            ->helperText('Sube el PDF de la actividad (máx. 20 MB)'),
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
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year')
                    ->label('Año')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pdf_path')
                    ->label('Archivo')
                    ->formatStateUsing(fn ($state) => $state ? basename($state) : '—')
                    ->limit(30),
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
                TextColumn::make('published_at')
                    ->label('Fecha de Publicación')
                    ->dateTime('d/m/Y H:i')
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
                    ->options(fn () => Activity::distinct()->pluck('year', 'year')->toArray()),
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
            ->defaultSort('year', 'desc');
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
            'index' => ListActivities::route('/'),
            'create' => CreateActivity::route('/create'),
            'edit' => EditActivity::route('/{record}/edit'),
        ];
    }
}
