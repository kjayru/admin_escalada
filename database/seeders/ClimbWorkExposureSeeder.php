<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class ClimbWorkExposureSeeder extends Seeder
{
    public function run(): void
    {
        // ── ClimbWork (datos de patrocinio.vue) ─────────────────────
        Sponsor::firstOrCreate(
            ['slug' => 'climb-work'],
            [
                'name'          => 'ClimbWork',
                'tagline'       => 'Vinculamos el oficio de la joyería en plata con el mundo de las montañas.',
                'description'   => "Siéntete seguro en todas partes, la inspiración viene del equipo que usamos. Estamos enfocados en promover actividades recreativas de montaña de manera segura y sustentable. Buscamos contribuir al cuidado del medio ambiente y al desarrollo de sociedades pacíficas y sustentables a través del deporte.\n\nSiéntete seguro en todas partes, la inspiración viene del equipo que usamos. Estamos enfocados en promover actividades recreativas de montaña de manera segura y sustentable. Buscamos contribuir al cuidado del medio ambiente y al desarrollo de sociedades pacíficas y sustentables a través del deporte.",
                'website_url'   => null,
                'contact_name'  => 'Uriel Torres',
                'contact_title' => 'Principal Sponsor',
                'contact_text'  => 'Protegemos el libre acceso a las zonas de escalada para nuestra comunidad y visitantes.',
                'status'        => 'active',
            ]
        );

        // ── Exposure (datos de patrocinio-2.vue) ────────────────────
        Sponsor::firstOrCreate(
            ['slug' => 'exposure'],
            [
                'name'          => 'Exposure',
                'tagline'       => 'MISSION LT 2.0',
                'description'   => "El Aspect Pro está diseñado para misiones a tope, grandes días en el valle y altos picos alpinos. Diseñado para funcionar en losas y terrenos verticales, el Aspect Pro tiene un talón moldeado en 3D, una entresuela rígida y un antepié ligeramente inclinado hacia abajo. La cobertura adicional en la puntera brinda mayor durabilidad para escalar grietas. Un diseño de altura media brinda protección y cobertura para los tobillos en espacios abiertos. Finalmente, la parte superior de cuero Ecco DriTan™ está hecha con un proceso que requiere menos agua y la lengüeta de malla agrega transpirabilidad.\n\nSomos distribuidores autorizados con amplia experiencia en proyectos con Escalada Libre, ofreciendo productos para montañismo y escalada en México.",
                'website_url'   => null,
                'contact_name'  => null,
                'contact_title' => null,
                'contact_text'  => 'Adquiérelo fácilmente desde nuestro sitio web y conoce más sobre este producto.',
                'facebook_url'  => null,
                'twitter_url'   => null,
                'email'         => null,
                'status'        => 'active',
            ]
        );

        $this->command->info('ClimbWork y Exposure sembrados correctamente.');
    }
}
