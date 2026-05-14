<?php

namespace App\Filament\Widgets;

use App\Models\NewsletterSubscriber;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewsletterStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total    = NewsletterSubscriber::count();
        $active   = NewsletterSubscriber::where('status', 'active')->count();
        $inactive = NewsletterSubscriber::where('status', '!=', 'active')->count();

        return [
            Stat::make('Total de suscriptores', $total)
                ->description('Emails registrados')
                ->color('primary'),

            Stat::make('Activos', $active)
                ->description('Con suscripción activa')
                ->color('success'),

            Stat::make('Inactivos / Dados de baja', $inactive)
                ->description('Cancelados o inactivos')
                ->color('danger'),
        ];
    }
}
