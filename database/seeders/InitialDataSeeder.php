<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**\n\n     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users
        User::firstOrCreate([
            'email' => 'admin@escalada.com'
        ], [
            'name' => 'Admin',
            'password' => bcrypt('r3d3nc10n'),
            'phone' => '1234567890',
            'status' => 'active',
        ]);

        // Create pages
        $homePage = Page::firstOrCreate([
            'slug' => 'inicio',
        ], [
            'title' => 'Inicio',
            'template' => 'home',
            'seo_title' => 'Escalada Pro - Inicio',
            'seo_description' => 'Bienvenido a Escalada Pro',
            'status' => 'published',
            'published_at' => now(),
        ]);

        if ($homePage->sections()->count() === 0) {
            PageSection::create([
                'page_id' => $homePage->id,
                'type' => 'hero',
                'heading' => 'Bienvenido a Escalada Pro',
                'subheading' => 'La comunidad de escaladores más grande del país',
                'body' => 'Únete a nuestra comunidad de escaladores y descubre nuevos desafíos',
                'sort_order' => 1,
                'status' => 'active',
            ]);
        }

        Page::firstOrCreate([
            'slug' => 'nosotros',
        ], [
            'title' => 'Nosotros',
            'template' => 'default',
            'seo_title' => 'Sobre Escalada Pro',
            'seo_description' => 'Conoce más sobre nuestra organización',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Create blog posts
        BlogPost::firstOrCreate([
            'slug' => 'primera-entrada-del-blog',
        ], [
            'title' => 'Primera entrada del blog',
            'body' => 'Este es el contenido de la primera entrada del blog. Aquí compartiremos noticias y novedades sobre escalada.',
            'excerpt' => 'Primera entrada del blog de Escalada Pro',
            'status' => 'published',
            'published_at' => now(),
        ]);

        BlogPost::firstOrCreate([
            'slug' => 'consejos-para-principiantes',
        ], [
            'title' => 'Consejos para principiantes',
            'body' => 'Si estás empezando en el mundo de la escalada, aquí te compartimos algunos consejos útiles para comenzar con buen pie.',
            'excerpt' => 'Guía básica para nuevos escaladores',
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        // Create site settings
        SiteSetting::firstOrCreate([
            'key' => 'site_name',
        ], [
            'value' => 'Escalada Pro',
        ]);

        SiteSetting::firstOrCreate([
            'key' => 'contact_email',
        ], [
            'value' => 'info@escaladapro.cl',
        ]);

        SiteSetting::firstOrCreate([
            'key' => 'contact_phone',
        ], [
            'value' => '+56 9 1234 5678',
        ]);

        $this->command->info('✅ Datos de prueba creados exitosamente');
    }
}
