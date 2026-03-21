<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransparencyDocumentResource\Pages;
use App\Models\TransparencyDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TransparencyDocumentResource extends Resource
{
    protected static ?string $model = TransparencyDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'Documento de Transparencia';

    protected static ?string $pluralModelLabel = 'Documentos de Transparencia';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('year')
                            ->label('Año')
                            ->required()
                            ->numeric()
                            ->default(date('Y'))
                            ->minValue(2000)
                            ->maxValue(2100),
                        Forms\Components\Select::make('type')
                            ->label('Tipo de Documento')
                            ->options([
                                'asambleas' => 'Asambleas',
                                'reportes'  => 'Reportes',
                                'estados'   => 'Estados de cuenta',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(['draft' => 'Borrador', 'published' => 'Publicado'])
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
                    ])->columns(3),

                Forms\Components\Section::make('Archivo')
                    ->schema([
                        Forms\Components\Select::make('media_id')
                            ->label('Archivo')
                            ->relationship('media', 'file_name')
                            ->searchable()
                            ->required()
                            ->helperText('Selecciona el PDF desde Biblioteca de Medios'),
                        Forms\Components\Textarea::make('description')
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'asambleas' => 'Asambleas',
                        'reportes'  => 'Reportes',
                        'estados'   => 'Estados de cuenta',
                        default     => ucfirst($state),
                    })
                    ->color('warning'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => $state === 'published' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'asambleas' => 'Asambleas',
                        'reportes'  => 'Reportes',
                        'estados'   => 'Estados de cuenta',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(['draft' => 'Borrador', 'published' => 'Publicado']),
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
            ->defaultSort('year', 'desc');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTransparencyDocuments::route('/'),
            'create' => Pages\CreateTransparencyDocument::route('/create'),
            'edit'   => Pages\EditTransparencyDocument::route('/{record}/edit'),
        ];
    }
}
