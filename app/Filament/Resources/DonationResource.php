<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages\ListDonations;
use App\Models\Donation;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|\UnitEnum|null $navigationGroup = 'Mensajes';

    protected static ?string $modelLabel = 'Donación';

    protected static ?string $pluralModelLabel = 'Donaciones';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width('60px'),

                TextColumn::make('payer_name')
                    ->label('Nombre')
                    ->formatStateUsing(fn ($record) => trim($record->payer_name . ' ' . $record->payer_last_name))
                    ->searchable(query: function ($query, string $search) {
                        $query->where('payer_name', 'like', "%{$search}%")
                              ->orWhere('payer_last_name', 'like', "%{$search}%");
                    }),

                TextColumn::make('payer_email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->money(fn ($record) => strtolower($record->currency ?? 'mxn'))
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Moneda')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('paypal_order_id')
                    ->label('Orden PayPal')
                    ->copyable()
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'COMPLETED' => 'success',
                        'PENDING'   => 'warning',
                        default     => 'gray',
                    }),

                TextColumn::make('captured_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('captured_at', 'desc')
            ->searchable()
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonations::route('/'),
        ];
    }
}
