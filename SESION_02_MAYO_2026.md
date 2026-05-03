# Sesión 2 de mayo de 2026

## Diagnóstico y corrección: Error 419 en peticiones POST desde el frontend QA

### Contexto

Se detectó que el sitio QA (`https://escalada.cobosdev.com`) no tenía conexión funcional con el administrador (`https://admin.cobosdev.com`) al momento de enviar formularios.

---

## Hallazgos mediante inspección con Playwright

Se inspeccionaron las siguientes páginas y peticiones de red:

| Ruta | Estado |
|------|--------|
| `GET /api/v1/blog` | ✅ 200 |
| `GET /api/v1/settings` | ✅ 200 |
| `GET /api/v1/menus/main` | ✅ 200 |
| `GET /api/v1/pages/contacto` | ✅ 200 |
| `GET /api/v1/sponsor-placements` | ✅ 200 |
| `POST /api/v1/contact` | ❌ **419** |

Las peticiones `GET` funcionaban correctamente. El problema se manifestaba únicamente en peticiones `POST` (formulario de contacto, comentarios de blog, inquiries de productos).

### Causa raíz

En `bootstrap/app.php`, el middleware `EnsureFrontendRequestsAreStateful` de Sanctum estaba prepuesto en el stack global de la API:

```php
// ❌ Configuración que causaba el problema
$middleware->api(prepend: [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
]);
```

Este middleware detecta el dominio `escalada.cobosdev.com` (definido en `FRONTEND_URL`) como un dominio "stateful", lo trata como una SPA con autenticación por cookie, y exige un token CSRF en cada petición. El frontend Nuxt nunca llama a `/sanctum/csrf-cookie` para obtener ese token, lo que provoca el rechazo con **HTTP 419 CSRF Token Mismatch**.

---

## Corrección implementada

**Archivo:** `bootstrap/app.php`

Se eliminó `EnsureFrontendRequestsAreStateful` del middleware global de la API:

```php
// ✅ Después del fix
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
    ]);
})
```

**Justificación:**
- Las rutas públicas (`/contact`, `/blog/{id}/comments`, `/products/{id}/inquiries`) no requieren autenticación por cookie ni CSRF.
- Las rutas protegidas (`GET /user`) ya usan el guard `auth:sanctum` con token Bearer, que funciona independientemente del middleware stateful.

---

## Acción requerida en producción

Verificar que el `.env` del servidor de producción tenga las variables correctas:

```dotenv
APP_URL=https://admin.cobosdev.com
FRONTEND_URL=https://escalada.cobosdev.com
```

Después de hacer deploy, ejecutar:

```bash
php artisan config:cache
php artisan route:cache
```
