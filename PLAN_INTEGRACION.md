# Plan de Integración — EscaladaPro API + Web

> Generado: 14 de marzo de 2026 · Revisado: 14 de marzo de 2026  
> Base: `escaladapro-api` (Laravel 12) · `escaladapro/web` (Nuxt 4)  
> ⚠️ `escaladapro-web` es un proyecto DEPRECADO — ignorar



---

---

## Resumen Ejecutivo

El backend tiene **20 endpoints funcionales** — todos los bugs SQL han sido corregidos (Fase 0 completada). El frontend real (`escaladapro/web`) tiene **16 páginas con UI completamente implementada**, diseño en amarillo `#F5C400`, Tailwind y Swiper, pero **cero integración con la API**: todos los datos son hardcodeados.

La tarea pendiente es conectar ese frontend existente a la API, no construir páginas desde cero.

---

## Estado de Fases

| Fase | Descripción | Estado |
|------|-------------|--------|
| 0 | Corrección de bugs críticos en la API | ✅ **COMPLETADA** |
| 1 | Fundamentos del frontend (composable + tipos) | 🔄 **En progreso** (pendiente en proyecto real) |
| 2 | Conectar páginas públicas a la API | ⏳ Pendiente |
| 3 | Endpoint gyms + integración de Settings/Menus | ⏳ Pendiente |
| 4 | Tests, seeders y SEO | ⏳ Pendiente |
| 5 | Producción | ⏳ Pendiente |

---

## ✅ Fase 0 — Corrección de Bugs Críticos en la API — COMPLETADA

Todos los bugs fueron corregidos. Ver detalle completo en `MAPA_CONTEXTUAL.md` sección 9.

**Resumen de cambios aplicados:**
- 5 controllers corregidos: `SponsorController`, `MenuController`, `TransparencyDocumentController`, `ProductCategoryController`, `SupportCampaignController`
- 6 resources corregidos: `SponsorResource`, `MenuResource`, `MenuItemResource`, `TransparencyDocumentResource`, `SupportCampaignResource`, `ProductResource`
- Migración aplicada: `add_slug_to_transparency_documents_table`

---

## 🔄 Fase 1 — Fundamentos del Frontend
> **Proyecto:** `/Users/wiletinoco/VUE/escaladapro/web`  
> **Prioridad: ALTA** — Requisito para todas las demás fases

### 1.1 — Crear `types/api.ts`

Crear `/Users/wiletinoco/VUE/escaladapro/web/types/api.ts` con las interfaces TypeScript derivadas de los Laravel Resources:

```typescript
// types/api.ts

export interface BlogPost {
  id: number
  title: string
  slug: string
  excerpt: string | null
  body: string
  published_at: string
  author: { name: string }
  featured_media: MediaItem | null
  seo_title: string | null
  seo_description: string | null
}

export interface MediaItem {
  id: number
  url: string
  file_name: string
  alt: string | null
  mime_type: string
  size: number
}

export interface Page {
  id: number
  title: string
  slug: string
  sections: PageSection[]
}

export interface PageSection {
  id: number
  type: string
  content: Record<string, unknown>
  sort_order: number
}

export interface Product {
  id: number
  title: string
  slug: string
  summary: string | null
  description: string | null
  price: number | null
  category: ProductCategory | null
  media: MediaItem[]
  featured_media: MediaItem | null
}

export interface ProductCategory {
  id: number
  name: string
  slug: string
}

export interface Sponsor {
  id: number
  name: string
  slug: string
  description: string | null
  url: string | null
  logo: MediaItem | null
  placements: SponsorPlacement[]
}

export interface SponsorPlacement {
  id: number
  name: string
  position: string
  sort_order: number
}

export interface Menu {
  id: number
  name: string
  items: MenuItem[]
}

export interface MenuItem {
  id: number
  label: string
  url: string
  sort_order: number
  children: MenuItem[]
}

export interface Setting {
  key: string
  value: string
  label: string
}

export interface SupportCampaign {
  id: number
  name: string
  description: string | null
  status: string
  start_at: string | null
  end_at: string | null
  methods: SupportMethod[]
}

export interface SupportMethod {
  id: number
  name: string
  description: string | null
  instructions: string | null
}

export interface TransparencyDocument {
  id: number
  title: string
  slug: string
  type: string
  year: number | null
  published_at: string | null
  media: MediaItem | null
}

export interface ContactForm {
  name: string
  email: string
  subject: string
  message: string
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}
```

### 1.2 — Extender `composables/useApi.ts` con métodos tipados

El composable actual solo crea la instancia `$fetch`. Debe exportar además métodos por módulo:

```typescript
// composables/useApi.ts
import type {
  BlogPost, Page, Product, ProductCategory, Sponsor,
  Menu, Setting, SupportCampaign, TransparencyDocument,
  ContactForm, PaginatedResponse
} from '~/types/api'

export const useApi = () => {
  const config = useRuntimeConfig()
  const apiFetch = $fetch.create({ baseURL: config.public.apiBase as string })

  return {
    // instancia cruda para casos especiales
    $fetch: apiFetch,

    blog: {
      getAll: (params?: { page?: number }) =>
        apiFetch<PaginatedResponse<BlogPost>>('/api/v1/blog', { params }),
      getBySlug: (slug: string) =>
        apiFetch<BlogPost>(`/api/v1/blog/${slug}`),
    },
    pages: {
      getBySlug: (slug: string) =>
        apiFetch<Page>(`/api/v1/pages/${slug}`),
    },
    products: {
      getAll: (params?: { page?: number; category?: string }) =>
        apiFetch<PaginatedResponse<Product>>('/api/v1/products', { params }),
      getBySlug: (slug: string) =>
        apiFetch<Product>(`/api/v1/products/${slug}`),
    },
    productCategories: {
      getAll: () =>
        apiFetch<ProductCategory[]>('/api/v1/product-categories'),
    },
    sponsors: {
      getAll: () =>
        apiFetch<Sponsor[]>('/api/v1/sponsors'),
      getBySlug: (slug: string) =>
        apiFetch<Sponsor>(`/api/v1/sponsors/${slug}`),
    },
    menus: {
      getByName: (name: 'main' | 'footer') =>
        apiFetch<Menu>(`/api/v1/menus/${name}`),
    },
    settings: {
      getAll: () =>
        apiFetch<Setting[]>('/api/v1/settings'),
    },
    supportCampaigns: {
      getAll: () =>
        apiFetch<SupportCampaign[]>('/api/v1/support-campaigns'),
    },
    transparencyDocuments: {
      getAll: (params?: { type?: string; year?: number }) =>
        apiFetch<TransparencyDocument[]>('/api/v1/transparency-documents', { params }),
    },
    contact: {
      send: (data: ContactForm) =>
        apiFetch<{ message: string }>('/api/v1/contact', { method: 'POST', body: data }),
    },
  }
}
```

> **Decisión:** No se agrega Pinia. El real frontend (`escaladapro/web`) no lo tiene instalado. Usar `useState()` de Nuxt para estado global ligero si es necesario (ej. menú cargado una vez).

---

## Fase 2 — Conectar páginas públicas a la API
> **Proyecto:** `/Users/wiletinoco/VUE/escaladapro/web`  
> **Contexto:** Las 16 páginas tienen UI completa. Solo hay que reemplazar los arrays hardcodeados.

### 2.1 — Resolver conflictos de rutas (BLOQUEANTE)

Existen rutas duplicadas que Nuxt resuelve ambiguamente:

| Conflicto | Acción |
|-----------|--------|
| `pages/blog.vue` + `pages/blog/index.vue` | Eliminar `pages/blog.vue` — conservar `blog/index.vue` |
| `pages/como-apoyar.vue` + `pages/como-apoyar/index.vue` | Eliminar `pages/como-apoyar.vue` — conservar `como-apoyar/index.vue` |

### 2.2 — Convertir artículo de blog a ruta dinámica

`pages/blog/article.vue` está hardcodeado con un artículo fijo. Debe convertirse en:

```
pages/blog/[slug].vue   ← ruta dinámica /blog/:slug
```

```typescript
// pages/blog/[slug].vue
const route = useRoute()
const api = useApi()
const { data: post } = await useAsyncData(`blog-${route.params.slug}`, () =>
  api.blog.getBySlug(route.params.slug as string)
)
useHead({
  title: post.value?.seo_title || post.value?.title,
  meta: [{ name: 'description', content: post.value?.seo_description }]
})
```

La antigua `article.vue` puede eliminarse o conservarse para legacy.

### 2.3 — Orden de integración de páginas

**Prioridad 1 — Contacto** (más simple, efecto inmediato):
```
pages/contacto.vue → POST /api/v1/contact
```
Reemplazar el `console.log()` con:
```typescript
const api = useApi()
const loading = ref(false)
const success = ref(false)
async function submitForm() {
  loading.value = true
  try {
    await api.contact.send({ name, email, subject, message })
    success.value = true
  } finally {
    loading.value = false
  }
}
```

**Prioridad 2 — Documentos de Transparencia**:
```
pages/transparencia.vue → GET /api/v1/transparency-documents
```
Reemplazar enlaces `href="#"` con URLs del media de cada documento.
Agrupar por `type` y `year` con computed:
```typescript
const { data: docs } = await useAsyncData('transparency', () =>
  api.transparencyDocuments.getAll()
)
const docsByType = computed(() => groupBy(docs.value, 'type'))
```

**Prioridad 3 — Blog**:
```
pages/blog/index.vue → GET /api/v1/blog (paginado)
pages/blog/all.vue   → GET /api/v1/blog?page=N
pages/blog/[slug].vue → GET /api/v1/blog/{slug}
```

**Prioridad 4 — Productos**:
```
pages/como-apoyar/productos.vue → GET /api/v1/products
```
Filtro por categoría usando `/v1/product-categories`.

**Prioridad 5 — Campañas de Apoyo**:
```
pages/actividades.vue         → GET /api/v1/support-campaigns
pages/como-apoyar/index.vue   → GET /api/v1/support-campaigns
```

**Prioridad 6 — Patrocinadores**:
```
pages/patrocinio.vue  → GET /api/v1/sponsors
pages/patrocinio-2.vue → GET /api/v1/sponsors/{slug} o /v1/products
```

**Prioridad 7 — Menú dinámico en Header**:
```
components/layouts/Header.vue → GET /api/v1/menus/main
```
Reemplazar los 8 links hardcodeados. Usar `useState` para cachear:
```typescript
const menu = useState('main-menu', () => null)
if (!menu.value) {
  const api = useApi()
  menu.value = await api.menus.getByName('main')
}
```

### 2.4 — Patrón recomendado para páginas con datos

```typescript
<script setup lang="ts">
const api = useApi()

// SSR: datos disponibles al renderizar
const { data, pending, error } = await useAsyncData('key', () => api.resource.getAll())
</script>

<template>
  <div v-if="pending"><!-- loading --></div>
  <div v-else-if="error"><!-- error --></div>
  <div v-else><!-- contenido con data --></div>
</template>
```

---

## Fase 3 — Endpoint gyms + Settings
> **Prioridad: MEDIA**

### 3.1 — Crear endpoint de Gyms (escaladapro-api)

La página `pages/como-apoyar/gyms.vue` lista gimnasios. No existe modelo ni endpoint.

**Opción A — Crear modelo completo:**
```bash
php artisan make:model Gym -mrc
```
Campos: `name`, `address`, `city`, `state`, `country`, `phone`, `website`, `logo`, `status`.

**Opción B — Usar tabla `settings` con clave `gyms` (JSON):**
Más rápido pero menos flexible. Solo viable si la lista de gyms es pequeña y manual.

**Recomendación:** Opción A para escalar correctamente.

### 3.2 — Conectar Settings al frontend

`GET /api/v1/settings` devuelve pares `key → value` del sitio.

Usar en `app.vue` o layout para cargar una vez:
```typescript
const settings = useState('settings', () => ({}))
if (!Object.keys(settings.value).length) {
  const api = useApi()
  const list = await api.settings.getAll()
  settings.value = Object.fromEntries(list.map(s => [s.key, s.value]))
}
```

---

## Fase 4 — Tests, Seeders y SEO
> **Prioridad: MEDIA**

### 4.1 — Tests de la API (Backend)

Crear con Pest en `tests/Feature/Api/V1/`:

```
BlogPostTest.php      → index, show
ProductTest.php       → index, show, inquiry (POST)
SponsorTest.php       → index, show
TransparencyTest.php  → index with type/year filter
ContactTest.php       → POST validation + success
MenuTest.php          → show main/footer
```

### 4.2 — Seeders de datos de prueba

| Seeder | Estado Actual | Datos necesarios |
|--------|--------------|-----------------|
| `AdminUserSeeder` | ✅ | — |
| `SiteSettingsSeeder` | ✗ | site_name, contact_email, social_links |
| `MenuSeeder` | ✗ | Menú main (8 items) y footer |
| `PageSeeder` | ✗ | home, nosotros, historia |
| `DemoDataSeeder` | ✗ | Posts, productos, sponsors, campañas |

### 4.3 — SEO para páginas dinámicas

En toda página que recibe datos del API:
```typescript
useHead({
  title: `${post.seo_title || post.title} — EscaladaPro`,
  meta: [
    { name: 'description', content: post.seo_description || post.excerpt },
    { property: 'og:title', content: post.seo_title || post.title },
    { property: 'og:image', content: post.featured_media?.url },
    { property: 'og:type', content: 'article' },
  ]
})
```

---

## Fase 5 — Producción
> **Prioridad: BAJA — solo tras completar Fases 1-4**

### 5.1 — Variables de entorno de producción

| Variable | Proyecto | Valor producción |
|----------|----------|-----------------|
| `APP_URL` | API | `https://api.escaladapro.com` |
| `FRONTEND_URL` | API | `https://www.escaladapro.com` |
| `NUXT_PUBLIC_API_BASE` | Web | `https://api.escaladapro.com` |
| `SESSION_SECURE_COOKIE` | API | `true` |
| `APP_DEBUG` | API | `false` |

### 5.2 — Rendimiento

- Agregar índices en: `slug`, `status`, `published_at` en tablas de consulta frecuente
- Cachear respuestas de `/v1/settings` y `/v1/menus` (ya preparado en `SiteSetting`)
- Activar SSR en Nuxt para páginas de blog (SEO crítico)

### 5.3 — CORS en producción

```php
// config/cors.php — allowed_origins
'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
```

---

## Checklist

### ✅ Fase 0 — Completada
- [x] Corregir `SponsorController` + `SponsorResource`
- [x] Corregir `MenuController` + `MenuResource` + `MenuItemResource`
- [x] Corregir `TransparencyDocumentController` + `TransparencyDocumentResource`
- [x] Corregir `ProductCategoryController`
- [x] Corregir `SupportCampaignController` + `SupportCampaignResource`
- [x] Corregir `ProductResource`
- [x] Migración `slug` en `transparency_documents` — aplicada

### 🔄 Fase 1 — Fundamentos Frontend (proyecto: `escaladapro/web`)
- [ ] Crear `types/api.ts` en `/Users/wiletinoco/VUE/escaladapro/web/types/`
- [ ] Extender `composables/useApi.ts` con métodos tipados por módulo

### Fase 2 — Conectar páginas
- [ ] Eliminar `pages/blog.vue` (ruta duplicada)
- [ ] Eliminar `pages/como-apoyar.vue` (ruta duplicada)
- [ ] Renombrar `pages/blog/article.vue` → `pages/blog/[slug].vue`
- [ ] Conectar `pages/contacto.vue` → `POST /api/v1/contact`
- [ ] Conectar `pages/transparencia.vue` → `GET /api/v1/transparency-documents`
- [ ] Conectar `pages/blog/index.vue` → `GET /api/v1/blog`
- [ ] Conectar `pages/blog/all.vue` → `GET /api/v1/blog` (paginado)
- [ ] Conectar `pages/blog/[slug].vue` → `GET /api/v1/blog/{slug}`
- [ ] Conectar `pages/como-apoyar/productos.vue` → `GET /api/v1/products`
- [ ] Conectar `pages/actividades.vue` → `GET /api/v1/support-campaigns`
- [ ] Conectar `pages/patrocinio.vue` → `GET /api/v1/sponsors`
- [ ] Conectar `components/layouts/Header.vue` → `GET /api/v1/menus/main`

### Fase 3 — Gyms + Settings
- [ ] Crear modelo/migración/controlador `Gym` en scaladapro-api
- [ ] Agregar ruta `GET /api/v1/gyms` y recurso `GymResource`
- [ ] Conectar `pages/como-apoyar/gyms.vue` → `GET /api/v1/gyms`
- [ ] Conectar settings del sitio al layout

### Fase 4 — Tests y Seeders
- [ ] Crear tests Pest para todos los endpoints `/v1/`
- [ ] Completar seeders: Settings, Menu, Pages, DemoData
- [ ] Implementar `useHead()` con SEO en páginas dinámicas

### Fase 5 — Producción
- [ ] Configurar variables de entorno de producción
- [ ] Verificar CORS con dominio real
- [ ] Activar SSR en páginas de blog

