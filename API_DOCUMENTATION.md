# EscaladaPro API - Documentación

## 🎯 Descripción
API REST para el CMS de EscaladaPro, construida con Laravel 12 y Filament 3.

## 🚀 Instalación

### Requisitos
- PHP 8.2+
- Composer
- Base de datos (MySQL/PostgreSQL)

### Configuración
1. Clonar el repositorio
2. Instalar dependencias:
```bash
composer install
```

3. Configurar archivo `.env`:
```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=escaladapro
DB_USERNAME=root
DB_PASSWORD=
```

4. Generar key y migrar base de datos:
```bash
php artisan key:generate
php artisan migrate
php artisan storage:link
```

5. Crear usuario administrador:
```bash
php artisan make:filament-user
```

## 📊 Panel de Administración

### Acceso
- URL: `http://localhost:8000/admin`
- Usuario: admin@escaladapro.com
- Contraseña: (la que configuraste)

### Módulos Disponibles

#### 📰 Blog/Artículos
- Gestión de artículos del blog
- Editor de texto enriquecido
- Subida de imágenes
- Estados: Borrador/Publicado
- Generación automática de slug
- Fecha de publicación programada

#### 🏢 Gimnasios
- Directorio de gimnasios
- Logo
- Descripción
- URL del sitio web
- Estado activo/inactivo

#### 🛍️ Productos
- Catálogo de productos
- Foto del producto
- Descripción
- Precio en USD
- Estado activo/inactivo

#### ⭐ Patrocinadores
- Gestión de patrocinadores
- Foto y banner
- Cargo/posición
- Descripción con editor rico
- Lista de productos del patrocinador
- Redes sociales (Facebook, Instagram, Twitter, YouTube, TikTok, LinkedIn)
- Estado activo/inactivo

## 🔌 API Endpoints

Base URL: `http://localhost:8000/api/v1`

### Artículos

#### Listar artículos
```http
GET /api/v1/articles
```
**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Título del artículo",
      "slug": "titulo-del-articulo",
      "content": "Contenido completo...",
      "excerpt": "Extracto breve",
      "image": "http://localhost:8000/storage/articles/imagen.jpg",
      "status": "published",
      "published_at": "2025-11-12 10:00:00",
      "created_at": "2025-11-12 09:00:00",
      "updated_at": "2025-11-12 09:30:00"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Obtener artículo por slug
```http
GET /api/v1/articles/{slug}
```

### Gimnasios

#### Listar gimnasios
```http
GET /api/v1/gyms
```
**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Gimnasio Ejemplo",
      "logo": "http://localhost:8000/storage/gyms/logo.jpg",
      "description": "Descripción del gimnasio",
      "url": "https://ejemplo.com",
      "is_active": true
    }
  ]
}
```

#### Obtener gimnasio por ID
```http
GET /api/v1/gyms/{id}
```

### Productos

#### Listar productos
```http
GET /api/v1/products
```
**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Producto Ejemplo",
      "photo": "http://localhost:8000/storage/products/foto.jpg",
      "description": "Descripción del producto",
      "price": "99.99",
      "is_active": true
    }
  ]
}
```

#### Obtener producto por ID
```http
GET /api/v1/products/{id}
```

### Patrocinadores

#### Listar patrocinadores
```http
GET /api/v1/sponsors
```
**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Patrocinador Ejemplo",
      "photo": "http://localhost:8000/storage/sponsors/photos/foto.jpg",
      "position": "Patrocinador Oficial",
      "description": "Descripción del patrocinador",
      "products": [
        {
          "name": "Producto 1",
          "url": "https://ejemplo.com/producto1"
        }
      ],
      "banner": "http://localhost:8000/storage/sponsors/banners/banner.jpg",
      "social_networks": [
        {
          "platform": "instagram",
          "url": "https://instagram.com/ejemplo"
        }
      ],
      "is_active": true
    }
  ]
}
```

#### Obtener patrocinador por ID
```http
GET /api/v1/sponsors/{id}
```

## 🔒 CORS
La API está configurada para aceptar peticiones desde `http://localhost:3000` (configuración por defecto para Nuxt).

Para cambiar la URL permitida, modifica la variable `FRONTEND_URL` en el archivo `.env`.

## 🎨 Características de Filament

### Navegación Organizada
Los recursos están organizados en grupos:
- **Contenido**: Artículos
- **Directorio**: Gimnasios
- **Tienda**: Productos
- **Marketing**: Patrocinadores

### Campos Especiales
- **Editor Rico**: Para contenido y descripciones largas
- **Subida de Archivos**: Con previsualización de imágenes
- **Repeaters**: Para listas dinámicas (productos y redes sociales de patrocinadores)
- **Generación Automática**: Slug automático desde el título en artículos
- **Toggle**: Para activar/desactivar registros
- **Date Picker**: Para programar publicaciones

### Validaciones
- Slugs únicos para artículos
- URLs validadas para gimnasios y redes sociales
- Campos requeridos donde corresponde
- Precios con formato decimal

## 🔧 Comandos Útiles

### Desarrollo
```bash
# Iniciar servidor
php artisan serve

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Base de Datos
```bash
# Crear nueva migración
php artisan make:migration create_table_name

# Ejecutar migraciones
php artisan migrate

# Revertir última migración
php artisan migrate:rollback

# Refrescar base de datos
php artisan migrate:fresh --seed
```

### Filament
```bash
# Crear nuevo recurso
php artisan make:filament-resource ModelName --generate

# Crear usuario admin
php artisan make:filament-user

# Limpiar caché de Filament
php artisan filament:cache-components
```

## 📝 Notas Importantes

1. **Imágenes**: Las imágenes se almacenan en `storage/app/public/` y se sirven a través de `/storage/`
2. **Paginación**: Los artículos están paginados (10 por página)
3. **Filtros**: Solo se devuelven registros activos/publicados en la API
4. **Ordenamiento**: 
   - Artículos: Por fecha de publicación (descendente)
   - Otros: Por nombre (ascendente)

## 🔐 Seguridad

- Las rutas de API son públicas para lectura
- El panel de Filament requiere autenticación
- CORS configurado para el frontend
- Sanctum listo para autenticación si se necesita en el futuro

## 🌐 Integración con Nuxt

### Ejemplo de uso en Nuxt 3:

```typescript
// composables/useApi.ts
export const useApi = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase

  return {
    articles: {
      getAll: () => $fetch(`${baseURL}/api/v1/articles`),
      getBySlug: (slug: string) => $fetch(`${baseURL}/api/v1/articles/${slug}`)
    },
    gyms: {
      getAll: () => $fetch(`${baseURL}/api/v1/gyms`),
      getById: (id: number) => $fetch(`${baseURL}/api/v1/gyms/${id}`)
    },
    products: {
      getAll: () => $fetch(`${baseURL}/api/v1/products`),
      getById: (id: number) => $fetch(`${baseURL}/api/v1/products/${id}`)
    },
    sponsors: {
      getAll: () => $fetch(`${baseURL}/api/v1/sponsors`),
      getById: (id: number) => $fetch(`${baseURL}/api/v1/sponsors/${id}`)
    }
  }
}
```

### Configuración en nuxt.config.ts:
```typescript
export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      apiBase: process.env.API_BASE_URL || 'http://localhost:8000'
    }
  }
})
```

## 📞 Soporte

Para cualquier problema o pregunta, revisa los logs en:
- `storage/logs/laravel.log`

---

**Versión**: 1.0.0  
**Última actualización**: 12 de Noviembre, 2025
