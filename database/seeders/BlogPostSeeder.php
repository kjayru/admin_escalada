<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Nos reunimos con el gobierno de Nuevo León',
                'slug' => 'nos-reunimos-con-el-gobierno-de-nuevo-leon',
                'category' => 'blog',
                'author_name' => 'Escalada Libre',
                'excerpt' => 'Con el fin de la gestión integral de la Huasteca zona natural protegida de Nuevo León',
                'body' => '<p>En un importante avance para la gestión y conservación de las áreas naturales protegidas, representantes de Escalada Libre México A.C. se reunieron con autoridades del gobierno de Nuevo León para discutir el futuro de la zona natural protegida de la Huasteca.</p><p>Durante la reunión se abordaron temas cruciales como la gestión integral del área, el mantenimiento de rutas de escalada, la señalización adecuada y la educación ambiental para visitantes. Las autoridades mostraron gran interés en trabajar en conjunto con la asociación para garantizar el uso sustentable y responsable de esta importante área natural.</p><p>Este tipo de colaboración entre organizaciones civiles y gobierno es fundamental para el desarrollo sustentable de la escalada en México y la conservación de nuestros ecosistemas.</p>',
                'status' => 'published',
                'is_featured' => true,
                'published_at' => '2026-03-15 10:00:00',
            ],
            [
                'title' => 'Todo para escaladores',
                'slug' => 'todo-para-escaladores-tensafest',
                'category' => 'eventos',
                'author_name' => 'Escalada Libre',
                'excerpt' => 'El fin de semana se realiza el evento TensaFest dirigido a deportistas que practican la escalada...',
                'body' => '<p>Este fin de semana se llevará a cabo el esperado evento TensaFest, un festival dedicado completamente a los amantes de la escalada en slackline y highline.</p><p>El evento contará con diferentes actividades:</p><ul><li>Talleres de técnicas de tensado</li><li>Competencias de slackline</li><li>Demostraciones de highline</li><li>Charlas sobre seguridad</li><li>Venta de equipo especializado</li></ul><p>Los organizadores esperan la participación de más de 200 deportistas de diferentes estados del país. Las inscripciones aún están abiertas.</p>',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => '2026-03-10 14:00:00',
            ],
            [
                'title' => 'Exposición fotográfica',
                'slug' => 'exposicion-fotografica',
                'category' => 'eventos',
                'author_name' => 'Escalada Libre',
                'excerpt' => 'Con gran respuesta de la comunidad, hemos realizado una exposición fotográfica de las montañas...',
                'body' => '<p>Con gran éxito de convocatoria, se inauguró la exposición fotográfica "Montañas de México: Perspectivas Verticales", organizada por Escalada Libre México A.C.</p><p>La exposición cuenta con más de 50 fotografías de diferentes escaladores y fotógrafos que han capturado la belleza de las montañas y paredes de escalada en México. Desde el imponente Pico de Orizaba hasta las paredes de caliza de Potrero Chico, la muestra ofrece una perspectiva única del país desde la mirada de quienes lo escalan.</p><p>La exposición estará abierta al público durante todo el mes en el Centro Cultural, con entrada libre.</p>',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => '2026-03-05 11:00:00',
            ],
            [
                'title' => 'Jornada de limpieza en Potrero Chico',
                'slug' => 'jornada-limpieza-potrero-chico',
                'category' => 'eventos',
                'author_name' => 'Escalada Libre',
                'excerpt' => 'Convocamos a la comunidad escaladora a participar en la jornada de limpieza y mantenimiento de senderos',
                'body' => '<p>El próximo sábado realizaremos una jornada de limpieza en el Parque Nacional El Potrero Chico. Invitamos a toda la comunidad escaladora a participar en esta importante actividad de conservación.</p><p>Actividades programadas:</p><ul><li>Limpieza de senderos de acceso</li><li>Recolección de residuos</li><li>Mantenimiento de áreas de camping</li><li>Instalación de señalética educativa</li></ul><p>Punto de encuentro: Estacionamiento principal a las 8:00 AM. Se proporcionarán bolsas y guantes. ¡Tu participación hace la diferencia!</p>',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => '2026-02-28 09:00:00',
            ],
            [
                'title' => 'Clínica de escalada en roca para principiantes',
                'slug' => 'clinica-escalada-principiantes',
                'category' => 'eventos',
                'author_name' => 'Escalada Libre',
                'excerpt' => 'Aprende las técnicas básicas de escalada en roca con instructores certificados',
                'body' => '<p>Escalada Libre México A.C. presenta su clínica de iniciación a la escalada en roca, diseñada especialmente para personas que desean iniciarse en este emocionante deporte.</p><p>La clínica cubrirá:</p><ul><li>Equipo básico y su uso correcto</li><li>Técnicas fundamentales de escalada</li><li>Sistemas de aseguramiento</li><li>Nudos esenciales</li><li>Seguridad en la roca</li></ul><p>Cupo limitado a 15 participantes. Incluye todo el equipo técnico necesario.</p>',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => '2026-02-20 10:00:00',
            ],
            [
                'title' => 'Nueva guía de rutas de La Huasteca publicada',
                'slug' => 'nueva-guia-rutas-huasteca',
                'category' => 'noticias',
                'author_name' => 'Escalada Libre',
                'excerpt' => 'Después de dos años de trabajo, presentamos la guía más completa de rutas de escalada en La Huasteca',
                'body' => '<p>Nos complace anunciar la publicación de la nueva guía de rutas de escalada de La Huasteca, fruto de dos años de arduo trabajo de mapeo, documentación y fotografía.</p><p>La guía incluye:</p><ul><li>Más de 300 rutas documentadas</li><li>Fotografías de cada sector</li><li>Diagramas detallados (topos)</li><li>Información de acceso</li><li>Recomendaciones de equipo</li><li>Historia de las vías</li></ul><p>Disponible en formato digital e impreso a través de nuestra página web.</p>',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => '2026-02-15 12:00:00',
            ],
            [
                'title' => 'Acuerdo de conservación con CONANP',
                'slug' => 'acuerdo-conservacion-conanp',
                'category' => 'blog',
                'author_name' => 'Escalada Libre',
                'excerpt' => 'Escalada Libre firma importante acuerdo con la Comisión Nacional de Áreas Naturales Protegidas',
                'body' => '<p>En un hito importante para la escalada sustentable en México, Escalada Libre México A.C. ha firmado un convenio de colaboración con la CONANP para trabajar en conjunto en la conservación de áreas naturales protegidas donde se practica la escalada.</p><p>El acuerdo establece compromisos mutuos para:</p><ul><li>Monitoreo de impacto ambiental</li><li>Programas de educación ambiental</li><li>Mantenimiento de infraestructura</li><li>Investigación científica</li></ul><p>Este tipo de alianzas son fundamentales para garantizar que las futuras generaciones puedan seguir disfrutando de estos espacios naturales.</p>',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => '2026-01-30 15:00:00',
            ],
            [
                'title' => 'Reequipamiento de sector El Surf en Potrero',
                'slug' => 'reequipamiento-sector-el-surf',
                'category' => 'noticias',
                'author_name' => 'Escalada Libre',
                'excerpt' => 'Se completó el reequipamiento del popular sector El Surf con anclajes de acero inoxidable',
                'body' => '<p>Nos complace informar que se ha completado exitosamente el proyecto de reequipamiento del sector El Surf en Potrero Chico, uno de los sectores más populares entre escaladores locales e internacionales.</p><p>El proyecto incluyó:</p><ul><li>Reemplazo de 45 anclajes antiguos</li><li>Instalación de chapas de acero inoxidable</li><li>Revisión de cadenas de reunión</li><li>Limpieza de vegetación en accesos</li></ul><p>Agradecemos a todos los voluntarios y donadores que hicieron posible este proyecto.</p>',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => '2026-01-15 11:00:00',
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::create($post);
        }
    }
}
