<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Slimani\MediaManager\Form\MediaPicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\TransparencyDocumentResource\Pages\ListTransparencyDocuments;
use App\Filament\Resources\TransparencyDocumentResource\Pages\CreateTransparencyDocument;
use App\Filament\Resources\TransparencyDocumentResource\Pages\EditTransparencyDocument;
use App\Filament\Resources\TransparencyDocumentResource\Pages;
use App\Models\TransparencyDocument;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TransparencyDocumentResource extends Resource
{
    protected static ?string $model = TransparencyDocument::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string | \UnitEnum | null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'Documento de Transparencia';

    protected static ?string $pluralModelLabel = 'Documentos de Transparencia';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('year')
                            ->label('Año')
                            ->required()
                            ->numeric()
                            ->default(date('Y'))
                            ->minValue(2000)
                            ->maxValue(2100),
                        Select::make('type')
                            ->label('Tipo de Documento')
                            ->options([
                                'asambleas' => 'Asambleas',
                                'reportes'  => 'Reportes',
                                'estados'   => 'Estados de cuenta',
                            ])
                            ->required(),
                        Select::make('status')
                            ->label('Estado')
                            ->options(['draft' => 'Borrador', 'published' => 'Publicado'])
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
                    ])->columns(3),

                Section::make('Archivo')
                    ->schema([
                        MediaPicker::make('media_id')
                            ->label('Archivo PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->nullable()
                            ->helperText('Selecciona el PDF desde la Biblioteca de Medios'),
                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('year')
                    ->label('Año')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'asambleas' => 'Asambleas',
                        'reportes'  => 'Reportes',
                        'estados'   => 'Estados de cuenta',
                        default     => ucfirst($state),
                    })
                    ->color('warning'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => $state === 'published' ? 'success' : 'gray'),
                TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'asambleas' => 'Asambleas',
                        'reportes'  => 'Reportes',
                        'estados'   => 'Estados de cuenta',
                    ]),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(['draft' => 'Borrador', 'published' => 'Publicado']),
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

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListTransparencyDocuments::route('/'),
            'create' => CreateTransparencyDocument::route('/create'),
            'edit'   => EditTransparencyDocument::route('/{record}/edit'),
        ];
    }
}
