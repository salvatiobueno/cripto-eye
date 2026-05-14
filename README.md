# Eye Market Intelligence (MVP)

Plataforma web ligera para visualizar mercado cripto en tiempo real con deteccion de patrones por IA.

## Stack

- Backend: PHP 8.3 (sin framework)
- Frontend: HTML + Tailwind CDN + Vanilla JS
- DB: SQLite
- Hosting target: Ubuntu + Nginx + PHP-FPM

## Inicio rapido

1. Copia `.env.example` a `.env` y agrega tu `AI_API_KEY`.
2. Asegura permisos de escritura en `storage/`.
3. Sirve `public/` como document root.

Ejemplo local con PHP built-in server:

```bash
php -S 127.0.0.1:8080 -t public
```

## Endpoints

- `GET /api/dashboard.php` datos para dashboard + indicadores
- `GET /api/insights.php` resumen IA + patrones detectados
- `GET /api/health.php` estado de servicios

Ejemplos:

```bash
curl http://127.0.0.1:8080/api/dashboard.php
curl http://127.0.0.1:8080/api/insights.php
curl http://127.0.0.1:8080/api/health.php
```

Para proveedor compatible (OpenAI/Claude-style gateway), ajusta:

- `AI_BASE_URL`
- `AI_MODEL`
- `AI_API_KEY`

## Despliegue en Ubuntu + Nginx

1. Instalar `php8.3-fpm` y extensiones `pdo_sqlite`, `curl`.
2. Apuntar `root` de Nginx a `/var/www/eye/public`.
3. Configurar `try_files $uri $uri/ =404;`
4. Pasar PHP por `fastcgi_pass unix:/run/php/php8.3-fpm.sock;`
5. Reiniciar servicios.

## Aviso legal

La interfaz muestra permanentemente:

> Los patrones detectados por IA son unicamente informativos y no constituyen asesoramiento financiero ni garantia predictiva.
