<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\MemberGroupResource\Pages\ListMemberGroups;
use App\Filament\Resources\MemberGroupResource\Pages\CreateMemberGroup;
use App\Filament\Resources\MemberGroupResource\Pages\EditMemberGroup;
use App\Filament\Resources\MemberGroupResource\RelationManagers\MembersRelationManager;
use App\Filament\Resources\MemberGroupResource\Pages;
use App\Models\MemberGroup;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MemberGroupResource extends Resource
{
    protected static ?string $model = MemberGroup::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static string | \UnitEnum | null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'Grupo de Miembros';

    protected static ?string $pluralModelLabel = 'Asociación — Grupos';

    protected static ?string $navigationLabel = 'Nosotros — Miembros';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Grupo')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del grupo')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active'   => 'Activo',
                                'inactive' => 'Inactivo',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),
                TextColumn::make('name')
                    ->label('Grupo')
                    ->searchable(),
                TextColumn::make('members_count')
                    ->label('Miembros')
                    ->counts('members')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'danger'  => 'inactive',
                    ]),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
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
        return [
            MembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListMemberGroups::route('/'),
            'create' => CreateMemberGroup::route('/create'),
            'edit'   => EditMemberGroup::route('/{record}/edit'),
        ];
    }
}
