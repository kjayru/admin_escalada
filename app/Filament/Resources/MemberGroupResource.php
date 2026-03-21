<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberGroupResource\Pages;
use App\Models\MemberGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MemberGroupResource extends Resource
{
    protected static ?string $model = MemberGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'Grupo de Miembros';

    protected static ?string $pluralModelLabel = 'Asociación — Grupos';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Grupo')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del grupo')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active'   => 'Activo',
                                'inactive' => 'Inactivo',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Miembros')
                    ->schema([
                        Forms\Components\Repeater::make('members')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre completo')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('role')
                                    ->label('Cargo / Rol')
                                    ->maxLength(100)
                                    ->placeholder('Ej. Presidente, Secretario...'),
                                Forms\Components\Select::make('featured_media_id')
                                    ->label('Foto')
                                    ->relationship('featuredMedia', 'file_name')
                                    ->searchable()
                                    ->nullable()
                                    ->helperText('Selecciona de la biblioteca de medios'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Orden')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'active'   => 'Activo',
                                        'inactive' => 'Inactivo',
                                    ])
                                    ->default('active'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable('sort_order')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Nuevo miembro')
                            ->addActionLabel('Agregar miembro')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),
                Tables\Columns\TextColumn::make('name')
                    ->label('Grupo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('members_count')
                    ->label('Miembros')
                    ->counts('members')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'danger'  => 'inactive',
                    ]),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
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
            'index'  => Pages\ListMemberGroups::route('/'),
            'create' => Pages\CreateMemberGroup::route('/create'),
            'edit'   => Pages\EditMemberGroup::route('/{record}/edit'),
        ];
    }
}
