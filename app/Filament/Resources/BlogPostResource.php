<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'Entrada del Blog';

    protected static ?string $pluralModelLabel = 'Blog Posts';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contenido Principal')
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
                            ->unique(ignoreRecord: true)
                            ->helperText('URL amigable generada automáticamente'),
                        Forms\Components\Select::make('category')
                            ->label('Categoría')
                            ->options([
                                'blog' => 'Blog',
                                'eventos' => 'Eventos',
                                'noticias' => 'Noticias',
                            ])
                            ->required()
                            ->default('blog'),
                        Forms\Components\TextInput::make('author_name')
                            ->label('Autor')
                            ->maxLength(255)
                            ->placeholder('Escalada Libre'),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Resumen')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Descripción corta para listados y SEO')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('body')
                            ->label('Contenido')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('blog')
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Imagen Destacada')
                    ->schema([
                        Forms\Components\Select::make('featured_media_id')
                            ->label('Imagen Destacada')
                            ->relationship('featuredMedia', 'file_name')
                            ->searchable()
                            ->nullable()
                            ->helperText('Selecciona una imagen de la biblioteca de medios'),
                    ]),

                Forms\Components\Section::make('Publicación')
                    ->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado en Home')
                            ->helperText('Los posts destacados aparecen primero en la página de inicio')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft'     => 'Borrador',
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
                            ->label('Fecha de Publicación')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('Título SEO')
                            ->maxLength(60)
                            ->helperText('Máx. 60 caracteres'),
                        Forms\Components\Textarea::make('seo_description')
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
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('category')
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
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Autor')
                    ->searchable()
                    ->default('Escalada Libre')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_comments_count')
                    ->label('Comentarios')
                    ->counts('approvedComments')
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft'     => 'Borrador',
                        'published' => 'Publicado',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'blog' => 'Blog',
                        'eventos' => 'Eventos',
                        'noticias' => 'Noticias',
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
            ->defaultSort('is_featured', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit'   => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
