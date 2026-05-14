<?php
declare(strict_types=1);

namespace App\Core;

final class Security
{
    public static function applyHeaders(): void
    {
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), camera=(), microphone=()');
        header('Content-Security-Policy: default-src \'self\'; script-src \'self\' https://cdn.tailwindcss.com https://cdn.jsdelivr.net; style-src \'self\' \'unsafe-inline\' https://fonts.googleapis.com; font-src \'self\' https://fonts.gstatic.com; connect-src \'self\'; img-src \'self\' data:;');
    }

    public static function applyCors(string $allowedOrigin): void
    {
        header('Access-Control-Allow-Origin: ' . $allowedOrigin);
        header('Access-Control-Allow-Headers: Content-Type');
    }

    public static function rateLimit(string $ip, int $maxPerMinute, string $storageDir): void
    {
        $safeIp = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $ip) ?? 'unknown';
        $bucket = $storageDir . '/rate_' . $safeIp . '_' . date('YmdHi') . '.json';
        $count = 0;

        if (is_file($bucket)) {
            $raw = file_get_contents($bucket);
            $decoded = json_decode((string)$raw, true);
            $count = (int)($decoded['count'] ?? 0);
        }
        $count++;
        file_put_contents($bucket, json_encode(['count' => $count], JSON_UNESCAPED_UNICODE));

        if ($count > $maxPerMinute) {
            Response::json(['ok' => false, 'error' => 'Rate limit exceeded'], 429);
        }
    }
}
