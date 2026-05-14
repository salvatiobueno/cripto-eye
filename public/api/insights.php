<?php
declare(strict_types=1);

use App\Analysis\PatternEngine;
use App\Core\Env;
use App\Core\Response;
use App\Services\AiInsightService;
use App\Services\CoinGeckoService;

require_once __DIR__ . '/bootstrap.php';

$service = new CoinGeckoService(
    Env::get('COINGECKO_BASE_URL', 'https://api.coingecko.com/api/v3') ?? 'https://api.coingecko.com/api/v3',
    Env::get('COINGECKO_VS_CURRENCY', 'usd') ?? 'usd',
    Env::get('COINGECKO_COINS', 'bitcoin,ethereum,solana') ?? 'bitcoin,ethereum,solana',
    (int)(Env::get('COINGECKO_REQUEST_TIMEOUT', '8') ?? '8')
);

$coins = $service->fetchMarketData();
$analysis = (new PatternEngine())->analyze($coins, $db->latestSnapshots(40));

$ai = new AiInsightService(
    filter_var(Env::get('AI_ENABLED', 'true') ?? 'true', FILTER_VALIDATE_BOOLEAN),
    Env::get('AI_API_KEY', '') ?? '',
    Env::get('AI_BASE_URL', 'https://api.openai.com/v1') ?? 'https://api.openai.com/v1',
    Env::get('AI_MODEL', 'gpt-4o-mini') ?? 'gpt-4o-mini',
    (int)(Env::get('AI_TIMEOUT', '20') ?? '20')
);

$insight = $ai->explain($analysis, $coins);

Response::json([
    'ok' => true,
    'generated_at' => gmdate('c'),
    'insight' => $insight,
    'analysis' => $analysis,
]);
