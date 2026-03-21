<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Member;
use App\Models\MemberGroup;
use Illuminate\Database\Seeder;

class MemberGroupSeeder extends Seeder
{
    public function run(): void
    {
        // Crear (o recuperar) los 2 registros de Media para las fotos
        $foto1 = Media::firstOrCreate(
            ['path' => 'images/foto1.png'],
            [
                'disk'      => 'public',
                'file_name' => 'foto1.png',
                'mime_type' => 'image/png',
                'size'      => 0,
                'alt'       => 'Foto miembro',
            ]
        );

        $foto2 = Media::firstOrCreate(
            ['path' => 'images/foto2.png'],
            [
                'disk'      => 'public',
                'file_name' => 'foto2.png',
                'mime_type' => 'image/png',
                'size'      => 0,
                'alt'       => 'Foto miembro',
            ]
        );

        $photos = [$foto1->id, $foto2->id];

        $groups = [
            [
                'name'       => 'Mesa directiva',
                'slug'       => 'mesa-directiva',
                'sort_order' => 1,
                'members'    => [
                    ['name' => 'Carlos Ortiz',       'role' => 'Presidente'],
                    ['name' => 'Sebastián Landeros', 'role' => 'Secretario'],
                    ['name' => 'Gustavo Castro',     'role' => 'Tesorero'],
                    ['name' => 'Adrián Lozano',      'role' => 'Consejero'],
                    ['name' => 'Ricardo Vara',       'role' => 'Consejero'],
                ],
            ],
            [
                'name'       => 'Miembros actuales',
                'slug'       => 'miembros-actuales',
                'sort_order' => 2,
                'members'    => [
                    ['name' => 'Ramón Narváez',        'role' => null],
                    ['name' => 'Jacobo Jasso',         'role' => null],
                    ['name' => 'Salvador Escamilla',   'role' => null],
                    ['name' => 'Marco Nieto',          'role' => null],
                    ['name' => 'Francisco Serratos',   'role' => null],
                    ['name' => 'Mayela Contreras',     'role' => null],
                    ['name' => 'Othón Martínez',       'role' => null],
                    ['name' => 'José Marcelo Rosiles', 'role' => null],
                    ['name' => 'Rubén Pérez',          'role' => null],
                    ['name' => 'Melissa Canavati',     'role' => null],
                    ['name' => 'Keila Leal',           'role' => null],
                    ['name' => 'Rodrigo (Pop) Serrano','role' => null],
                    ['name' => 'Eduardo Mijares',      'role' => null],
                    ['name' => 'Alejandro Martínez',   'role' => null],
                    ['name' => 'Oskar Sandoval',       'role' => null],
                ],
            ],
            [
                'name'       => 'Miembros fundadores',
                'slug'       => 'miembros-fundadores',
                'sort_order' => 3,
                'members'    => [
                    ['name' => 'Milton Páez',          'role' => null],
                    ['name' => 'Luisa Ríos',           'role' => null],
                    ['name' => 'Roberto Cebrián',      'role' => null],
                    ['name' => 'Charo Castro',         'role' => null],
                    ['name' => 'Carlos Uriel Torres',  'role' => null],
                    ['name' => 'Felipe Sánchez Garza', 'role' => null],
                    ['name' => 'Daniel Peñaloza',      'role' => null],
                    ['name' => 'Américo Gaitán',       'role' => null],
                    ['name' => 'Alma Lara',            'role' => null],
                    ['name' => 'Hernán Villarreal',    'role' => null],
                    ['name' => 'Joel Guadarrama',      'role' => null],
                    ['name' => 'Marcelo González',     'role' => null],
                    ['name' => 'Sofía Zepeda',         'role' => null],
                ],
            ],
            [
                'name'       => 'Miembros pasados',
                'slug'       => 'miembros-pasados',
                'sort_order' => 4,
                'members'    => [
                    ['name' => 'Rafael Bausone',      'role' => null],
                    ['name' => 'Ricardo Vara',        'role' => null],
                    ['name' => 'Fernanda Rodríguez',  'role' => null],
                    ['name' => 'Ramón Narváez',       'role' => null],
                    ['name' => 'Jacobo Jasso',        'role' => null],
                    ['name' => 'Carlos Ramos',        'role' => null],
                    ['name' => 'Andrés Medina',       'role' => null],
                    ['name' => 'Gerardo Buentello',   'role' => null],
                    ['name' => 'Tiffani Hensley',     'role' => null],
                    ['name' => 'Adrián Lozano',       'role' => null],
                ],
            ],
        ];

        foreach ($groups as $groupData) {
            $members = $groupData['members'];
            unset($groupData['members']);

            $group = MemberGroup::updateOrCreate(
                ['slug' => $groupData['slug']],
                $groupData
            );

            // Limpiar miembros previos del grupo para evitar duplicados
            Member::where('member_group_id', $group->id)->delete();

            foreach ($members as $i => $memberData) {
                Member::create([
                    'member_group_id'   => $group->id,
                    'name'              => $memberData['name'],
                    'role'              => $memberData['role'],
                    'featured_media_id' => $photos[array_rand($photos)],
                    'sort_order'        => $i,
                    'status'            => 'active',
                ]);
            }
        }
    }
}
