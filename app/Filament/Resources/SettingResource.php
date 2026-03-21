<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class SettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $modelLabel = 'Configuración';

    protected static ?string $pluralModelLabel = 'Configuraciones';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos')
                    ->schema([
                        Forms\Components\Select::make('group')
                            ->label('Grupo')
                            ->options([
                                'stats'      => 'Estadísticas',
                                'numeros'    => 'Números Home',
                                'newsletter' => 'Newsletter',
                                'general'    => 'General',
                                'social'     => 'Redes Sociales',
                                'contact'    => 'Contacto',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('key')
                            ->label('Clave')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Ej: stat.actividades, site.nombre')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('value')
                            ->label('Valor')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->label('Grupo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'stats'      => 'warning',
                        'numeros'    => 'danger',
                        'newsletter' => 'success',
                        'general'    => 'primary',
                        'social'     => 'success',
                        'contact'    => 'info',
                        default      => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Clave')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('¡Copiado!'),
                Tables\Columns\TextInputColumn::make('value')
                    ->label('Valor')
                    ->searchable()
                    ->rules(['max:500']),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Grupo')
                    ->options([
                        'stats'      => 'Estadísticas',
                        'numeros'    => 'Números Home',
                        'newsletter' => 'Newsletter',
                        'general'    => 'General',
                        'social'     => 'Redes Sociales',
                        'contact'    => 'Contacto',
                    ]),
            ])
            ->headerActions([
                Action::make('seed_stats')
                    ->label('Inicializar Estadísticas')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Inicializar estadísticas predeterminadas')
                    ->modalDescription('Esto creará las claves de estadísticas que aún no existan. No sobreescribirá valores existentes.')
                    ->action(function () {
                        $defaults = [
                            'stat.actividades'      => '74',
                            'stat.arboles'          => '500',
                            'stat.rutas'            => '93',
                            'stat.bolts'            => '1110',
                            'stat.senalizaciones'   => '15',
                            'stat.voluntarios'      => '300',
                            'stat.anos_util'        => '30',
                            'stat.costo_reequipado' => '$3700',
                            'stat.monto_invertido'  => '$400,000',
                        ];

                        $created = 0;
                        foreach ($defaults as $key => $value) {
                            $exists = SiteSetting::where('key', $key)->exists();
                            if (! $exists) {
                                SiteSetting::create([
                                    'key'   => $key,
                                    'value' => $value,
                                    'group' => 'stats',
                                ]);
                                $created++;
                            }
                        }

                        Notification::make()
                            ->title($created > 0
                                ? "{$created} estadísticas inicializadas correctamente."
                                : 'Todas las estadísticas ya existen.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('group')
            ->groups([
                Tables\Grouping\Group::make('group')
                    ->label('Grupo')
                    ->collapsible(),
            ])
            ->defaultGroup('group');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit'   => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
