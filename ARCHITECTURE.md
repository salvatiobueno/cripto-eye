# Arquitectura y flujo

## Estructura

- `public/` UI y endpoints HTTP
- `public/api/` endpoints (`dashboard.php`, `insights.php`, `health.php`)
- `src/Core/` env, seguridad, respuesta y SQLite
- `src/Services/` cliente CoinGecko + cliente IA cloud
- `src/Analysis/` motor de patrones y anomalias
- `storage/` cache, rate-limit buckets y base SQLite
- `deploy/nginx-eye.conf` config base para Ubuntu + Nginx

## Flujo de datos

1. Frontend solicita `/api/dashboard.php`.
2. Backend usa cache corta (45s); si expira consulta CoinGecko.
3. Se guarda snapshot en SQLite para historico.
4. `PatternEngine` calcula correlaciones, momentum, anomalias.
5. Frontend pinta heatmap, barras, mini charts, alertas glow.
6. Frontend solicita `/api/insights.php` para explicacion IA natural.
7. `AiInsightService` llama modelo cloud con API key de `.env`.
8. Si IA falla, usa explicacion heuristica local.

## Endpoints

- `GET /api/dashboard.php`
- `GET /api/insights.php`
- `GET /api/health.php`

## Seguridad minima incluida

- API keys solo en backend (`.env`)
- Headers de seguridad + CSP
- Rate limiting por IP/minuto
- Mensajes de error controlados
