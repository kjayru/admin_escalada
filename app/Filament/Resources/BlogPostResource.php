<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Slimani\MediaManager\Form\MediaPicker;
use App\Filament\Schemas\ContentBlockSchema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\BlogPostResource\Pages\ListBlogPosts;
use App\Filament\Resources\BlogPostResource\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPostResource\Pages\EditBlogPost;
use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-newspaper';

    protected static string | \UnitEnum | null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'Entrada del Blog';

    protected static ?string $pluralModelLabel = 'Blog Posts';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Modo de Edición')
                    ->schema([
                        Select::make('content_mode')
                            ->label('Modo de Contenido')
                            ->options([
                                'classic' => 'Clásico (Editor de Texto)',
                                'blocks'  => 'Bloques (Constructor Visual)',
                            ])
                            ->default('classic')
                            ->required()
                            ->live()
                            ->helperText('Elige cómo editar el contenido de este post.'),
                    ]),

                Section::make('Contenido Principal')
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
                            ->unique(ignoreRecord: true)
                            ->helperText('URL amigable generada automáticamente'),
                        Select::make('category')
                            ->label('Categoría')
                            ->options([
                                'blog' => 'Blog',
                                'eventos' => 'Eventos',
                                'noticias' => 'Noticias',
                            ])
                            ->required()
                            ->default('blog'),
                        TextInput::make('author_name')
                            ->label('Autor')
                            ->maxLength(255)
                            ->placeholder('Escalada Libre'),
                        Textarea::make('excerpt')
                            ->label('Resumen')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Descripción corta para listados y SEO')
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Contenido')
                            ->required(false)
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('blog')
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => $get('content_mode') !== 'blocks'),
                    ])->columns(3),

                Section::make('Bloques de Contenido')
                    ->schema([
                        ContentBlockSchema::repeater(),
                    ])
                    ->visible(fn (Get $get): bool => $get('content_mode') === 'blocks'),

                Section::make('Imagen Destacada')
                    ->schema([
                        MediaPicker::make('featured_media_id')
                            ->label('Imagen Destacada')
                            ->nullable()
                            ->helperText('Selecciona una imagen de la Biblioteca de Medios'),
                    ]),

                Section::make('Publicación')
                    ->schema([
                        Toggle::make('is_featured')
                            ->label('Destacado en Home')
                            ->helperText('Los posts destacados aparecen primero en la página de inicio')
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft'     => 'Borrador',
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
                            ->label('Fecha de Publicación')
                            ->nullable(),
                    ])->columns(2),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('Título SEO')
                            ->maxLength(60)
                            ->helperText('Máx. 60 caracteres'),
                        Textarea::make('seo_description')
                            ->label('Descripción SEO')
                            ->rows(2)
                            ->maxLength(160)
                            ->helperText('Máx. 160 caracteres'),
                    ])->columns(2)->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->colors([
                        'primary' => 'blog',
                        'success' => 'eventos',
                        'info' => 'noticias',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('author_name')
                    ->label('Autor')
                    ->searchable()
                    ->default('Escalada Libre')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        default     => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('approved_comments_count')
                    ->label('Comentarios')
                    ->counts('approvedComments')
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft'     => 'Borrador',
                        'published' => 'Publicado',
                    ]),
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'blog' => 'Blog',
                        'eventos' => 'Eventos',
                        'noticias' => 'Noticias',
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
            ->defaultSort('is_featured', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit'   => EditBlogPost::route('/{record}/edit'),
        ];
    }
}
