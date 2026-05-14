<?php
declare(strict_types=1);

namespace App\Services;

final class CoinGeckoService
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $vsCurrency,
        private readonly string $coins,
        private readonly int $timeoutSeconds
    ) {}

    public function fetchMarketData(): array
    {
        $url = sprintf(
            '%s/coins/markets?vs_currency=%s&ids=%s&price_change_percentage=1h,24h,7d&sparkline=true&order=market_cap_desc&per_page=50&page=1',
            rtrim($this->baseUrl, '/'),
            urlencode($this->vsCurrency),
            urlencode($this->coins)
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);

        $raw = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $code >= 400) {
            return $this->mockData();
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : $this->mockData();
    }

    private function mockData(): array
    {
        $coins = [
            ['id' => 'bitcoin', 'symbol' => 'btc', 'name' => 'Bitcoin', 'price' => 68820],
            ['id' => 'ethereum', 'symbol' => 'eth', 'name' => 'Ethereum', 'price' => 3410],
            ['id' => 'solana', 'symbol' => 'sol', 'name' => 'Solana', 'price' => 168],
        ];

        return array_map(static function (array $c): array {
            return [
                'id' => $c['id'],
                'symbol' => $c['symbol'],
                'name' => $c['name'],
                'current_price' => $c['price'] + mt_rand(-80, 120),
                'market_cap' => mt_rand(8, 1400) * 1000000000,
                'total_volume' => mt_rand(1, 120) * 100000000,
                'price_change_percentage_1h_in_currency' => mt_rand(-220, 220) / 100,
                'price_change_percentage_24h_in_currency' => mt_rand(-850, 850) / 100,
                'price_change_percentage_7d_in_currency' => mt_rand(-1300, 1300) / 100,
                'sparkline_in_7d' => ['price' => array_map(static fn() => mt_rand(2000, 12000) / 100, range(1, 30))],
            ];
        }, $coins);
    }
}
