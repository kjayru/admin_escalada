# Mapa Contextual — EscaladaPro

> Generado: 14 de marzo de 2026 — Revisado: 14 de marzo de 2026  
> Proyectos: `escaladapro-api` (Laravel 12) · `escaladapro/web` (Nuxt 4)  
> ⚠️ `escaladapro-web` es un proyecto incorrecto/deprecado — el frontend real es `escaladapro/web`

---

## 1. Visión General del Sistema

```
┌─────────────────────────────────────────────────────────────────────┐
│                        EscaladaPRO Platform                         │
│         Plataforma de contenido para la comunidad de escalada        │
└─────────────────────────────────────────────────────────────────────┘
         │                                          │
         ▼                                          ▼
┌─────────────────┐                      ┌──────────────────────┐
│  escaladapro-   │  REST API (JSON)      │  escaladapro-web     │
│  api            │ ◄───────────────────► │  (Nuxt 4 / Vue 3)   │
│  (Laravel 12)   │  /api/v1/*           │                      │
│  PHP 8.2        │                      │  SPA / SSR           │
│  Sanctum + CORS │                      │  Frontend público     │
└────────┬────────┘                      └──────────────────────┘
         │
         ▼
┌─────────────────┐
│  Panel Admin     │
│  (Filament 3)    │
│  /admin          │
└─────────────────┘
```

---

## 2. Stack Tecnológico

| Capa | Proyecto | Tecnología | Versión |
|------|----------|-----------|---------|
| **Backend API** | escaladapro-api | Laravel | 12.x |
| | | PHP | 8.2+ |
| | | Laravel Sanctum | 4.x |
| | | Filament (Admin) | 3.x |
| | | Pest (Tests) | 4.x |
| **Frontend** | escaladapro/web | Nuxt | 4.3+ |
| | | Vue | 3.5+ |
| | | Vue Router | 4.x |
| | | Tailwind CSS | 6.x (via @nuxtjs/tailwindcss) |
| | | Swiper | 12.x |
| | | TypeScript | nativo |
| **Base de datos** | escaladapro-api | MySQL / SQLite | — |
| **Auth (API)** | escaladapro-api | Sanctum SPA + Bearer token | — |

---

## 3. Mapa de Dominio — Entidades del Negocio

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                            DOMINIO: Contenido                                │
│                                                                              │
│  ┌─────────────┐    ┌────────────────┐    ┌──────────────────┐              │
│  │    Page     │    │   BlogPost     │    │     Product      │              │
│  │─────────────│    │────────────────│    │──────────────────│              │
│  │ slug        │    │ slug           │    │ slug             │              │
│  │ title       │    │ title          │    │ name             │              │
│  │ template    │    │ excerpt        │    │ summary          │              │
│  │ status      │    │ body           │    │ description      │              │
│  │ published_at│    │ status         │    │ price / currency │              │
│  │             │    │ published_at   │    │ status           │              │
│  │ ┌─────────┐ │    │                │    │                  │              │
│  │ │Sections │ │    │ ┌──────────┐   │    │ ┌────────────┐  │              │
│  │ │(7 tipos)│ │    │ │Comments  │   │    │ │ Inquiries  │  │              │
│  │ │hero     │ │    │ │pending/  │   │    │ │(cotizacion)│  │              │
│  │ │text     │ │    │ │approved/ │   │    │ └────────────┘  │              │
│  │ │gallery  │ │    │ │rejected  │   │    │                  │              │
│  │ │cards    │ │    │ └──────────┘   │    │ ┌────────────┐  │              │
│  │ │timeline │ │    └────────────────┘    │ │ Category   │  │              │
│  │ │cta      │ │                          │ └────────────┘  │              │
│  │ │split    │ │                          └──────────────────┘              │
│  │ └─────────┘ │                                                             │
│  └─────────────┘                                                             │
└──────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────┐
│                          DOMINIO: Patrocinio & Apoyo                         │
│                                                                              │
│  ┌──────────────────────┐          ┌──────────────────────────┐             │
│  │      Sponsor         │          │     SupportCampaign      │             │
│  │──────────────────────│          │──────────────────────────│             │
│  │ name / slug          │          │ name / slug              │             │
│  │ description          │          │ description              │             │
│  │ website_url          │          │ status (active)          │             │
│  │ status               │          │ start_at / end_at        │             │
│  │                      │          │                          │             │
│  │ ┌──────────────────┐ │          │ ┌──────────────────────┐ │             │
│  │ │SponsorPlacement  │ │          │ │   SupportMethod      │ │             │
│  │ │(home/blog/global)│ │          │ │  paypal/transfer/    │ │             │
│  │ │start_at/end_at   │ │          │ │  gym/product         │ │             │
│  │ └──────────────────┘ │          │ └──────────────────────┘ │             │
│  └──────────────────────┘          └──────────────────────────┘             │
└──────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────┐
│                          DOMINIO: Infraestructura                            │
│                                                                              │
│  ┌──────────────┐   ┌──────────────┐   ┌──────────────┐   ┌─────────────┐  │
│  │    Media     │   │     Menu     │   │ SiteSetting  │   │ Transparency│  │
│  │──────────────│   │──────────────│   │──────────────│   │  Document   │  │
│  │ path / url   │   │ name (main   │   │ key / value  │   │─────────────│  │
│  │ mime_type    │   │ / footer)    │   │ group        │   │ title/year  │  │
│  │ alt / title  │   │              │   │ cache ∞      │   │ type/status │  │
│  │ polimórfica  │   │ ┌──────────┐ │   └──────────────┘   └─────────────┘  │
│  │ (mediables)  │   │ │MenuItem  │ │                                        │
│  └──────────────┘   │ │(anidado) │ │                                        │
│                     │ └──────────┘ │                                        │
│                     └──────────────┘                                        │
└──────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Mapa de Endpoints API

### 4.1 Rutas Públicas — `/api/v1/`

| # | Método | Endpoint | Controlador | Frontend consume |
|---|--------|----------|-------------|-----------------|
| 1 | GET | `/v1/pages` | `PageController@index` | ✗ no implementado |
| 2 | GET | `/v1/pages/{slug}` | `PageController@show` | ✗ no implementado |
| 3 | GET | `/v1/blog` | `BlogPostController@index` | ⚠️ composable usa `/articles` |
| 4 | GET | `/v1/blog/{slug}` | `BlogPostController@show` | ⚠️ composable usa `/articles/{slug}` |
| 5 | POST | `/v1/blog/{id}/comments` | `BlogPostController@storeComment` | ✗ no implementado |
| 6 | GET | `/v1/products` | `ProductController@index` | ✓ composable OK |
| 7 | GET | `/v1/products/{slug}` | `ProductController@show` | ✓ composable OK |
| 8 | POST | `/v1/products/{id}/inquiries` | `ProductController@storeInquiry` | ✗ no implementado |
| 9 | GET | `/v1/product-categories` | `ProductCategoryController@index` | ✗ no implementado |
| 10 | GET | `/v1/product-categories/{slug}` | `ProductCategoryController@show` | ✗ no implementado |
| 11 | GET | `/v1/sponsors` | `SponsorController@index` | ✓ composable OK |
| 12 | GET | `/v1/sponsors/{slug}` | `SponsorController@show` | ⚠️ composable usa ID |
| 13 | GET | `/v1/support-campaigns` | `SupportCampaignController@index` | ✗ no implementado |
| 14 | GET | `/v1/support-campaigns/{slug}` | `SupportCampaignController@show` | ✗ no implementado |
| 15 | GET | `/v1/transparency-documents` | `TransparencyDocumentController@index` | ✗ no implementado |
| 16 | GET | `/v1/transparency-documents/{slug}` | `TransparencyDocumentController@show` | ✗ no implementado |
| 17 | POST | `/v1/contact` | `ContactController@store` | ✗ no implementado |
| 18 | GET | `/v1/menus/{location}` | `MenuController@show` | ✗ no implementado |
| 19 | GET | `/v1/settings` | `SettingController@index` | ✗ no implementado |
| 20 | GET | `/v1/settings/{key}` | `SettingController@show` | ✗ no implementado |

### 4.2 Rutas de Autenticación — `/`

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/login` | Login (devuelve cookie de sesión) |
| POST | `/register` | Registro de usuario |
| POST | `/forgot-password` | Solicitar reset |
| POST | `/reset-password` | Confirmar reset |
| GET | `/verify-email/{id}/{hash}` | Verificar email |
| POST | `/logout` | Cerrar sesión |

### 4.3 Rutas Autenticadas

| Método | Endpoint | Descripción | Middleware |
|--------|----------|-------------|-----------|
| GET | `/api/user` | Perfil del usuario autenticado | `auth:sanctum` |

---

## 5. Estado Actual del Frontend — `escaladapro/web`

### 5.1 Composable `useApi.ts`

```typescript
// composables/useApi.ts
export const useApi = () => {
  const config = useRuntimeConfig()
  return $fetch.create({ baseURL: config.public.apiBase })
}
// NUXT_PUBLIC_API_BASE=https://api-escaladapro.test
```

Es una instancia de `$fetch` sin headers de auth ni interceptors.  
**Estado: Configurado pero NO usado en ninguna página.**

### 5.2 Rutas del Frontend y estado de integración

| Ruta | Archivo | UI Completa | API integrada | Endpoint que necesita |
|------|---------|-------------|--------------|----------------------|
| `/` | `pages/index.vue` | ✅ | ❌ hardcodeado | `/v1/pages/home`, `/v1/sponsors`, `/v1/blog` |
| `/nosotros` | `pages/nosotros.vue` | ✅ | ❌ estático | `/v1/pages/nosotros` |
| `/actividades` | `pages/actividades.vue` | ✅ | ❌ hardcodeado | `/v1/support-campaigns` |
| `/historia` | `pages/historia.vue` | ✅ | ❌ estático | `/v1/pages/historia` |
| `/blog` | `pages/blog.vue` + `blog/index.vue` ⚠️ duplicado | ✅ | ❌ hardcodeado | `/v1/blog` |
| `/blog/all` | `pages/blog/all.vue` | ✅ paginación local | ❌ hardcodeado | `/v1/blog?page=N` |
| `/blog/article` | `pages/blog/article.vue` | ✅ | ❌ hardcodeado | Cambiar a `/blog/[slug].vue` + `/v1/blog/{slug}` |
| `/como-apoyar` | `pages/como-apoyar.vue` + `index.vue` ⚠️ duplicado | ✅ | ❌ estático | `/v1/support-campaigns` |
| `/como-apoyar/paypal` | `pages/como-apoyar/paypal.vue` | ✅ | ❌ handler vacío | `/v1/support-campaigns` o pago externo |
| `/como-apoyar/transferencia` | `pages/como-apoyar/transferencia.vue` | ✅ | ❌ datos bancarios fijos | `/v1/support-campaigns` + settings |
| `/como-apoyar/productos` | `pages/como-apoyar/productos.vue` | ✅ | ❌ hardcodeado | `/v1/products` |
| `/como-apoyar/gyms` | `pages/como-apoyar/gyms.vue` | ✅ | ❌ hardcodeado | ⚠️ endpoint no existe en API |
| `/contacto` | `pages/contacto.vue` | ✅ | ❌ solo console.log | `POST /v1/contact` |
| `/transparencia` | `pages/transparencia.vue` | ✅ | ❌ hardcodeado | `/v1/transparency-documents` |
| `/patrocinio` | `pages/patrocinio.vue` | ✅ | ❌ hardcodeado | `/v1/sponsors/{slug}` |
| `/patrocinio-2` | `pages/patrocinio-2.vue` | ✅ | ❌ hardcodeado | `/v1/sponsors/{slug}` o `/v1/products/{slug}` |

### 5.3 Componentes existentes

| Componente | Existe | Usado en páginas |
|------------|--------|-----------------|
| `base/Button.vue` | ✅ polimórfico, 4 variantes | ❌ ninguna página lo usa |
| `base/Container.vue` | ✅ | ❌ ninguna página lo usa |
| `base/Heading.vue` | ✅ h1–h6 | ❌ ninguna página lo usa |
| `layouts/Header.vue` | ✅ con menú mobile | ✅ en layout |
| `layouts/Footer.vue` | ✅ con fondo imagen | ✅ en layout |
| `sections/` | ⚠️ directorio vacío | — |

### 5.4 CSS y Design System

- **Tailwind CSS** configurado con `extend: { colors: { accent: '#F5C400' }, fontFamily: { sans: [...] } }`
- **Tokens CSS** en `assets/css/tokens.css`: `--c-primary: #f5c400`, `--container: 1280px`
- ⚠️ Inconsistencia de color amarillo: `#F8C52D` (inline en páginas) vs `#F5C400` (config) — misma variante, dos valores
- Todas las páginas usan clases Tailwind **inline**, ignorando componentes base ya creados

### 5.5 Variables de entorno

| Variable | Valor actual |
|----------|-------------|
| `NUXT_PUBLIC_API_BASE` | `https://api-escaladapro.test` (Herd/Valet local) |

---

## 6. Mapa de Autenticación

```
Frontend (Nuxt)                          Backend (Laravel Sanctum)
─────────────────────────────────────────────────────────────────
                    ┌─ SPA Mode (stateful) ─┐
                    │                       │
POST /login ───────►│  AuthenticatedSession │──► Session cookie
                    │  Controller@store     │
GET  /api/user ────►│  (auth:sanctum)       │──► User JSON
                    │                       │
POST /logout ──────►│  @destroy             │──► Clear session
                    └───────────────────────┘

                    ┌─ Token Mode (stateless) ─┐  (NO implementado aún)
                    │                          │
POST /login ───────►│  retorna token           │──► Bearer token
GET  /api/* ───────►│  Authorization: Bearer   │──► Protected data
                    └──────────────────────────┘
```

> **Estado:** La autenticación SPA con Sanctum está configurada en el backend pero **no está implementada en el frontend**.

---

## 7. Mapa de Configuración

### 7.1 Variables de Entorno Necesarias

| Variable | Proyecto | Valor ejemplo | Descripción |
|----------|----------|--------------|-------------|
| `FRONTEND_URL` | API | `http://localhost:3000` | CORS origin permitido |
| `APP_URL` | API | `http://localhost:8000` | URL base de la API |
| `NUXT_PUBLIC_API_BASE` | Web | `http://localhost:8000` | URL de la API desde el frontend |
| `DB_*` | API | — | Credenciales de BD |
| `MAIL_*` | API | — | Servidor de correo |

### 7.2 CORS — Configuración Actual

```
paths:              ['*']        ← Todas las rutas
allowed_methods:    ['*']        ← Todos los métodos HTTP
allowed_origins:    [FRONTEND_URL] ← Solo 1 origen
allowed_headers:    ['*']
supports_credentials: true       ← Necesario para Sanctum SPA
```

---

## 8. Panel de Administración (Filament)

```
/admin  →  Filament 3
├── Recursos implementados (parcial ~70%)
│   ├── BlogPostResource
│   ├── PageResource
│   ├── ProductResource
│   ├── SponsorResource
│   └── [otros pendientes]
├── Login propio
└── Guard: web
```

---

## 9. Bugs Críticos en la API — ✅ TODOS CORREGIDOS (Fase 0)

| # | Controlador/Resource | Bug | Fix Aplicado |
|---|---------------------|-----|-------------|
| 1 | `SponsorController` | `is_active`, `display_order`, `level` inexistentes | `->active()` scope; filtros eliminados |
| 2 | `SponsorResource` | campos `level`, `display_order`, `alt_text` inexistentes | alineado al modelo real |
| 3 | `MenuController` | `where('location',...)` → columna no existe; `is_active`, `orderBy('order')` no existen | `where('name',...)`, `orderBy('sort_order')`, `activeItems` |
| 4 | `MenuResource`/`MenuItemResource` | `location`, `type`, `order`, `title` mal nombrados | alineados al modelo real |
| 5 | `TransparencyDocumentController` | `is_published`, `with(['file'])`, slug faltante en DB | `->published()`, `with(['media'])`, migración slug aplicada |
| 6 | `TransparencyDocumentResource` | `file`, `filename` incorrectos | `media`, `file_name` |
| 7 | `ProductCategoryController` | `where('is_active',true)` → columna no existe | filtro eliminado |
| 8 | `SupportCampaignController` | `is_active`, `supportMethods`, `display_order` incorrectos | `->active()`, `activeMethods` |
| 9 | `SupportCampaignResource` | campos fantasma | alineado al modelo real |
| 10 | `ProductResource` | `condition`, `location`, `alt_text` incorrectos | `summary`, `alt`, fixed |

> **Migración adicional aplicada:** `add_slug_to_transparency_documents_table` — agrega `slug nullable unique` a la tabla `transparency_documents`.

---

## 10. ⚠️ Inconsistencias Frontend ↔ Backend — Estado Actual

| # | Página | Situación | Acción Requerida en Fase 2 |
|---|--------|-----------|--------------------------|
| 1 | Todas (16 páginas) | Datos hardcodeados | Conectar al composable `useApi.ts` |
| 2 | `/blog` | Ruta duplicada: `blog.vue` + `blog/index.vue` | Eliminar `blog.vue` |
| 3 | `/como-apoyar` | Ruta duplicada: `como-apoyar.vue` + `como-apoyar/index.vue` | Eliminar `como-apoyar.vue` |
| 4 | `/blog/article` | Ruta fija, no dinámica | Renombrar a `blog/[slug].vue` + API call |
| 5 | `/como-apoyar/gyms` | Endpoint `/v1/gyms` **no existe** en la API | Endpoint pendiente de crear en API |
| 6 | `composables/useApi.ts` | Solo crea instancia `$fetch`, sin métodos tipados | Extender con módulos por recurso |
| 7 | `types/` | Directorio vacío, sin interfaces TypeScript | Crear `types/api.ts` con 14 interfaces |
| 8 | Header.vue | Menú hardcodeado (8 links fijos) | Conectar a `/v1/menus?name=main` |

---

## 11. Estado de Madurez por Módulo

| Módulo | DB/Modelo | API Endpoint | Admin Filament | Frontend | Tests |
|--------|-----------|--------------|----------------|----------|-------|
| Pages | ✅ | ✅ | ⚠️ parcial | ⚠️ UI lista, sin API | ✗ |
| PageSections | ✅ | ✅ (dentro pages) | ⚠️ parcial | ⚠️ UI lista, sin API | ✗ |
| Blog | ✅ | ✅ | ⚠️ parcial | ⚠️ UI lista, sin API | ✗ |
| BlogComments | ✅ | ✅ | ✗ | ✗ | ✗ |
| Products | ✅ | ✅ | ⚠️ parcial | ⚠️ UI lista, sin API | ✗ |
| ProductCategories | ✅ | ✅ | ✗ | ⚠️ UI lista, sin API | ✗ |
| ProductInquiries | ✅ | ✅ | ✗ | ✗ | ✗ |
| Sponsors | ✅ | ✅ CorregidO | ⚠️ parcial | ⚠️ UI lista, sin API | ✗ |
| SponsorPlacements | ✅ | ✅ | ✗ | ✗ | ✗ |
| SupportCampaigns | ✅ | ✅ Corregido | ✗ | ⚠️ UI lista, sin API | ✗ |
| SupportMethods | ✅ | ✅ | ✗ | ✗ | ✗ |
| TransparencyDocs | ✅ | ✅ Corregido | ✗ | ⚠️ UI lista, sin API | ✗ |
| Contact | ✅ | ✅ | ✗ | ⚠️ UI lista, sin API | ✗ |
| Menus | ✅ | ✅ Corregido | ✗ | ⚠️ Header hardcodeado | ✗ |
| Settings | ✅ | ✅ | ✗ | ✗ | ✗ |
| Media | ✅ | (embebido) | ⚠️ parcial | ✗ | ✗ |
| Auth (Users) | ✅ | ✅ Breeze | — | ✗ | ✅ Breeze |
| Gyms | ✗ | ✗ No existe | ✗ | ⚠️ Página existe | ✗ |

**Leyenda:** ✅ Completo · ⚠️ Parcial/Pendiente · ✗ No implementado
