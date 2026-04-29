<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Grouping\Group;
use App\Filament\Resources\SettingResource\Pages\ListSettings;
use App\Filament\Resources\SettingResource\Pages\CreateSetting;
use App\Filament\Resources\SettingResource\Pages\EditSetting;
use App\Filament\Resources\SettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class SettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string | \UnitEnum | null $navigationGroup = 'Configuración';

    protected static ?string $modelLabel = 'Configuración';

    protected static ?string $pluralModelLabel = 'Configuraciones';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos')
                    ->schema([
                        Select::make('group')
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
                        TextInput::make('key')
                            ->label('Clave')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Ej: stat.actividades, site.nombre')
                            ->maxLength(255),
                        TextInput::make('value')
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
                TextColumn::make('group')
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
                TextColumn::make('key')
                    ->label('Clave')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('¡Copiado!'),
                TextInputColumn::make('value')
                    ->label('Valor')
                    ->searchable()
                    ->rules(['max:500']),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('group')
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
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('group')
            ->groups([
                Group::make('group')
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
            'index'  => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'edit'   => EditSetting::route('/{record}/edit'),
        ];
    }
}
