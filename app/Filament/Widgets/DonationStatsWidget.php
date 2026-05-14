<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DonationStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total  = Donation::sum('amount');
        $count  = Donation::count();
        $latest = Donation::latest('captured_at')->value('captured_at');

        return [
            Stat::make('Total recaudado', '$' . number_format($total, 2) . ' MXN')
                ->description('Suma de todas las donaciones')
                ->color('success'),

            Stat::make('Total de donaciones', $count)
                ->description('Transacciones completadas')
                ->color('primary'),

            Stat::make('Última donación', $latest ? \Carbon\Carbon::parse($latest)->format('d/m/Y H:i') : '—')
                ->description('Fecha y hora')
                ->color('gray'),
        ];
    }
}
