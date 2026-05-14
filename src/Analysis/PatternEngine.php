<?php
declare(strict_types=1);

namespace App\Analysis;

final class PatternEngine
{
    public function analyze(array $coins, array $history): array
    {
        $pairs = $this->buildPairCorrelations($coins);
        $anomalies = $this->detectAnomalies($coins);
        $marketPulse = $this->marketPulse($coins);
        $insights = $this->heuristicInsights($pairs, $anomalies, $marketPulse);

        return [
            'market_pulse' => $marketPulse,
            'correlations' => $pairs,
            'anomalies' => $anomalies,
            'insights' => $insights,
            'history_points' => count($history),
        ];
    }

    private function buildPairCorrelations(array $coins): array
    {
        $changes = [];
        foreach ($coins as $c) {
            $changes[] = [
                'symbol' => strtoupper((string)$c['symbol']),
                'v1h' => (float)($c['price_change_percentage_1h_in_currency'] ?? 0),
                'v24h' => (float)($c['price_change_percentage_24h_in_currency'] ?? 0),
                'v7d' => (float)($c['price_change_percentage_7d_in_currency'] ?? 0),
            ];
        }

        $out = [];
        for ($i = 0; $i < count($changes); $i++) {
            for ($j = $i + 1; $j < count($changes); $j++) {
                $a = $changes[$i];
                $b = $changes[$j];
                $score = $this->vectorSimilarity([$a['v1h'], $a['v24h'], $a['v7d']], [$b['v1h'], $b['v24h'], $b['v7d']]);
                $out[] = [
                    'pair' => $a['symbol'] . '/' . $b['symbol'],
                    'score' => round($score, 3),
                    'type' => $score > 0.78 ? 'high' : ($score > 0.45 ? 'medium' : 'low'),
                ];
            }
        }

        usort($out, static fn(array $x, array $y) => $y['score'] <=> $x['score']);
        return array_slice($out, 0, 20);
    }

    private function detectAnomalies(array $coins): array
    {
        $items = [];
        foreach ($coins as $coin) {
            $h1 = (float)($coin['price_change_percentage_1h_in_currency'] ?? 0);
            $h24 = (float)($coin['price_change_percentage_24h_in_currency'] ?? 0);
            $vol = (float)($coin['total_volume'] ?? 0);
            $cap = max((float)($coin['market_cap'] ?? 1), 1);
            $volatility = abs($h1) + abs($h24 / 2);
            $volumePressure = $vol / $cap;
            $momentum = ($h1 * 0.55) + ($h24 * 0.45);
            $state = $momentum > 1.6 ? 'bullish' : ($momentum < -1.6 ? 'bearish' : 'neutral');

            if ($volatility > 8 || $volumePressure > 0.18 || abs($momentum) > 5) {
                $items[] = [
                    'symbol' => strtoupper((string)$coin['symbol']),
                    'state' => $state,
                    'volatility' => round($volatility, 2),
                    'volume_pressure' => round($volumePressure, 4),
                    'momentum' => round($momentum, 2),
                ];
            }
        }

        usort($items, static fn(array $a, array $b) => $b['volatility'] <=> $a['volatility']);
        return array_slice($items, 0, 10);
    }

    private function marketPulse(array $coins): array
    {
        $avg1h = $this->avg(array_map(static fn(array $c) => (float)($c['price_change_percentage_1h_in_currency'] ?? 0), $coins));
        $avg24h = $this->avg(array_map(static fn(array $c) => (float)($c['price_change_percentage_24h_in_currency'] ?? 0), $coins));
        $avg7d = $this->avg(array_map(static fn(array $c) => (float)($c['price_change_percentage_7d_in_currency'] ?? 0), $coins));
        $dominance = $this->dominance($coins);

        return [
            'avg_1h' => round($avg1h, 2),
            'avg_24h' => round($avg24h, 2),
            'avg_7d' => round($avg7d, 2),
            'market_state' => $avg24h > 1.5 ? 'bullish' : ($avg24h < -1.5 ? 'bearish' : 'neutral'),
            'btc_dominance' => $dominance,
        ];
    }

    private function heuristicInsights(array $pairs, array $anomalies, array $pulse): array
    {
        $topPair = $pairs[0]['pair'] ?? 'N/A';
        $alerts = count($anomalies);
        return [
            sprintf('Se observa sincronizacion destacada en %s con fuerte alineacion de variaciones.', $topPair),
            sprintf('El mercado presenta sesgo %s (24h promedio %.2f%%).', $pulse['market_state'], $pulse['avg_24h']),
            sprintf('Se detectaron %d eventos de volatilidad o presion de volumen fuera del rango normal.', $alerts),
        ];
    }

    private function dominance(array $coins): float
    {
        $total = 0.0;
        $btc = 0.0;
        foreach ($coins as $c) {
            $cap = (float)($c['market_cap'] ?? 0);
            $total += $cap;
            if (($c['id'] ?? '') === 'bitcoin') {
                $btc = $cap;
            }
        }
        if ($total <= 0) {
            return 0.0;
        }
        return round(($btc / $total) * 100, 2);
    }

    private function vectorSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $ma = 0.0;
        $mb = 0.0;
        for ($i = 0; $i < count($a); $i++) {
            $dot += $a[$i] * $b[$i];
            $ma += $a[$i] * $a[$i];
            $mb += $b[$i] * $b[$i];
        }
        if ($ma <= 0 || $mb <= 0) {
            return 0.0;
        }
        return $dot / (sqrt($ma) * sqrt($mb));
    }

    private function avg(array $numbers): float
    {
        if ($numbers === []) {
            return 0.0;
        }
        return array_sum($numbers) / count($numbers);
    }
}
