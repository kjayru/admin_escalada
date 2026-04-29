<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\MenuResource\Pages\ListMenus;
use App\Filament\Resources\MenuResource\Pages\CreateMenu;
use App\Filament\Resources\MenuResource\Pages\EditMenu;
use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use App\Models\Page;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bars-3';

    protected static string | \UnitEnum | null $navigationGroup = 'Configuración';

    protected static ?string $modelLabel = 'Menú';

    protected static ?string $pluralModelLabel = 'Menús';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Menú')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Ej: Menú Principal, Menú Footer'),
                    ]),

                Section::make('Ítems del Menú')
                    ->schema([
                        Repeater::make('allItems')
                            ->label('')
                            ->relationship()
                            ->schema([
                                TextInput::make('label')
                                    ->label('Etiqueta')
                                    ->required()
                                    ->maxLength(100),
                                Select::make('page_id')
                                    ->label('Página vinculada')
                                    ->options(fn () => Page::where('status', 'published')
                                        ->orderBy('title')
                                        ->pluck('title', 'id')
                                        ->toArray())
                                    ->searchable()
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $page = Page::find($state);
                                            if ($page) {
                                                $set('url', '/' . $page->slug);
                                            }
                                        }
                                    }),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->maxLength(255)
                                    ->helperText('Ruta relativa ej: /nosotros, o URL externa'),
                                TextInput::make('sort_order')
                                    ->label('Orden')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                                Toggle::make('is_active')
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
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('allItems_count')
                    ->label('Ítems')
                    ->counts('allItems')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index'  => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit'   => EditMenu::route('/{record}/edit'),
        ];
    }
}
