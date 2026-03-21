<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class StatsAndMenuSeeder extends Seeder
{
    public function run(): void
    {
        // ── Estadísticas ────────────────────────────────────────────
        $stats = [
            'stat.actividades'      => '74',
            'stat.arboles'          => '500',
            'stat.rutas'            => '93',
            'stat.bolts'            => '1110',
            'stat.senalizaciones'   => '15',
            'stat.voluntarios'      => '300',
            'stat.anos_util'        => '30',
            'stat.costo_reequipado' => '$3,700',
            'stat.monto_invertido'  => '$400,000',
        ];

        foreach ($stats as $key => $value) {
            SiteSetting::firstOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'stats']
            );
        }

        // Ajustar group en registros que ya existen sin group
        SiteSetting::whereIn('key', array_keys($stats))
            ->whereNull('group')
            ->update(['group' => 'stats']);

        $this->command->info('✅ Estadísticas sembradas.');

        // ── Menú Principal ──────────────────────────────────────────
        $menu = Menu::firstOrCreate(['name' => 'Menú Principal']);

        if ($menu->allItems()->count() === 0) {
            $pages = Page::whereIn('slug', [
                'inicio', 'nosotros', 'actividades', 'historia',
                'transparencia', 'contacto',
            ])->pluck('id', 'slug');

            $items = [
                ['label' => 'Inicio',         'url' => '/',               'slug' => 'inicio'],
                ['label' => 'Nosotros',        'url' => '/nosotros',       'slug' => 'nosotros'],
                ['label' => 'Actividades',     'url' => '/actividades',    'slug' => 'actividades'],
                ['label' => 'Historia',        'url' => '/historia',       'slug' => 'historia'],
                ['label' => 'Blog',            'url' => '/blog',           'slug' => null],
                ['label' => 'Cómo Apoyar',     'url' => '/como-apoyar',    'slug' => null],
                ['label' => 'Transparencia',   'url' => '/transparencia',  'slug' => 'transparencia'],
                ['label' => 'Patrocinio',      'url' => '/patrocinio',     'slug' => null],
                ['label' => 'Contacto',        'url' => '/contacto',       'slug' => 'contacto'],
            ];

            foreach ($items as $i => $item) {
                MenuItem::create([
                    'menu_id'    => $menu->id,
                    'label'      => $item['label'],
                    'url'        => $item['url'],
                    'page_id'    => $item['slug'] ? ($pages[$item['slug']] ?? null) : null,
                    'sort_order' => $i + 1,
                    'is_active'  => true,
                ]);
            }

            $this->command->info('✅ Menú Principal sembrado con ' . count($items) . ' ítems.');
        } else {
            $this->command->info('ℹ️  El Menú Principal ya tiene ítems, se omitió el seed.');
        }
    }
}
