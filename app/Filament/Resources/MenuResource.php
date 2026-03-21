<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $modelLabel = 'Menú';

    protected static ?string $pluralModelLabel = 'Menús';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Menú')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Ej: Menú Principal, Menú Footer'),
                    ]),

                Forms\Components\Section::make('Ítems del Menú')
                    ->schema([
                        Forms\Components\Repeater::make('allItems')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Etiqueta')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\Select::make('page_id')
                                    ->label('Página vinculada')
                                    ->options(fn () => Page::where('status', 'published')
                                        ->orderBy('title')
                                        ->pluck('title', 'id')
                                        ->toArray())
                                    ->searchable()
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $page = Page::find($state);
                                            if ($page) {
                                                $set('url', '/' . $page->slug);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->maxLength(255)
                                    ->helperText('Ruta relativa ej: /nosotros, o URL externa'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Orden')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Nuevo ítem')
                            ->defaultItems(0)
                            ->reorderable('sort_order')
                            ->collapsible()
                            ->addActionLabel('Agregar ítem'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('allItems_count')
                    ->label('Ítems')
                    ->counts('allItems')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit'   => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
