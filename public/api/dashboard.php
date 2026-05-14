<?php
declare(strict_types=1);

use App\Analysis\PatternEngine;
use App\Core\Env;
use App\Core\Response;
use App\Services\CoinGeckoService;

require_once __DIR__ . '/bootstrap.php';

$cacheFile = dirname(__DIR__, 2) . '/storage/cache_dashboard.json';
$cacheSeconds = (int)(Env::get('COINGECKO_CACHE_SECONDS', '45') ?? '45');

if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < $cacheSeconds) {
    $cached = json_decode((string)file_get_contents($cacheFile), true);
    if (is_array($cached)) {
        Response::json($cached);
    }
}

$service = new CoinGeckoService(
    Env::get('COINGECKO_BASE_URL', 'https://api.coingecko.com/api/v3') ?? 'https://api.coingecko.com/api/v3',
    Env::get('COINGECKO_VS_CURRENCY', 'usd') ?? 'usd',
    Env::get('COINGECKO_COINS', 'bitcoin,ethereum,solana') ?? 'bitcoin,ethereum,solana',
    (int)(Env::get('COINGECKO_REQUEST_TIMEOUT', '8') ?? '8')
);

$coins = $service->fetchMarketData();
$db->insertSnapshot($coins);
$history = $db->latestSnapshots(40);

$engine = new PatternEngine();
$analysis = $engine->analyze($coins, $history);

$response = [
    'ok' => true,
    'generated_at' => gmdate('c'),
    'coins' => $coins,
    'analysis' => $analysis,
];
file_put_contents($cacheFile, json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

Response::json($response);
