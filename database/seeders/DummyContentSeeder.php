<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sponsor;
use App\Models\SponsorPlacement;
use App\Models\SupportCampaign;
use App\Models\SupportMethod;
use App\Models\TransparencyDocument;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@escalada.com')->first()
            ?? User::first();

        // ─── Blog Posts ───────────────────────────────────────────
        $posts = [
            [
                'title'       => 'Guía de rutas en el Peñón de Turrialba',
                'slug'        => 'guia-rutas-penon-turrialba',
                'author_name' => 'Carlos Rodríguez',
                'excerpt'     => 'Descubre las mejores rutas de escalada en el Peñón de Turrialba, desde principiantes hasta expertos. Un paraíso vertical en el corazón de Costa Rica.',
                'body'        => '<p>El Peñón de Turrialba es uno de los destinos de escalada más emblemáticos de Costa Rica. Con más de 40 rutas catalogadas, ofrece opciones para todos los niveles.</p><h2>Rutas recomendadas para principiantes</h2><p>Si estás comenzando tu viaje en la escalada, las zonas bajas del Peñón ofrecen rutas de grado 5a a 5c con mucha adherencia y agarres cómodos. <em>El Debut</em> (5b) es perfecta para tu primera experiencia en roca natural.</p><h2>Sector Avanzado</h2><p>Los escaladores experimentados encontrarán desafíos en el sector Pared Norte, donde rutas como <em>La Lágrima Negra</em> (7a+) y <em>Vértigo</em> (7b) ponen a prueba la técnica y la resistencia.</p><p>Recomendamos ir acompañado de un guía local para la primera visita.</p>',
                'status'      => 'published',
                'published_at' => now()->subDays(5),
                'seo_title'   => 'Rutas de Escalada en Turrialba | Escalada Libre',
                'seo_description' => 'Guía completa de rutas de escalada en el Peñón de Turrialba, Costa Rica. Todas las grades y sectores.',
            ],
            [
                'title'       => 'Cómo elegir tu primer juego de fisureros',
                'slug'        => 'elegir-primer-juego-fisureros',
                'author_name' => 'María Solís',
                'excerpt'     => 'Los fisureros son la pieza central del equipo de escalada en fisura. Te explicamos qué buscar y cómo invertir inteligentemente en tu primer rack.',
                'body'        => '<p>Escalar en fisura es una de las disciplinas más técnicas y satisfactorias del montañismo. La elección correcta de tu rack puede marcar la diferencia entre una experiencia segura y placentera o una jornada llena de dudas.</p><h2>Tipos de fisureros</h2><p>Existen básicamente dos tipos: los <strong>Friends o Camalots</strong> (de arranque excéntrico) y los <strong>Hexes</strong> (pasivos). Para empezar, recomendamos un medio rack de Camalots tamaños 0.5, 0.75, 1 y 2.</p><h2>Marcas reconocidas</h2><ul><li>Black Diamond: excelente relación calidad-precio</li><li>Wild Country: muy populares en Europa</li><li>Metolius: ideales para fisuras paralelas</li></ul><p>Recuerda que el equipo de escalada es una inversión en tu seguridad. No comprometas calidad por ahorrar dinero.</p>',
                'status'      => 'published',
                'published_at' => now()->subDays(12),
                'seo_title'   => 'Guía para Elegir Fisureros de Escalada',
                'seo_description' => 'Todo lo que debes saber sobre fisureros y cómo construir tu primer rack para escalada en roca.',
            ],
            [
                'title'       => 'Temporada de escalada 2026: lo que viene',
                'slug'        => 'temporada-escalada-2026',
                'author_name' => 'Equipo Escalada Libre',
                'excerpt'     => 'Repasamos los eventos, expediciones y proyectos que marcarán la temporada de escalada en Costa Rica durante 2026.',
                'body'        => '<p>El año 2026 promete ser uno de los más activos para la comunidad escaladora de Costa Rica. Aquí un adelanto de lo que se viene.</p><h2>Competencias nacionales</h2><p>La Federación Nacional confirma cuatro fechas de competencia en boulder y lead, con la final nacional programada para octubre en San José.</p><h2>Apertura de nuevas rutas</h2><p>El equipo de equipadores trabaja actualmente en tres nuevas zonas en Guanacaste. Se esperan más de 20 vías nuevas de distintos grados antes de julio.</p><h2>Campamentos de verano</h2><p>Escalada Libre organiza campamentos para jóvenes escaladores de 12 a 18 años. Los cupos son limitados, inscríbete pronto.</p>',
                'status'      => 'published',
                'published_at' => now()->subDays(2),
                'seo_title'   => 'Temporada de Escalada 2026 en Costa Rica',
                'seo_description' => 'Eventos, competencias y novedades de la temporada de escalada 2026 en Costa Rica.',
            ],
            [
                'title'       => 'Técnica de pie: la base de la escalada eficiente',
                'slug'        => 'tecnica-pie-escalada-eficiente',
                'author_name' => 'Andrea Mora',
                'excerpt'     => 'El talón, el empuje, el giro… La técnica de pie determina en gran medida tu nivel en la pared. Aprende los fundamentos con estos ejercicios.',
                'body'        => '<p>Una de las diferencias más notorias entre un escalador principiante y uno avanzado es el uso del pie. Los mejores escaladores del mundo se caracterizan por una técnica de pie precisa y silenciosa.</p><h2>El pisar en punta</h2><p>Colocar la zapatilla en la punta del escalón maximiza el balance y la fuerza de empuje. Practica rutas fáciles conscientemente enfocándote solo en los pies.</p><h2>El talón hook</h2><p>El gancho de talón permite descansar los brazos en pasos técnicos. Es especialmente útil en techos y travesías.</p><h2>El smear</h2><p>En superficies lisas sin agarres visibles, el smear (frote de suela) te permite subir usando la fricción de la suela contra la roca. Requiere confianza y equilibrio.</p><p>Dedica al menos una sesión semanal exclusivamente a ejercicios de pie.</p>',
                'status'      => 'published',
                'published_at' => now()->subDays(20),
                'seo_title'   => 'Técnica de Pie en Escalada - Guía Completa',
                'seo_description' => 'Aprende las técnicas de pie fundamentales en escalada: punta, talón hook, smear y más.',
            ],
            [
                'title'       => 'Expedición al Cerro Chirripó: reporte de escalada',
                'slug'        => 'expedicion-chirripó-reporte-2026',
                'author_name' => 'Luis Jiménez',
                'excerpt'     => 'Un equipo de Escalada Libre completó la primera ascensión técnica de temporada en la pared oeste del Chirripó. Este es su reporte.',
                'body'        => '<p>En febrero de 2026, un equipo de cuatro escaladores de Escalada Libre realizó la ascensión técnica a la pared oeste del Cerro Chirripó. Durante 8 días documentaron las condiciones de la roca, la meteorología y el estado de los anclajes históricos.</p><h2>Las condiciones</h2><p>La roca ígnea de la pared oeste se encontró en excelente estado después de la época seca. Los sectores intermedios presentaron algo de humedad pero nada que impidiera el avance.</p><h2>Rutas completadas</h2><p>El equipo logró completar tres rutas: <em>Sendero de Quetzales</em> (6b, 8 largos), <em>La Cresta del Viento</em> (6c+, 11 largos) y una variante nueva sin nombre aún que será catalogada próximamente.</p><p>El informe completo con fotos y topo estará disponible en nuestra sección de documentos.</p>',
                'status'      => 'published',
                'published_at' => now()->subDays(8),
                'seo_title'   => 'Expedición Cerro Chirripó 2026 - Escalada Libre',
                'seo_description' => 'Reporte de la expedición de escalada técnica a la pared oeste del Chirripó en febrero 2026.',
            ],
            [
                'title'       => 'Mantenimiento de cuerdas dinámicas: guía práctica',
                'slug'        => 'mantenimiento-cuerdas-dinamicas',
                'author_name' => 'María Solís',
                'excerpt'     => 'Saber cuándo retirar tu cuerda de escalada puede salvarte la vida. Aquí te explicamos cómo inspeccionarla y cuándo es hora de reemplazarla.',
                'body'        => '<p>Una cuerda dinámica es el componente más crítico de tu sistema de seguridad en escalada. Conocer su vida útil y cómo inspeccionarla regularmente es responsabilidad de todo escalador.</p><h2>Inspección visual</h2><p>Recorre la cuerda palmo a palmo buscando deformaciones en la funda: partes blandas, irregularidades o fibras cortadas son señales de descarte inmediato.</p><h2>Vida útil aproximada</h2><ul><li>Uso ocasional (1-2 veces por mes): 5-7 años</li><li>Uso regular (fin de semana): 3-5 años</li><li>Uso intensivo (varios días por semana): 1-3 años</li><li>Después de una caída factor 2: inspección inmediata, considerar sustitución</li></ul><h2>Almacenamiento correcto</h2><p>Guarda la cuerda alejada de productos químicos, luz solar directa y calor. Un bolso específico para cuerda es la mejor inversión.</p>',
                'status'      => 'published',
                'published_at' => now()->subDays(30),
                'seo_title'   => 'Cómo Mantener tu Cuerda de Escalada',
                'seo_description' => 'Guía práctica para inspeccionar, mantener y saber cuándo cambiar tu cuerda dinámica de escalada.',
            ],
            [
                'title'       => 'Yoga para escaladores: movilidad y recuperación',
                'slug'        => 'yoga-escaladores-movilidad',
                'author_name' => 'Daniela Castro',
                'excerpt'     => 'Incorporar yoga a tu rutina de escalada mejora la flexibilidad, la fuerza de core y acelera la recuperación muscular. Te mostramos por dónde empezar.',
                'body'        => '<p>La escalada exige fuerza, flexibilidad y equilibrio mental. El yoga es el complemento perfecto que muchos escaladores de élite ya practican regularmente.</p><h2>Beneficios concretos</h2><p>La mejora en la apertura de cadera te permite hacer pasos de pie más altos con menos esfuerzo. El fortalecimiento del core mejora tu postura en la pared y reduce fatiga en los brazos.</p><h2>Rutina básica pre-escalada (15 min)</h2><ol><li>Saludo al sol x 5</li><li>Perro mirando hacia abajo, 1 min</li><li>Postura del guerrero I y II, 30 seg cada lado</li><li>Paloma, 2 min cada lado</li><li>Estiramiento de muñecas y dedos, 3 min</li></ol><p>Prueba esta rutina antes de tu próxima sesión y notarás la diferencia desde el primer día.</p>',
                'status'      => 'published',
                'published_at' => now()->subDays(45),
                'seo_title'   => 'Yoga para Escaladores | Escalada Libre',
                'seo_description' => 'Cómo usar el yoga para mejorar tu escalada: movilidad, core y recuperación.',
            ],
            [
                'title'       => 'Escalada en bloque: entrenamiento para la fuerza de agarre',
                'slug'        => 'boulder-entrenamiento-fuerza-agarre',
                'author_name' => 'Carlos Rodríguez',
                'excerpt'     => 'El boulder es la disciplina ideal para desarrollar potencia y fuerza de agarre rápidamente. Estos ejercicios específicos te ayudarán a progresar.',
                'body'        => '<p>La escalada en bloque (boulder) es quizás la forma más pura de escalada: sin cuerda, sin arnés, solo tú y la roca. Pero también es el mejor laboratorio para mejorar tu fuerza de agarre y potencia.</p><h2>El campus board</h2><p>El campus board (listones de madera escalonados) es el instrumento más utilizado por los escaladores de competición para desarrollar potencia explosiva. Ojo: no recomendado para menores de 18 años ni escaladores con menos de 2 años de experiencia.</p><h2>Ejercicios de hangboard</h2><p>El dead hang (colgarse muerto) en listones de 20mm durante series de 10 segundos con 3 minutos de descanso es una base sólida. Progresa gradualmente añadiendo peso con cinturón.</p><h2>Trabaja los tipos de agarre</h2><ul><li>Full crimp: máxima fuerza, mayor riesgo de lesión</li><li>Half crimp: el más versátil</li><li>Open hand: el más seguro y funcional para roca real</li></ul>',
                'status'      => 'draft',
                'published_at' => null,
                'seo_title'   => 'Entrenamiento de Fuerza para Boulder',
                'seo_description' => 'Ejercicios específicos para mejorar la fuerza de agarre y potencia en escalada en bloque.',
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::firstOrCreate(['slug' => $post['slug']], $post);
        }

        // ─── Mensajes de Contacto (simulados) ─────────────────────
        $messages = [
            ['name' => 'Sofía Vargas',     'email' => 'sofia.vargas@gmail.com',  'phone' => '+506 8811-2233', 'subject' => 'Inscripción campamento verano', 'message' => 'Hola, quisiera información sobre el campamento de verano para mi hija de 14 años. ¿Qué incluye y cuál es el costo?', 'status' => 'new'],
            ['name' => 'Ricardo Monge',    'email' => 'rmonge@hotmail.com',      'phone' => null,             'subject' => 'Patrocinio empresarial',        'message' => 'Representamos a una empresa deportiva interesada en patrocinar actividades de escalada. ¿Con quién podemos hablar?', 'status' => 'read'],
            ['name' => 'Valentina Rojas',  'email' => 'valerojas@outlook.com',   'phone' => '+506 7720-4455', 'subject' => 'Clases para adultos',           'message' => 'Soy completamente principiante, tengo 32 años. ¿Tienen clases para adultos desde cero? ¿Cuándo empieza el próximo grupo?', 'status' => 'replied'],
            ['name' => 'Andrés Campos',    'email' => 'andres.c@gmail.com',      'phone' => '+506 8899-1122', 'subject' => 'Donación de equipo',            'message' => 'Tengo equipo de escalada usado pero en buen estado: cuerda, arneses y zapatos. ¿Los aceptan como donación?', 'status' => 'replied'],
            ['name' => 'Paula Hernández',  'email' => 'paulahern@gmail.com',     'phone' => null,             'subject' => 'Voluntariado en eventos',       'message' => '¡Hola! Me gustaría saber si hay oportunidades de voluntariado en sus próximos eventos de escalada.', 'status' => 'new'],
            ['name' => 'Marco Ureña',      'email' => 'marco.urena@gmail.com',   'phone' => '+506 6644-7788', 'subject' => 'Error en el sitio web',         'message' => 'La página de transparencia no carga correctamente en mi navegador Firefox. ¿Podrían revisarlo?', 'status' => 'read'],
        ];

        foreach ($messages as $msg) {
            ContactMessage::create($msg);
        }

        // ─── Patrocinadores ────────────────────────────────────────
        $sponsors = [
            [
                'name'        => 'Montaña Gear CR',
                'slug'        => 'montana-gear-cr',
                'description' => 'Tienda especializada en equipamiento de escalada y montañismo con las mejores marcas internacionales. 15 años apoyando a la comunidad escaladora de Costa Rica.',
                'website_url' => 'https://montanagear.cr',
                'status'      => 'active',
            ],
            [
                'name'        => 'Banco Deportivo Nacional',
                'slug'        => 'banco-deportivo-nacional',
                'description' => 'Organización financiera comprometida con el desarrollo del deporte amateur en Costa Rica. Patrocinador principal de las competencias nacionales.',
                'website_url' => 'https://bdn.cr',
                'status'      => 'active',
            ],
            [
                'name'        => 'Vértigo Cafés',
                'slug'        => 'vertigo-cafes',
                'description' => 'Cadena de cafeterías con temática de escalada presentes en San José, Heredia y Cartago. El punto de encuentro de la comunidad escaladora.',
                'website_url' => 'https://vertigocafes.com',
                'status'      => 'active',
            ],
            [
                'name'        => 'SportLife CR',
                'slug'        => 'sportlife-cr',
                'description' => 'Marca de ropa deportiva de alto rendimiento fabricada en Costa Rica. Proveedora oficial de uniformes del equipo nacional de escalada.',
                'website_url' => 'https://sportlifecr.com',
                'status'      => 'active',
            ],
            [
                'name'        => 'Tika Outdoor',
                'slug'        => 'tika-outdoor',
                'description' => 'Importadora y distribuidora oficial de marcas como Petzl, La Sportiva y Mammut en Centroamérica.',
                'website_url' => 'https://tikaoutdoor.com',
                'status'      => 'active',
            ],
            [
                'name'        => 'Clínica Deportiva Valle Verde',
                'slug'        => 'clinica-deportiva-valle-verde',
                'description' => 'Clínica especializada en medicina deportiva y fisioterapia para deportes de alto rendimiento. Médicos del equipo nacional.',
                'website_url' => 'https://valleverde.cr',
                'status'      => 'inactive',
            ],
        ];

        $sponsorModels = [];
        foreach ($sponsors as $sp) {
            $sponsorModels[$sp['slug']] = Sponsor::firstOrCreate(['slug' => $sp['slug']], $sp);
        }

        // ─── Sponsor Placements ───────────────────────────────────
        $placements = [
            [
                'sponsor_id'  => $sponsorModels['montana-gear-cr']->id,
                'placement'   => 'hero',
                'title'       => 'Equípa tu pasión',
                'body'        => 'Encuentra todo lo que necesitas para escalar en Montaña Gear CR. Envíos a todo Costa Rica.',
                'link_url'    => 'https://montanagear.cr',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'sponsor_id'  => $sponsorModels['banco-deportivo-nacional']->id,
                'placement'   => 'hero',
                'title'       => 'Impulsamos el deporte tico',
                'body'        => 'Banco Deportivo Nacional, comprometido con el desarrollo atlético de Costa Rica.',
                'link_url'    => 'https://bdn.cr',
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'sponsor_id'  => $sponsorModels['vertigo-cafes']->id,
                'placement'   => 'sidebar',
                'title'       => 'Vértigo Cafés',
                'body'        => 'Tu café después de escalar. Visítanos en San José, Heredia y Cartago.',
                'link_url'    => 'https://vertigocafes.com',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'sponsor_id'  => $sponsorModels['tika-outdoor']->id,
                'placement'   => 'footer',
                'title'       => 'Distribuidores Petzl | La Sportiva | Mammut',
                'body'        => 'Las mejores marcas de escalada disponibles en Centroamérica.',
                'link_url'    => 'https://tikaoutdoor.com',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
        ];

        foreach ($placements as $pl) {
            SponsorPlacement::create($pl);
        }

        // ─── Categorías de Producto ────────────────────────────────
        $categories = [
            ['name' => 'Equipamiento de Seguridad', 'slug' => 'equipamiento-seguridad',  'description' => 'Cuerdas, arneses, cascos y todo el equipo de protección para escalar con seguridad.'],
            ['name' => 'Calzado',                   'slug' => 'calzado',                  'description' => 'Zapatillas de escalada para todo tipo de terreno y nivel.'],
            ['name' => 'Ropa Técnica',               'slug' => 'ropa-tecnica',             'description' => 'Camisetas, pantalones y ropa de alto rendimiento para escalar.'],
            ['name' => 'Accesorios y Complementos', 'slug' => 'accesorios-complementos',  'description' => 'Magnesio, bolsas de magnesio, cepillos de boulder y más.'],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['slug']] = ProductCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // ─── Productos ─────────────────────────────────────────────
        $products = [
            [
                'category_id'  => $catModels['equipamiento-seguridad']->id,
                'name'         => 'Cuerda Dinámica 9.8mm × 60m',
                'slug'         => 'cuerda-dinamica-9-8mm-60m',
                'summary'      => 'Cuerda dinámica de uso simple certificada UIAA. Ideal para escalada deportiva y tradicional.',
                'description'  => '<p>Cuerda dinámica de la más alta calidad, con funda tratada con DryCore para mayor durabilidad y resistencia a la humedad. Certificación UIAA y CE. Peso: 59g/m.</p><ul><li>Diámetro: 9.8mm</li><li>Longitud: 60m</li><li>Caídas UIAA: 8</li><li>Fuerza de choque: &lt;9kN</li></ul>',
                'price'        => 185000,
                'currency'     => 'CRC',
                'status'       => 'published',
            ],
            [
                'category_id'  => $catModels['equipamiento-seguridad']->id,
                'name'         => 'Arnés de Escalada Sport Pro',
                'slug'         => 'arnes-escalada-sport-pro',
                'summary'      => 'Arnés cómodo y versátil para escalada deportiva. Ajuste rápido y ventilación excelente.',
                'description'  => '<p>Arnés ligero con acolchado doble en cintura y perneras. Sistema de ajuste Dual Back Clip para perfecta estabilidad. Incluye 4 portamateriales y portacuerda.</p>',
                'price'        => 62000,
                'currency'     => 'CRC',
                'status'       => 'published',
            ],
            [
                'category_id'  => $catModels['equipamiento-seguridad']->id,
                'name'         => 'Casco de Escalada Ventis',
                'slug'         => 'casco-escalada-ventis',
                'summary'      => 'Casco ultra ligero (240g) con ventilación optimizada. Para escalada en roca y alpinismo.',
                'description'  => '<p>Casco de carcasa dura con forro interior EPS. Sistema de ajuste rotatorio para una talla única ajustable. Color: Rojo/Negro.</p>',
                'price'        => 48000,
                'currency'     => 'CRC',
                'status'       => 'published',
            ],
            [
                'category_id'  => $catModels['calzado']->id,
                'name'         => 'Zapatilla La Solución Velcro',
                'slug'         => 'zapatilla-la-solucion-velcro',
                'summary'      => 'Zapatilla de rendimiento máximo para boulder y rutas técnicas. Plantilla asimétrica de alta precisión.',
                'description'  => '<p>La solución ideal para los proyectos más difíciles. Suela Vibram XS Grip2, horma muy agresiva con caída de talón pronunciada. Cierre de velcro para ajuste rápido.</p>',
                'price'        => 99000,
                'currency'     => 'CRC',
                'status'       => 'published',
            ],
            [
                'category_id'  => $catModels['calzado']->id,
                'name'         => 'Zapatilla Beginner Neutral',
                'slug'         => 'zapatilla-beginner-neutral',
                'summary'      => 'La zapatilla perfecta para empezar. Cómoda, versátil y adecuada para toda una sesión.',
                'description'  => '<p>Horma neutral con plantilla acolchada para mayor comodidad. Ideal para el gimnasio y primeras salidas en roca. Cierre con cordones.</p>',
                'price'        => 55000,
                'currency'     => 'CRC',
                'status'       => 'published',
            ],
            [
                'category_id'  => $catModels['ropa-tecnica']->id,
                'name'         => 'Camiseta Técnica Vías Locales',
                'slug'         => 'camiseta-tecnica-vias-locales',
                'summary'      => 'Camiseta de escalada con mapa de rutas históricas de Costa Rica. Tela técnica anti-olor.',
                'description'  => '<p>100% poliéster reciclado con tratamiento anti-olor Polygiene. Diseño exclusivo con las rutas clásicas de Costa Rica. Disponible en tallas XS–XXL.</p>',
                'price'        => 28000,
                'currency'     => 'CRC',
                'status'       => 'published',
            ],
            [
                'category_id'  => $catModels['ropa-tecnica']->id,
                'name'         => 'Shorts de Escalada FlexMove',
                'slug'         => 'shorts-escalada-flexmove',
                'summary'      => 'Shorts con 4-way stretch para máxima movilidad en la pared. Bolsillo con cremallera.',
                'description'  => '<p>Fabricados con tejido elástico en las cuatro direcciones. Cintura elástica con cordón. Secan rápido y son perfectos para escalar en climas cálidos.</p>',
                'price'        => 35000,
                'currency'     => 'CRC',
                'status'       => 'published',
            ],
            [
                'category_id'  => $catModels['accesorios-complementos']->id,
                'name'         => 'Magnesera Chalk Bag Boulder Pro',
                'slug'         => 'magnesera-chalk-bag-boulder-pro',
                'summary'      => 'Magnesera amplia con forro interior liso y cierre con cordón. Perfecta para boulder y deportiva.',
                'description'  => '<p>Diámetro de boca: 14cm. Gancho y cinturón incluidos. Interior de tela lisa para mejor distribución del magnesio. Varios colores disponibles.</p>',
                'price'        => 18000,
                'currency'     => 'CRC',
                'status'       => 'published',
            ],
            [
                'category_id'  => $catModels['accesorios-complementos']->id,
                'name'         => 'Magnesio Puro 250g',
                'slug'         => 'magnesio-puro-250g',
                'summary'      => 'Magnesio de carbonato de alta pureza para escalada. Sin aditivos ni fragancia.',
                'description'  => '<p>Presentación en bloque para mayor duración. Sin talco ni perfume. Ideal para personas con piel sensible.</p>',
                'price'        => 6500,
                'currency'     => 'CRC',
                'status'       => 'published',
            ],
            [
                'category_id'  => $catModels['accesorios-complementos']->id,
                'name'         => 'Kit de 5 Mosquetones Exprés',
                'slug'         => 'kit-5-mosquetones-express',
                'summary'      => 'Set de 5 extres listos para usar. Mosquetones de aluminio forjado con cintas Dyneema.',
                'description'  => '<p>Mosquetones anodizados color rojo + negro con apertura de gatillo y seguro automático en el de la cuerda. Peso total: 280g.</p>',
                'price'        => 42000,
                'currency'     => 'CRC',
                'status'       => 'published',
            ],
        ];

        foreach ($products as $prod) {
            Product::firstOrCreate(['slug' => $prod['slug']], array_merge($prod, [
                'user_id' => $admin?->id,
            ]));
        }

        // ─── Documentos de Transparencia ──────────────────────────
        $docs = [
            ['title' => 'Informe Anual 2024',                        'slug' => 'informe-anual-2024',                    'year' => 2024, 'type' => 'annual',     'description' => 'Resumen de actividades, logros y estadísticas de Escalada Libre durante el año 2024.',          'status' => 'published', 'published_at' => '2025-03-15 00:00:00'],
            ['title' => 'Estado Financiero 2024',                    'slug' => 'estado-financiero-2024',                'year' => 2024, 'type' => 'financial',  'description' => 'Balance general, estado de resultados y notas a los estados financieros auditados 2024.',       'status' => 'published', 'published_at' => '2025-03-15 00:00:00'],
            ['title' => 'Acta de Asamblea General Ordinaria 2024',  'slug' => 'acta-asamblea-general-2024',            'year' => 2024, 'type' => 'legal',      'description' => 'Acta oficial de la Asamblea General Ordinaria celebrada el 15 de enero de 2025.',              'status' => 'published', 'published_at' => '2025-01-20 00:00:00'],
            ['title' => 'Informe de Operaciones 2024',               'slug' => 'informe-operaciones-2024',              'year' => 2024, 'type' => 'operations', 'description' => 'Detalle de programas, eventos, participantes y actividades operativas del año 2024.',            'status' => 'published', 'published_at' => '2025-02-10 00:00:00'],
            ['title' => 'Informe Anual 2023',                        'slug' => 'informe-anual-2023',                    'year' => 2023, 'type' => 'annual',     'description' => 'Resumen de actividades, logros y estadísticas de Escalada Libre durante el año 2023.',          'status' => 'published', 'published_at' => '2024-03-10 00:00:00'],
            ['title' => 'Estado Financiero 2023',                    'slug' => 'estado-financiero-2023',                'year' => 2023, 'type' => 'financial',  'description' => 'Balance general y estado de resultados auditados del ejercicio fiscal 2023.',                  'status' => 'published', 'published_at' => '2024-03-10 00:00:00'],
            ['title' => 'Informe de Operaciones 2023',               'slug' => 'informe-operaciones-2023',              'year' => 2023, 'type' => 'operations', 'description' => 'Actividades, talleres, competencias y programas desarrollados durante 2023.',                   'status' => 'published', 'published_at' => '2024-02-05 00:00:00'],
            ['title' => 'Informe Anual 2025 (Borrador)',             'slug' => 'informe-anual-2025-borrador',           'year' => 2025, 'type' => 'annual',     'description' => 'Borrador preliminar del informe anual 2025. Pendiente de aprobación en Asamblea.',            'status' => 'draft',      'published_at' => null],
        ];

        foreach ($docs as $doc) {
            TransparencyDocument::firstOrCreate(['slug' => $doc['slug']], $doc);
        }

        // ─── Campañas de Apoyo ───────────────────────────────────
        $campaign1 = SupportCampaign::firstOrCreate(['slug' => 'equipamiento-jovenes'], [
            'name'        => 'Equipamiento para Jóvenes Escaladores',
            'slug'        => 'equipamiento-jovenes',
            'description' => 'Ayúdanos a dotar de equipo de escalada a jóvenes talentos de comunidades de escasos recursos en Costa Rica. Con tu apoyo podemos llegar a más gimnasios y escuelas.',
            'status'      => 'active',
            'start_at'    => now()->subMonths(2),
            'end_at'      => now()->addMonths(4),
        ]);

        $campaign2 = SupportCampaign::firstOrCreate(['slug' => 'infraestructura-muro-exterior'], [
            'name'        => 'Muro de Escalada al Aire Libre',
            'slug'        => 'infraestructura-muro-exterior',
            'description' => 'Proyecto para construir un muro de escalada público al aire libre en la comunidad de Turrialba. Acceso gratuito para todos.',
            'status'      => 'active',
            'start_at'    => now()->subMonth(),
            'end_at'      => now()->addMonths(8),
        ]);

        // ─── Métodos de Apoyo ─────────────────────────────────────
        $methods = [
            [
                'campaign_id' => $campaign1->id,
                'type'        => 'paypal',
                'title'       => 'Donación por PayPal',
                'body'        => 'Realiza una donación única o recurrente de forma rápida y segura a través de PayPal. Aceptamos todas las tarjetas de crédito.',
                'settings'    => ['paypal_link' => 'https://paypal.me/escaladalibre', 'suggested_amount' => 20],
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'campaign_id' => $campaign1->id,
                'type'        => 'bank_transfer',
                'title'       => 'Transferencia Bancaria',
                'body'        => 'Transfiere directamente a nuestra cuenta en el Banco Nacional. Recuerda enviarnos el comprobante para registrar tu donación.',
                'settings'    => [
                    'bank'    => 'Banco Nacional de Costa Rica',
                    'account' => '100-01-150-123456-7',
                    'iban'    => 'CR21015000110000012345',
                    'name'    => 'Asociación Escalada Libre CR',
                ],
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'campaign_id' => $campaign1->id,
                'type'        => 'gym_partner',
                'title'       => 'Socio Gimnasio',
                'body'        => 'Si eres propietario de un gimnasio de escalada, considera destinar el 5% de tus membresías al fondo de equipamiento para jóvenes.',
                'settings'    => ['contact_email' => 'alianzas@escaladalibre.cr'],
                'sort_order'  => 3,
                'is_active'   => true,
            ],
            [
                'campaign_id' => $campaign2->id,
                'type'        => 'paypal',
                'title'       => 'Dona para el Muro Exterior',
                'body'        => 'Cada colón cuenta para construir un espacio público de escalada en Turrialba. Dona desde $5.',
                'settings'    => ['paypal_link' => 'https://paypal.me/murotiika', 'suggested_amount' => 10],
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'campaign_id' => $campaign2->id,
                'type'        => 'bank_transfer',
                'title'       => 'Transferencia — Muro Turrialba',
                'body'        => 'Transfiere a la cuenta especial del proyecto muro. Todos los fondos se destinan exclusivamente a la construcción.',
                'settings'    => [
                    'bank'    => 'Banco de Costa Rica',
                    'account' => '001-654321-0',
                    'iban'    => 'CR05015200001654321',
                    'name'    => 'Asociación Escalada Libre CR - Fondo Muro',
                ],
                'sort_order'  => 2,
                'is_active'   => true,
            ],
        ];

        foreach ($methods as $method) {
            SupportMethod::create($method);
        }

        $this->command->info('✅ Contenido dummy creado exitosamente.');
        $this->command->info('   - Blog posts: ' . BlogPost::count());
        $this->command->info('   - Mensajes de contacto: ' . ContactMessage::count());
        $this->command->info('   - Patrocinadores: ' . Sponsor::count());
        $this->command->info('   - Productos: ' . Product::count());
        $this->command->info('   - Documentos de transparencia: ' . TransparencyDocument::count());
        $this->command->info('   - Campañas de apoyo: ' . SupportCampaign::count());
    }
}
