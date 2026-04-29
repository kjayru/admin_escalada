<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\MemberResource\Pages\ListMembers;
use App\Filament\Resources\MemberResource\Pages\CreateMember;
use App\Filament\Resources\MemberResource\Pages\EditMember;
use App\Models\Member;
use App\Models\MemberGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Slimani\MediaManager\Form\MediaPicker;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user';

    protected static string | \UnitEnum | null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'Miembro';

    protected static ?string $pluralModelLabel = 'Miembros';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Grupo')
                    ->schema([
                        Select::make('member_group_id')
                            ->label('Grupo')
                            ->options(MemberGroup::orderBy('sort_order')->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active'   => 'Activo',
                                'inactive' => 'Inactivo',
                            ])
                            ->default('active')
                            ->required(),
                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Section::make('Información')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('role')
                            ->label('Cargo / Rol')
                            ->maxLength(100)
                            ->placeholder('Ej. Presidente, Secretario...'),
                        Textarea::make('bio')
                            ->label('Descripción / Bio')
                            ->rows(4)
                            ->nullable()
                            ->columnSpanFull(),
                        Toggle::make('featured_home')
                            ->label('Destacar en home')
                            ->default(false)
                            ->helperText('Aparecerá en la sección del equipo en la página principal'),
                    ])->columns(2),

                Section::make('Foto')
                    ->schema([
                        MediaPicker::make('featured_media_id')
                            ->label('Foto del miembro')
                            ->nullable()
                            ->helperText('Selecciona o sube una imagen desde la Biblioteca de Medios'),
                    ]),
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
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Cargo')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('group.name')
                    ->label('Grupo')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                IconColumn::make('featured_home')
                    ->label('Destacado en home')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'inactive' => 'danger',
                        default    => 'gray',
                    }),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('member_group_id')
                    ->label('Grupo')
                    ->options(MemberGroup::orderBy('sort_order')->pluck('name', 'id')),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active'   => 'Activo',
                        'inactive' => 'Inactivo',
                    ]),
            ])
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
            'index'  => ListMembers::route('/'),
            'create' => CreateMember::route('/create'),
            'edit'   => EditMember::route('/{record}/edit'),
        ];
    }
}
