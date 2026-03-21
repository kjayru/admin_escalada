<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug'            => 'historia',
                'title'           => 'Historia',
                'template'        => 'default',
                'seo_title'       => 'Historia - Escalada Libre',
                'seo_description' => 'Conoce la historia de Escalada Libre México.',
                'sections' => [
                    [
                        'type'    => 'hero',
                        'heading' => 'Historia',
                        'body'    => null,
                        'settings' => ['key' => 'hero'],
                    ],
                    [
                        'type'    => 'text',
                        'heading' => 'Nuestros Orígenes',
                        'body'    => 'Escalada Libre nació de la pasión de un grupo de escaladores del noreste de México comprometidos con preservar las paredes de roca y promover la escalada sustentable.',
                        'settings' => ['key' => 'intro'],
                    ],
                ],
            ],
            [
                'slug'            => 'actividades',
                'title'           => 'Actividades',
                'template'        => 'default',
                'seo_title'       => 'Actividades - Escalada Libre',
                'seo_description' => 'Descubre todas las actividades que realiza Escalada Libre.',
                'sections' => [
                    [
                        'type'    => 'hero',
                        'heading' => 'Actividades',
                        'body'    => null,
                        'settings' => ['key' => 'hero'],
                    ],
                    [
                        'type'    => 'text',
                        'heading' => '¿Qué hacemos?',
                        'body'    => 'Realizamos reforestaciones, limpieza de rutas, instalación de señalamientos y rehabilitación de vías de escalada en las principales zonas del noreste de México.',
                        'settings' => ['key' => 'intro'],
                    ],
                ],
            ],
            [
                'slug'            => 'transparencia',
                'title'           => 'Transparencia',
                'template'        => 'default',
                'seo_title'       => 'Transparencia - Escalada Libre',
                'seo_description' => 'Consulta nuestros documentos y reportes de transparencia.',
                'sections' => [
                    [
                        'type'    => 'hero',
                        'heading' => 'Transparencia',
                        'body'    => null,
                        'settings' => ['key' => 'hero'],
                    ],
                ],
            ],
            [
                'slug'            => 'contacto',
                'title'           => 'Contacto',
                'template'        => 'contact',
                'seo_title'       => 'Contacto - Escalada Libre',
                'seo_description' => 'Ponte en contacto con Escalada Libre México.',
                'sections' => [
                    [
                        'type'    => 'hero',
                        'heading' => 'Contáctanos',
                        'body'    => null,
                        'settings' => ['key' => 'hero'],
                    ],
                ],
            ],
            [
                'slug'            => 'como-apoyar',
                'title'           => 'Cómo Apoyar',
                'template'        => 'default',
                'seo_title'       => 'Cómo Apoyar - Escalada Libre',
                'seo_description' => 'Descubre las formas de apoyar a Escalada Libre México.',
                'sections' => [
                    [
                        'type'    => 'hero',
                        'heading' => 'Cómo puedes apoyarnos',
                        'body'    => 'Existen cuatro maneras en las que puedes apoyarnos y ser parte del cambio.',
                        'settings' => ['key' => 'hero'],
                    ],
                ],
            ],
        ];

        foreach ($pages as $pageData) {
            $sections = $pageData['sections'];
            unset($pageData['sections']);

            $page = Page::firstOrCreate(
                ['slug' => $pageData['slug']],
                array_merge($pageData, [
                    'status'       => 'published',
                    'published_at' => now(),
                ])
            );

            if ($page->sections()->count() === 0) {
                foreach ($sections as $i => $section) {
                    PageSection::create([
                        'page_id'    => $page->id,
                        'type'       => $section['type'],
                        'heading'    => $section['heading'],
                        'body'       => $section['body'],
                        'settings'   => $section['settings'],
                        'sort_order' => $i + 1,
                        'status'     => 'active',
                    ]);
                }
                $this->command->info("✅ Página '{$page->slug}' sembrada con " . count($sections) . " sección(es).");
            } else {
                $this->command->info("ℹ️  Página '{$page->slug}' ya existe, se omitió.");
            }
        }
    }
}
