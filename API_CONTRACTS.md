# API Contracts - Escalada Pro v1

Base URL: `https://api.escaladapro.com/api/v1`

## Autenticación
- Public endpoints: no requieren autenticación
- Protected endpoints: requieren `Bearer token` (Sanctum)

---

## 📄 Pages & CMS

### `GET /pages`
Lista todas las páginas publicadas
```json
Response 200:
{
  "data": [
    {
      "id": 1,
      "slug": "home",
      "title": "Inicio",
      "template": "home",
      "seo_title": "Escalada Pro - Inicio",
      "seo_description": "...",
      "sections": [...]
    }
  ]
}
```

### `GET /pages/{slug}`
Obtiene una página con sus secciones
```json
Response 200:
{
  "data": {
    "id": 1,
    "slug": "nosotros",
    "title": "Nosotros",
    "template": "default",
    "sections": [
      {
        "id": 1,
        "type": "hero",
        "heading": "Quiénes Somos",
        "subheading": "...",
        "body": "...",
        "items": [],
        "media": []
      }
    ],
    "updated_at": "2026-01-17T00:00:00.000000Z"
  }
}
```

---

## 📰 Blog

### `GET /blog/posts`
Lista posts publicados (paginado)
```json
Query params:
- page=1
- per_page=12
- search=texto

Response 200:
{
  "data": [
    {
      "id": 1,
      "title": "Título del post",
      "slug": "titulo-del-post",
      "excerpt": "...",
      "featured_media": {
        "url": "...",
        "alt": "..."
      },
      "published_at": "2026-01-15T00:00:00.000000Z",
      "comments_count": 5
    }
  ],
  "links": {...},
  "meta": {...}
}
```

### `GET /blog/posts/{slug}`
Detalle de un post con comentarios aprobados
```json
Response 200:
{
  "data": {
    "id": 1,
    "title": "...",
    "slug": "...",
    "body": "...",
    "featured_media": {...},
    "published_at": "...",
    "comments": [
      {
        "id": 1,
        "name": "Juan",
        "comment": "...",
        "created_at": "..."
      }
    ]
  }
}
```

### `POST /blog/posts/{id}/comments`
Crear comentario (público)
```json
Request:
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "comment": "Excelente artículo..."
}

Response 201:
{
  "message": "Comentario enviado. Está pendiente de moderación.",
  "data": {
    "id": 5,
    "status": "pending"
  }
}
```

---

## 📦 Products (Catálogo)

### `GET /products`
Lista productos publicados
```json
Query params:
- category_id=1
- search=texto
- page=1

Response 200:
{
  "data": [
    {
      "id": 1,
      "name": "Producto X",
      "slug": "producto-x",
      "summary": "...",
      "price": "50.00",
      "currency": "USD",
      "category": {
        "id": 1,
        "name": "Equipamiento"
      },
      "featured_media": {
        "url": "...",
        "alt": "..."
      },
      "publisher": {
        "name": "Usuario",
        "phone": "+123456789"
      }
    }
  ],
  "meta": {...}
}
```

### `GET /products/{slug}`
Detalle del producto
```json
Response 200:
{
  "data": {
    "id": 1,
    "name": "...",
    "slug": "...",
    "description": "...",
    "price": "50.00",
    "currency": "USD",
    "category": {...},
    "featured_media": {...},
    "gallery": [...],
    "publisher": {
      "name": "Usuario",
      "email": "usuario@example.com",
      "phone": "+123456789"
    }
  }
}
```

### `GET /product-categories`
Lista de categorías de productos
```json
Response 200:
{
  "data": [
    {
      "id": 1,
      "name": "Equipamiento",
      "slug": "equipamiento",
      "products_count": 12
    }
  ]
}
```

### `POST /products/{id}/inquiries`
Enviar consulta sobre un producto
```json
Request:
{
  "name": "María González",
  "email": "maria@example.com",
  "message": "¿Está disponible?"
}

Response 201:
{
  "message": "Consulta enviada al vendedor"
}
```

---

## 🤝 Sponsors

### `GET /sponsors`
Lista patrocinadores activos
```json
Response 200:
{
  "data": [
    {
      "id": 1,
      "name": "Patrocinador 1",
      "slug": "patrocinador-1",
      "logo": {
        "url": "...",
        "alt": "..."
      },
      "website_url": "https://...",
      "description": "..."
    }
  ]
}
```

### `GET /sponsor-placements/{placement}`
Placements activos por ubicación
```json
Query: placement = home_footer | blog_sidebar | global

Response 200:
{
  "data": [
    {
      "id": 1,
      "title": "...",
      "body": "...",
      "banner": {
        "url": "...",
        "alt": "..."
      },
      "link_url": "...",
      "sponsor": {
        "name": "...",
        "logo": {...}
      }
    }
  ]
}
```

---

## 💛 Cómo Apoyar

### `GET /support-campaigns`
Campañas activas de apoyo
```json
Response 200:
{
  "data": [
    {
      "id": 1,
      "name": "Campaña 2026",
      "slug": "campaña-2026",
      "description": "...",
      "methods": [
        {
          "id": 1,
          "type": "paypal",
          "title": "PayPal",
          "body": "...",
          "settings": {
            "paypal_email": "...",
            "paypal_link": "..."
          }
        },
        {
          "id": 2,
          "type": "transfer",
          "title": "Transferencia Bancaria",
          "body": "...",
          "settings": {
            "bank": "...",
            "account": "...",
            "swift": "..."
          }
        }
      ]
    }
  ]
}
```

---

## 📊 Transparencia

### `GET /transparency-documents`
Documentos de transparencia publicados
```json
Query params:
- year=2026
- type=financial|report|legal|other

Response 200:
{
  "data": [
    {
      "id": 1,
      "title": "Informe Financiero 2025",
      "year": 2025,
      "type": "financial",
      "description": "...",
      "document": {
        "url": "https://.../documento.pdf",
        "size": 2048000,
        "file_name": "informe-2025.pdf"
      },
      "published_at": "..."
    }
  ],
  "grouped_by_year": {
    "2026": [...],
    "2025": [...]
  }
}
```

---

## 📬 Contacto

### `POST /contact`
Enviar mensaje de contacto
```json
Request:
{
  "name": "María López",
  "email": "maria@example.com",
  "phone": "+123456789",
  "subject": "Consulta sobre membresía",
  "message": "Hola, quisiera información..."
}

Response 201:
{
  "message": "Mensaje enviado correctamente. Te responderemos pronto."
}
```

---

## 🧭 Menús

### `GET /menus/{name}`
Obtiene items de un menú (main, footer)
```json
Response 200:
{
  "data": {
    "name": "main",
    "items": [
      {
        "id": 1,
        "label": "Inicio",
        "url": "/",
        "page_id": 1,
        "children": []
      },
      {
        "id": 2,
        "label": "Nosotros",
        "url": null,
        "page_id": 2,
        "children": [
          {
            "id": 3,
            "label": "Historia",
            "url": "/nosotros/historia",
            "page_id": 3
          }
        ]
      }
    ]
  }
}
```

---

## ⚙️ Settings

### `GET /settings`
Configuración pública del sitio
```json
Response 200:
{
  "data": {
    "site_name": "Escalada Pro",
    "contact_email": "info@escaladapro.com",
    "contact_phone": "+123456789",
    "social": {
      "facebook": "...",
      "instagram": "...",
      "youtube": "..."
    },
    "footer_text": "...",
    "maintenance_mode": false
  }
}
```

---

## 🔍 Search (Global)

### `GET /search`
Búsqueda global en blog y productos
```json
Query params:
- q=texto
- type=blog|products|all

Response 200:
{
  "data": {
    "blog_posts": [...],
    "products": [...],
    "total_results": 15
  }
}
```

---

## Error Responses

```json
400 Bad Request:
{
  "message": "Validation error",
  "errors": {
    "email": ["El campo email es obligatorio"]
  }
}

404 Not Found:
{
  "message": "Resource not found"
}

500 Server Error:
{
  "message": "Internal server error"
}
```
