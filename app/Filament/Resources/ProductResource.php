<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\View;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Slimani\MediaManager\Form\MediaPicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\ProductCategory;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string | \UnitEnum | null $navigationGroup = 'Tienda';

    protected static ?string $modelLabel = 'Producto';

    protected static ?string $pluralModelLabel = 'Productos';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Producto')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('category_id')
                            ->label('Categoría')
                            ->options(fn () => ProductCategory::pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->preload(),
                        Select::make('status')
                            ->label('Estado')
                            ->options(['draft' => 'Borrador', 'published' => 'Publicado', 'out_of_stock' => 'Sin Stock'])
                            ->required()
                            ->default('draft'),
                        Hidden::make('user_id')
                            ->default(fn () => auth()->id()),
                    ])->columns(2),

                Section::make('Precio y Medios')
                    ->schema([
                        TextInput::make('price')
                            ->label('Precio')
                            ->numeric()
                            ->prefix('$')
                            ->nullable(),
                        TextInput::make('currency')
                            ->label('Moneda')
                            ->default('MX')
                            ->maxLength(10),
                        MediaPicker::make('featured_media_id')
                            ->label('Imagen Destacada')
                            ->nullable(),
                    ])->columns(3),

                Section::make('Galería de Imágenes')
                    ->description('Carga múltiples imágenes para el slider del producto')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->collection('gallery')
                            ->multiple()
                            ->maxFiles(10)
                            ->reorderable()
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->helperText('Las imágenes aparecerán en el orden que las organices aquí.'),
                    ]),

                Section::make('Descripción')
                    ->schema([
                        Textarea::make('summary')
                            ->label('Resumen')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        RichEditor::make('description')
                            ->label('Descripción Completa')
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('products'),
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
                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('price')
                    ->label('Precio')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' MXN')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'published'    => 'Publicado',
                        'draft'        => 'Borrador',
                        'out_of_stock' => 'Sin Stock',
                        default        => ucfirst($state),
                    })
                    ->color(fn ($state) => match($state) {
                        'published'    => 'success',
                        'out_of_stock' => 'danger',
                        default        => 'gray',
                    }),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(['draft' => 'Borrador', 'published' => 'Publicado', 'out_of_stock' => 'Sin Stock']),
                SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name'),
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
            ->defaultSort('name');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit'   => EditProduct::route('/{record}/edit'),
        ];
    }
}
