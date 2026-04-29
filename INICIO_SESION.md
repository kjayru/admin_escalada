# 🏔 EscaladaPro — Documento de Inicio de Sesión
> **Última actualización:** 18 de abril de 2026  
> **Propósito:** Documento maestro para retomar el desarrollo en cualquier momento. Lee esto antes de escribir una sola línea de código.

---

## 1. Resumen del Proyecto

**EscaladaPro** es una plataforma de contenido para la comunidad de escalada en México. Tiene dos repositorios activos y uno **deprecado** que debes ignorar:

| Repositorio | Ruta | Tecnología | Rol |
|---|---|---|---|
| `escaladapro-api` | `/Volumes/REDHARDISK/PROYECTOS/escaladapro-api` | Laravel 12 + Filament 3 | Backend API + Panel Admin |
| `escaladapro/web` | `/Users/wiletinoco/VUE/escaladapro/web` | Nuxt 4 + Vue 3 + Tailwind | Frontend público |
| ~~`escaladapro-web`~~ | — | — | ⛔ DEPRECADO — ignorar |

---

## 2. Cómo Levantar el Entorno

```bash
# ─── API (Laravel) ──────────────────────────────────────────────────────────
cd /Volumes/REDHARDISK/PROYECTOS/escaladapro-api
php artisan serve
# o si usas Valet/Herd: dominio https://escaladapro-api.test

# ─── Frontend (Nuxt) ────────────────────────────────────────────────────────
cd /Users/wiletinoco/VUE/escaladapro/web
npm run dev
# → http://localhost:3000

# ─── Panel Admin ────────────────────────────────────────────────────────────
# https://escaladapro-api.test/admin
# Usuario: admin@escalada.com
# Contraseña: r3d3nc10n
```

---

## 3. Stack Tecnológico

| Capa | Tecnología | Versión |
|---|---|---|
| **Backend** | Laravel | 12.x |
| | PHP | 8.2+ |
| | Sanctum | 4.x |
| | Filament (Admin) | 3.x |
| | Pest (Tests) | 4.x |
| **Frontend** | Nuxt | 4.3+ |
| | Vue | 3.5+ |
| | Tailwind CSS | 6.x |
| | Swiper | 12.x |
| | TypeScript | nativo |
| **Base de Datos** | MySQL / SQLite | — |
| **Auth** | Sanctum SPA + Bearer token | — |

---

## 4. Estado Actual del Proyecto (18 abril 2026)

### 4.1 Fases Globales

| Fase | Descripción | Estado |
|---|---|---|
| **Fase 0** | Corrección de bugs críticos en la API | ✅ COMPLETADA |
| **Fase 1** | Fundamentos del frontend (composable + tipos) | ✅ COMPLETADA |
| **Fase 2** | Conectar páginas públicas a la API | 🔄 EN PROGRESO |
| **Fase 3** | Endpoint gyms + Settings/Menus dinámicos | ⏳ Pendiente |
| **Fase 4** | Tests, seeders adicionales y SEO | ⏳ Pendiente |
| **Fase 5** | Despliegue a producción | ⏳ Pendiente |

### 4.2 Estado de Páginas del Frontend

| Ruta Nuxt | Archivo | UI | API Integrada | Observación |
|---|---|---|---|---|
| `/` | `pages/index.vue` | ✅ | ✅ parcial | Patrocinadores dinámicos OK; hero hardcodeado |
| `/nosotros` | `pages/nosotros.vue` | ✅ | ❌ | Estático |
| `/actividades` | `pages/actividades.vue` | ✅ | ❌ | Hardcodeado |
| `/historia` | `pages/historia.vue` | ✅ | ❌ | Estático |
| `/blog` | `pages/blog/index.vue` | ✅ | ✅ | Patrocinadores dinámicos |
| `/blog/all` | `pages/blog/all.vue` | ✅ | ❌ | Paginación local, datos duros |
| `/blog/[slug]` | `pages/blog/[slug].vue` | ✅ | ✅ | Implementado y funcional |
| `/como-apoyar` | `pages/como-apoyar/index.vue` | ✅ | ✅ | Fetch dinámico por slug `como-apoyar-home` |
| `/como-apoyar/paypal` | `pages/como-apoyar/paypal.vue` | ✅ | ❌ | Handler vacío |
| `/como-apoyar/transferencia` | `pages/como-apoyar/transferencia.vue` | ✅ | ❌ | Datos bancarios fijos |
| `/como-apoyar/productos` | `pages/como-apoyar/productos/index.vue` | ✅ | ❌ | Hardcodeado |
| `/como-apoyar/productos/[slug]` | `pages/como-apoyar/productos/[slug].vue` | ✅ | ❌ | — |
| `/como-apoyar/gyms` | `pages/como-apoyar/gyms.vue` | ✅ | ❌ | ⚠️ Endpoint gyms no existe en API aún |
| `/patrocinio` | `pages/patrocinio.vue` | ✅ | ❌ | Estático |
| `/patrocinador/[slug]` | `pages/patrocinador/[slug].vue` | ✅ | ⚠️ | Verificar estado |
| `/transparencia` | `pages/transparencia.vue` | ✅ | ✅ | Acordeón con `doc.file?.url` |
| `/contacto` | `pages/contacto.vue` | ✅ | ❌ | Formulario sin handler |

### 4.3 Estado del Panel Filament (Admin)

| Resource | Estado | Notas |
|---|---|---|
| `MediaResource` | ✅ 100% | Biblioteca de medios |
| `PageResource` | ✅ 100% | Con repeater de secciones |
| `BlogPostResource` | ✅ Personalizado | Categorías blog/eventos/noticias, slug auto, imagen destacada |
| `TransparencyDocumentResource` | ✅ Personalizado | Tipos: asambleas/reportes/estados |
| `SupportMethodResource` | ✅ Nuevo | Sin botones Crear/Eliminar, scoped a `como-apoyar-home` |
| `ProductResource` | ⚠️ Básico | Falta galería, gestión de inquiries |
| `ProductCategoryResource` | ⚠️ Básico | — |
| `SponsorResource` | ⚠️ Básico | — |
| `MenuResource` | ⚠️ Básico | Estructura jerárquica pendiente |
| `ContactMessageResource` | ⚠️ Básico | Solo lectura, filtros pendientes |

---

## 5. Endpoints API — Estado

Base URL: `https://escaladapro-api.test/api/v1`

| # | Método | Endpoint | Estado API | Nuxt consume |
|---|---|---|---|---|
| 1 | GET | `/blog` | ✅ | ✅ |
| 2 | GET | `/blog/{slug}` | ✅ | ✅ |
| 3 | POST | `/blog/{id}/comments` | ✅ | ❌ |
| 4 | GET | `/products` | ✅ | ❌ |
| 5 | GET | `/products/{slug}` | ✅ | ❌ |
| 6 | POST | `/products/{id}/inquiries` | ✅ | ❌ |
| 7 | GET | `/product-categories` | ✅ | ❌ |
| 8 | GET | `/sponsors` | ✅ | ✅ |
| 9 | GET | `/sponsors/{slug}` | ✅ | ⚠️ Composable usaba ID antes |
| 10 | GET | `/support-campaigns` | ✅ | ❌ |
| 11 | GET | `/support-campaigns/{slug}` | ✅ | ✅ (`como-apoyar-home`) |
| 12 | GET | `/transparency-documents` | ✅ | ✅ |
| 13 | POST | `/contact` | ✅ | ❌ |
| 14 | GET | `/menus/{location}` | ✅ | ❌ |
| 15 | GET | `/settings` | ✅ | ❌ |
| 16 | GET | `/pages/{slug}` | ✅ | ❌ |

---

## 6. Estructura de Archivos Clave

### 6.1 Backend (`escaladapro-api/`)

```
app/
├── Filament/Resources/
│   ├── BlogPostResource.php          ✅ personalizado
│   ├── TransparencyDocumentResource.php  ✅ personalizado
│   ├── SupportMethodResource/        ✅ nuevo (20 mar 2026)
│   │   └── Pages/
│   │       ├── ListSupportMethods.php
│   │       └── EditSupportMethod.php
│   └── ...
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── BlogPostController.php
│   │   ├── SponsorController.php
│   │   ├── SupportCampaignController.php  ← eager-load activeMethods.media
│   │   ├── TransparencyDocumentController.php
│   │   └── ...
│   └── Resources/
│       ├── BlogPostResource.php
│       ├── SupportCampaignResource.php   ← campo image por método
│       └── ...
├── Models/
│   ├── SupportMethod.php    ← media_id fillable + relación media()
│   └── ...
database/
├── migrations/
│   ├── ..._add_media_id_to_support_methods_table.php  ✅ ejecutada
│   ├── ..._add_category_to_blog_posts_table.php       ✅ ejecutada
│   └── ..._make_year_nullable_in_transparency_documents_table.php ✅ ejecutada
└── seeders/
    ├── ClimbWorkExposureSeeder.php      ✅ ejecutado
    └── TransparencyDocumentSeeder.php  ✅ ejecutado (16 docs)
routes/
└── api.php
```

### 6.2 Frontend (`escaladapro/web/`)

```
composables/
└── useApi.ts          ← instancia $fetch + métodos tipados por módulo
types/
└── api.ts             ← interfaces TypeScript (BlogPost, Sponsor, etc.)
pages/
├── index.vue          ← home (patrocinadores dinámicos)
├── blog/
│   ├── index.vue      ← listado con hero + patrocinadores dinámicos
│   ├── [slug].vue     ← detalle dinámico ✅
│   └── all.vue        ← paginación local (pendiente conectar API)
├── como-apoyar/
│   ├── index.vue      ← fetch por slug `como-apoyar-home` ✅
│   ├── paypal.vue
│   ├── transferencia.vue
│   ├── gyms.vue
│   └── productos/
│       ├── index.vue
│       └── [slug].vue
├── patrocinador/
│   └── [slug].vue
└── transparencia.vue  ← acordeón con doc.file?.url ✅
```

---

## 7. Base de Datos — Estado Actual

| Tabla | Registros relevantes |
|---|---|
| `sponsors` | 8 activos, incluyendo `climb-work` (id=7) y `exposure` (id=8) |
| `blog_posts` | Varios posts con slugs, categorías y autores |
| `support_methods` | Métodos de campaña `como-apoyar-home` con `media_id` |
| `transparency_documents` | 16 documentos: asambleas / reportes / estados |
| `users` | admin@escalada.com (admin Filament) |

### Datos PENDIENTES de cargar

- ⚠️ **PDFs reales** en `transparency_documents` — todos los `file` están en `null`. Cargar desde Filament.
- ⚠️ **Imágenes reales** en `SupportMethod` — `media_id` disponible en Filament, subir imágenes.

---

## 8. Arquitectura — Dominio de Entidades

```
CONTENIDO
├── Page (slug, sections[hero|text|gallery|cards|timeline|cta|split])
├── BlogPost (slug, category[blog|eventos|noticias], featured_media, comments)
└── Product (slug, price, category, gallery, inquiries)

PATROCINIO & APOYO
├── Sponsor (slug, placements[home|blog|global])
└── SupportCampaign (slug, methods[paypal|transfer|gym|product])

INFRAESTRUCTURA
├── Media (polimórfica → mediables)
├── Menu + MenuItem (anidado)
├── SiteSetting (key/value con cache)
└── TransparencyDocument (type[asambleas|reportes|estados], file)
```

---

## 9. Contrato de Tipos TypeScript (resumen)

```typescript
// types/api.ts — interfaces principales

interface BlogPost {
  id: number; title: string; slug: string
  category: string                          // blog | eventos | noticias
  excerpt: string | null; body: string
  is_featured: boolean
  author: { name: string }
  featured_media: MediaItem | null
  published_at: string
  seo_title: string | null; seo_description: string | null
}

interface Sponsor {
  id: number; name: string; slug: string
  description: string | null; logo: MediaItem | null
}

interface SupportCampaign {
  id: number; slug: string; name: string
  methods: SupportMethod[]
}

interface SupportMethod {
  id: number; name: string; image: string | null   // ← campo image (20 mar 2026)
  description: string | null; instructions: string | null
}

interface TransparencyDocument {
  id: number; title: string; slug: string; type: string
  year: number | null                              // ← nullable (20 mar 2026)
  file: { id: number; url: string; file_name: string; mime_type: string; size: number } | null
  description: string | null
}

interface PaginatedResponse<T> {
  data: T[]
  meta: { current_page: number; last_page: number; per_page: number; total: number }
}
```

---

## 10. Composable `useApi.ts` — Métodos disponibles

```typescript
const api = useApi()

// Blog
api.blog.getAll({ page?: number, per_page?: number })
api.blog.getBySlug(slug)

// Pages (CMS)
api.pages.getBySlug(slug)

// Productos
api.products.getAll({ page?, category? })
api.products.getBySlug(slug)
api.productCategories.getAll()

// Sponsors
api.sponsors.getAll()
api.sponsors.getBySlug(slug)

// Support Campaigns
api.supportCampaigns.getAll()
api.supportCampaigns.getBySlug(slug)   // ← añadido 20 mar 2026

// Transparencia
api.transparencyDocuments.getAll({ type?, year? })

// Menús y Settings
api.menus.getByName('main' | 'footer')
api.settings.getAll()

// Contacto
api.contact.send({ name, email, subject, message })
```

---

## 11. Tareas Pendientes — Prioridad Alta → Baja

### 🔴 Alta Prioridad
- [ ] **Subir PDFs reales** a documentos de transparencia desde Filament (`/admin`)
- [ ] **Subir imágenes reales** en `SupportMethod` — asignar `media_id` desde Filament
- [ ] **Conectar `/blog/all`** a la API con paginación real (`api.blog.getAll({ page })`)
- [ ] **Conectar `/contacto`** al endpoint `POST /api/v1/contact`
- [ ] **Conectar `/como-apoyar/paypal`** y **`/transferencia`** a datos dinámicos

### 🟡 Media Prioridad
- [ ] **Crear endpoint `/api/v1/gyms`** (o incluirlo dentro de `support-campaigns`) para `/como-apoyar/gyms`
- [ ] **Conectar `/como-apoyar/productos`** y **`/productos/[slug]`** a la API de productos
- [ ] **Conectar `/patrocinador/[slug]`** — verificar si usa slug o ID y corregir
- [ ] **Verificar `/transparencia`** en navegador con datos reales (acordeón y tipos)
- [ ] **Verificar `/como-apoyar`** — imágenes dinámicas desde API

### 🟢 Baja Prioridad
- [ ] Conectar `/nosotros`, `/actividades`, `/historia` al endpoint `/api/v1/pages/{slug}`
- [ ] Implementar menú dinámico desde `GET /api/v1/menus/main`
- [ ] Implementar footer dinámico desde `GET /api/v1/menus/footer`
- [ ] Personalizar Filament Resources pendientes (Product, Sponsor, Menu, Contact)
- [ ] Agregar paginación a `/api/v1/transparency-documents` si crece el volumen
- [ ] Limpiar registros huérfanos en `transparency_documents` (tipos `annual/financial/legal`)
- [ ] Escribir Feature Tests para endpoints API (Pest)
- [ ] SEO dinámico en todas las páginas (`useHead` con datos de API)

---

## 12. Diseño y Estilos

### Paleta de colores
- **Amarillo Accent:** `#F8C52D` / `#F5C400`
- **Gris texto:** `#6A6867`
- **Negro:** títulos principales
- **Blanco:** fondos y texto sobre imágenes

### Patrón de línea decorativa
```html
<div class="flex items-center gap-4 mb-6">
  <div class="w-[80px] h-[3px] bg-[#F8C52D]"></div>
  <span class="text-[#6A6867]">TAG</span>
</div>
```

### Patrón de sección numerada (01, 02, 03...)
```html
<section class="relative min-h-screen">
  <div class="absolute top-0 left-0 text-[200px] font-bold text-gray-900/5">01</div>
  <div class="absolute top-[120px] left-[60px]">...</div>
</section>
```

---

## 13. Comandos de Verificación Rápida

```bash
# Verificar endpoints desde terminal
curl -sk "https://escaladapro-api.test/api/v1/transparency-documents?type=asambleas" | python3 -m json.tool
curl -sk "https://escaladapro-api.test/api/v1/support-campaigns/como-apoyar-home" | python3 -m json.tool
curl -sk "https://escaladapro-api.test/api/v1/sponsors" | python3 -m json.tool
curl -sk "https://escaladapro-api.test/api/v1/blog?per_page=5" | python3 -m json.tool

# Estado de migraciones
cd /Volumes/REDHARDISK/PROYECTOS/escaladapro-api
php artisan migrate:status

# Limpiar caché
php artisan config:clear && php artisan cache:clear

# Regenerar tipos TS si cambia algún Resource
# (manual — comparar Resource PHP con types/api.ts)
```

---

## 14. Bugs Corregidos — Historial

| Fecha | Bug | Fix |
|---|---|---|
| 20 mar 2026 | `transparencia.vue`: `filter is not a function` | `useApi.ts` extrae `r.data` correctamente |
| 20 mar 2026 | Acordeón transparencia: `doc.media?.url` roto | Cambiado a `doc.file?.url` |
| 20 mar 2026 | Slugs `climb-work` y `exposure` no existían en BD | `ClimbWorkExposureSeeder` creado y ejecutado |
| 20 mar 2026 | Patrocinadores hardcodeados en `blog/index.vue` | Fetch dinámico de los 2 primeros sponsors activos |
| 14 mar 2026 | 5 controllers y 6 resources con bugs SQL | Fase 0 completada: todos corregidos |
| 14 mar 2026 | `transparencia_documents` sin columna `slug` | Migración `add_slug_to_transparency_documents_table` ejecutada |

---

## 15. Archivos de Referencia

| Documento | Ruta | Contenido |
|---|---|---|
| `MAPA_CONTEXTUAL.md` | `escaladapro-api/` | Arquitectura completa, mapa de endpoints, estado frontend |
| `PLAN_INTEGRACION.md` | `escaladapro-api/` | Fases de desarrollo y decisiones de implementación |
| `API_IMPLEMENTATION.md` | `escaladapro-api/` | Detalle de cada controlador y API Resource |
| `API_CONTRACTS.md` | `escaladapro-api/` | Contratos de request/response de cada endpoint |
| `BLOG_IMPLEMENTATION.md` | `escaladapro-api/` | Sistema de blog: categorías, slugs, Filament y Nuxt |
| `SESION_20_MARZO_2026.md` | `escaladapro-api/` | Log detallado de la sesión del 20 de marzo |
| `AUDITORIA.md` | `escaladapro-api/` | Auditoría inicial (feb 2026): qué estaba completo/pendiente |
| `PROGRESS.md` | `escaladapro/web/` | Progreso de UI del frontend (feb 2026) |

---

> **Convención de trabajo:** Cuando termines una sesión, actualiza la sección 4 (Estado Actual), añade los bugs corregidos en la sección 14, y anota las tareas completadas en la sección 11. Así este documento siempre refleja la realidad del proyecto.
