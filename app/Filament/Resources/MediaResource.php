<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Filament\Resources\MediaResource\RelationManagers;
use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'Archivo';
    
    protected static ?string $pluralModelLabel = 'Biblioteca de Medios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Archivo')
                    ->schema([
                        Forms\Components\FileUpload::make('path')
                            ->label('Archivo')
                            ->directory('media')
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->maxSize(10240)
                            ->required(),
                        Forms\Components\TextInput::make('alt')
                            ->label('Texto Alternativo')
                            ->maxLength(255)
                            ->helperText('Importante para SEO y accesibilidad'),
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Vista Previa')
                    ->disk('public')
                    ->size(60)
                    ->defaultImageUrl('/images/file-icon.png'),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Tipo')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('Tamaño')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('width')
                    ->label('Dimensiones')
                    ->formatStateUsing(fn ($record) => $record->width && $record->height ? "{$record->width}x{$record->height}" : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Subido por')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Subido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mime_type')
                    ->label('Tipo de Archivo')
                    ->options([
                        'image/jpeg' => 'JPEG',
                        'image/png' => 'PNG',
                        'image/gif' => 'GIF',
                        'image/webp' => 'WebP',
                        'application/pdf' => 'PDF',
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
