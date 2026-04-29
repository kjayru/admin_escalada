<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PageResource\Pages;
use App\Filament\Schemas\ContentBlockSchema;
use App\Models\Page;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    
    protected static string | \UnitEnum | null $navigationGroup = 'Contenido';
    
    protected static ?string $modelLabel = 'Página';
    
    protected static ?string $pluralModelLabel = 'Páginas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('URL amigable de la página'),
                        Select::make('template')
                            ->label('Plantilla')
                            ->options([
                                'default' => 'Por Defecto',
                                'home' => 'Inicio',
                                'contact' => 'Contacto',
                                'landing' => 'Landing Page',
                            ])
                            ->required()
                            ->default('default'),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'published' => 'Publicado',
                            ])
                            ->required()
                            ->default('draft'),
                        DateTimePicker::make('published_at')
                            ->label('Fecha de Publicación'),
                    ])->columns(2),
                
                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('Título SEO')
                            ->maxLength(255)
                            ->helperText('Máximo 60 caracteres'),
                        Textarea::make('seo_description')
                            ->label('Descripción SEO')
                            ->rows(3)
                            ->helperText('Máximo 160 caracteres'),
                    ])->collapsible(),
                
                Section::make('Secciones')
                    ->schema([
                        ContentBlockSchema::repeater(),
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
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Copiado!')
                    ->toggleable(),
                TextColumn::make('template')
                    ->label('Plantilla')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'home' => 'success',
                        'landing' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('sections_count')
                    ->label('Secciones')
                    ->counts('sections')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                    ]),
                SelectFilter::make('template')
                    ->label('Plantilla')
                    ->options([
                        'default' => 'Por Defecto',
                        'home' => 'Inicio',
                        'contact' => 'Contacto',
                        'landing' => 'Landing Page',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
