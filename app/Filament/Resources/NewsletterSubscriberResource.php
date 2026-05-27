<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriberResource\Pages\ListNewsletterSubscribers;
use App\Models\NewsletterSubscriber;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Validation\Rule;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-at-symbol';

    protected static string|\UnitEnum|null $navigationGroup = 'Mensajes';

    protected static ?string $modelLabel = 'Suscriptor';

    protected static ?string $pluralModelLabel = 'Newsletter';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('email')
                ->label('Correo electrónico')
                ->email()
                ->required()
                ->unique(
                    table: 'newsletter_subscribers',
                    column: 'email',
                    ignorable: fn ($record) => $record,
                )
                ->maxLength(255),

            TextInput::make('name')
                ->label('Nombre')
                ->maxLength(255),

            Select::make('status')
                ->label('Estado')
                ->options([
                    'active'      => 'Activo',
                    'unsubscribed' => 'Dado de baja',
                ])
                ->required(),

            DateTimePicker::make('subscribed_at')
                ->label('Fecha de suscripción')
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width('60px'),

                TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'       => 'success',
                        'unsubscribed' => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active'       => 'Activo',
                        'unsubscribed' => 'Dado de baja',
                        default        => $state,
                    }),

                TextColumn::make('subscribed_at')
                    ->label('Suscrito el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active'       => 'Activo',
                        'unsubscribed' => 'Dado de baja',
                    ]),
            ])
            ->actions([
                Action::make('unsubscribe')
                    ->label('Dar de baja')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Dar de baja al suscriptor')
                    ->modalDescription('¿Estás seguro de que deseas dar de baja a este suscriptor?')
                    ->modalSubmitActionLabel('Sí, dar de baja')
                    ->visible(fn ($record) => $record->status === 'active')
                    ->action(fn ($record) => $record->update(['status' => 'unsubscribed'])),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->modalHeading('Eliminar suscriptor')
                    ->modalDescription('¿Estás seguro de que deseas eliminar este suscriptor? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, eliminar'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('subscribed_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletterSubscribers::route('/'),
        ];
    }
}
