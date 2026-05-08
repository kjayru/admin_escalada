<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Slimani\MediaManager\Form\MediaPicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\LogoRowResource\Pages\ListLogoRows;
use App\Filament\Resources\LogoRowResource\Pages\CreateLogoRow;
use App\Filament\Resources\LogoRowResource\Pages\EditLogoRow;
use App\Models\SponsorPlacement;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LogoRowResource extends Resource
{
    protected static ?string $model = SponsorPlacement::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string | \UnitEnum | null $navigationGroup = 'Patrocinadores';

    protected static ?string $modelLabel = 'Logo en Home';

    protected static ?string $pluralModelLabel = 'Logos Fila Home';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('placement', 'logo_row');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('placement')->default('logo_row'),

                Section::make('Patrocinador')
                    ->schema([
                        Select::make('sponsor_id')
                            ->label('Patrocinador')
                            ->relationship('sponsor', 'name')
                            ->searchable()
                            ->required(),
                        TextInput::make('link_url')
                            ->label('URL de enlace (opcional)')
                            ->maxLength(255),
                        MediaPicker::make('banner_media_id')
                            ->label('Logo')
                            ->helperText('Sube el logo que se mostrará en la fila de patrocinadores del home')
                            ->nullable()
                            ->rule('nullable')
                            ->columnSpanFull()
                            ->afterStateHydrated(function (MediaPicker $component, mixed $state): void {
                                if (blank($state)) {
                                    $record = $component->getRecord();
                                    if ($record) {
                                        $state = $record->getAttribute($component->getName());
                                    }
                                }
                                if (! is_null($state) && ! is_scalar($state) && ! is_array($state)) {
                                    $component->state(null);
                                    return;
                                }
                                if (is_scalar($state) && ! blank($state)) {
                                    $component->state((string) $state);
                                    return;
                                }
                                if (is_array($state) && filled($state)) {
                                    $val = array_values(array_filter(array_map(
                                        fn($v) => is_scalar($v) ? (string) $v : null,
                                        $state
                                    )))[0] ?? null;
                                    if ($val) {
                                        $component->state($val);
                                        return;
                                    }
                                }
                                $component->state(null);
                            })
                            ->dehydrateStateUsing(function (MediaPicker $component, $state) {
                                $source = filled($state) ? $state : $component->getRawState();
                                $values = array_values(
                                    array_filter(array_map(
                                        fn($v) => is_scalar($v) ? (string) $v : null,
                                        (array) ($source ?? [])
                                    ))
                                );
                                return $values[0] ?? null;
                            })
                            ->saveRelationshipsUsing(function (MediaPicker $component, $state): void {
                                $record = $component->getRecord();
                                if (! $record) {
                                    return;
                                }
                                $source = filled($state) ? $state : $component->getRawState();
                                $values = array_values(
                                    array_filter(array_map(
                                        fn($v) => is_scalar($v) ? (string) $v : null,
                                        (array) ($source ?? [])
                                    ))
                                );
                                $id = $values[0] ?? null;
                                if ($record->banner_media_id != $id) {
                                    $record->banner_media_id = $id;
                                    $record->save();
                                }
                            }),
                    ])->columns(2),

                Section::make('Visibilidad')
                    ->schema([
                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sponsor.name')
                    ->label('Patrocinador')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('link_url')
                    ->label('URL')
                    ->toggleable()
                    ->limit(40),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
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
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLogoRows::route('/'),
            'create' => CreateLogoRow::route('/create'),
            'edit'   => EditLogoRow::route('/{record}/edit'),
        ];
    }
}
