<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportMethodResource\Pages;
use App\Models\Media;
use App\Models\SupportMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupportMethodResource extends Resource
{
    protected static ?string $model = SupportMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationGroup = 'Cómo Apoyar';

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

    public static function form(Form $form): Form
    {
        $typeLabels = [
            'paypal'        => 'PayPal',
            'transfer'      => 'Transferencia interbancaria',
            'gyms'          => 'Gyms',
            'products'      => 'Comprando nuestros productos',
            'bank_transfer' => 'Transferencia bancaria',
            'gym_partner'   => 'Socio Gimnasio',
        ];

        return $form
            ->schema([
                Forms\Components\Section::make('Tipo de bloque')
                    ->schema([
                        Forms\Components\Placeholder::make('type_label')
                            ->label('Tipo')
                            ->content(fn ($record) => $typeLabels[$record?->type] ?? ucfirst($record?->type ?? '-')),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Contenido')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título del bloque')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('body')
                            ->label('Descripción')
                            ->rows(4)
                            ->maxLength(1000),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Imagen')
                    ->schema([
                        Forms\Components\Select::make('media_id')
                            ->label('Imagen del bloque')
                            ->options(fn () => Media::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->helperText('Selecciona una imagen de la biblioteca de medios, o deja vacío para usar la imagen predeterminada.'),
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
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => $typeLabels[$state] ?? ucfirst($state))
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('body')
                    ->label('Descripción')
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            // Sin acciones masivas ni botón de eliminar
            ->bulkActions([])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportMethods::route('/'),
            'edit'  => Pages\EditSupportMethod::route('/{record}/edit'),
        ];
    }
}
