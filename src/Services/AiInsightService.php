<?php
declare(strict_types=1);

namespace App\Services;

final class AiInsightService
{
    public function __construct(
        private readonly bool $enabled,
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly string $model,
        private readonly int $timeoutSeconds
    ) {
    }

    public function explain(array $analysis, array $coins): array
    {
        if (!$this->enabled || $this->apiKey === '') {
            return $this->fallback($analysis);
        }

        $isAnthropic = str_contains($this->baseUrl, 'anthropic.com');
        $prompt = $this->buildPrompt($analysis, $coins);
        $systemText = 'Eres un analista cuantitativo. Explica patrones cripto en espanol claro para inversores no tecnicos. Da recomendaciones de compra/venta.';

        if ($isAnthropic) {
            $url = rtrim($this->baseUrl, '/') . '/messages';
            $payload = [
                'model' => $this->model,
                'max_tokens' => 2000,
                'temperature' => 0.35,
                'system' => $systemText,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ];
            $headers = [
                'Content-Type: application/json',
                'x-api-key: ' . $this->apiKey,
                'anthropic-version: 2023-06-01',
            ];
        } else {
            $url = rtrim($this->baseUrl, '/') . '/chat/completions';
            $payload = [
                'model' => $this->model,
                'temperature' => 0.35,
                'messages' => [
                    ['role' => 'system', 'content' => $systemText],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ];
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ];
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);

        $raw = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $code >= 400) {
            return $this->fallback($analysis);
        }

        $decoded = json_decode((string) $raw, true);
        
        $text = $isAnthropic 
            ? ($decoded['content'][0]['text'] ?? null) 
            : ($decoded['choices'][0]['message']['content'] ?? null);

        if (!is_string($text) || trim($text) === '') {
            return $this->fallback($analysis);
        }

        return [
            'summary' => trim($text),
            'source' => 'cloud_ai',
            'disclaimer' => 'Los patrones detectados por IA son unicamente informativos y no constituyen asesoramiento financiero ni garantia predictiva.',
        ];
    }

    private function fallback(array $analysis): array
    {
        $i1 = $analysis['insights'][0] ?? 'No se detectaron senales fuertes.';
        $i2 = $analysis['insights'][1] ?? '';
        $i3 = $analysis['insights'][2] ?? '';
        return [
            'summary' => $i1 . ' ' . $i2 . ' ' . $i3 . ' El sistema recomienda validar estos hallazgos con contexto macro y liquidez de mercado.',
            'source' => 'local_heuristic',
            'disclaimer' => 'Los patrones detectados por IA son unicamente informativos y no constituyen asesoramiento financiero ni garantia predictiva.',
        ];
    }

    private function buildPrompt(array $analysis, array $coins): string
    {
        $topCoins = array_slice(array_map(static fn(array $c) => [
            'symbol' => strtoupper((string) $c['symbol']),
            'change_24h' => round((float) ($c['price_change_percentage_24h_in_currency'] ?? 0), 2),
            'change_7d' => round((float) ($c['price_change_percentage_7d_in_currency'] ?? 0), 2),
            'volume' => (float) ($c['total_volume'] ?? 0),
        ], $coins), 0, 8);

        return "Analiza estos datos y escribe:\n"
            . "1) resumen de mercado\n2) patrones detectados\n3) relaciones extranñas posibles explicaciones macro\n"
            . "Maximo 500 palabras. Con recomendación de inversiones.\n"
            . "Analisis interno: " . json_encode($analysis, JSON_UNESCAPED_UNICODE) . "\n"
            . "Coins: " . json_encode($topCoins, JSON_UNESCAPED_UNICODE);
    }
}
