<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use App\Filament\Resources\SupportMethodResource\Pages\ListSupportMethods;
use App\Filament\Resources\SupportMethodResource\Pages\EditSupportMethod;
use App\Filament\Resources\SupportMethodResource\Pages;
use App\Models\SupportMethod;
use Slimani\MediaManager\Form\MediaPicker;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupportMethodResource extends Resource
{
    protected static ?string $model = SupportMethod::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-heart';

    protected static string | \UnitEnum | null $navigationGroup = 'Cómo Apoyar';

    protected static ?string $modelLabel = 'Bloque de apoyo';

    protected static ?string $pluralModelLabel = 'Cómo nos puedes apoyar';

    protected static ?int $navigationSort = 1;

    // Solo mostrar los 4 bloques fijos de la campaña como-apoyar-home
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('campaign', fn ($q) => $q->where('slug', 'como-apoyar-home'))
            ->orderBy('sort_order');
    }

    public static function form(Schema $schema): Schema
    {
        $typeLabels = [
            'paypal'        => 'PayPal',
            'transfer'      => 'Transferencia interbancaria',
            'gyms'          => 'Gyms',
            'products'      => 'Comprando nuestros productos',
            'bank_transfer' => 'Transferencia bancaria',
            'gym_partner'   => 'Socio Gimnasio',
        ];

        return $schema
            ->components([
                Section::make('Tipo de bloque')
                    ->schema([
                        Placeholder::make('type_label')
                            ->label('Tipo')
                            ->content(fn ($record) => $typeLabels[$record?->type] ?? ucfirst($record?->type ?? '-')),
                    ])
                    ->columns(1),

                Section::make('Contenido')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título del bloque')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('body')
                            ->label('Descripción')
                            ->rows(4)
                            ->maxLength(1000),
                    ])
                    ->columns(1),

                Section::make('Botón de acción')
                    ->schema([
                        TextInput::make('settings.button_label')
                            ->label('Texto del botón')
                            ->placeholder('Ej: Donar ahora')
                            ->maxLength(100),
                        TextInput::make('settings.button_url')
                            ->label('URL del botón')
                            ->placeholder('Ej: /como-apoyar/paypal')
                            ->maxLength(500),
                    ])
                    ->columns(2),

                Section::make('Imagen')
                    ->schema([
                        MediaPicker::make('media_id')
                            ->label('Imagen del bloque')
                            ->nullable()
                            ->helperText('Selecciona una imagen de la Biblioteca de Medios, o deja vacío para usar la imagen predeterminada.'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        $typeLabels = [
            'paypal'        => 'PayPal',
            'transfer'      => 'Transferencia interbancaria',
            'gyms'          => 'Gyms',
            'products'      => 'Comprando nuestros productos',
            'bank_transfer' => 'Transferencia bancaria',
            'gym_partner'   => 'Socio Gimnasio',
        ];

        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => $typeLabels[$state] ?? ucfirst($state))
                    ->badge()
                    ->color('warning'),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('body')
                    ->label('Descripción')
                    ->limit(60)
                    ->wrap(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            // Sin acciones masivas ni botón de eliminar
            ->toolbarActions([])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupportMethods::route('/'),
            'edit'  => EditSupportMethod::route('/{record}/edit'),
        ];
    }
}
