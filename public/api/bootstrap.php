<?php
declare(strict_types=1);

use App\Core\Database;
use App\Core\Env;
use App\Core\Security;

require_once dirname(__DIR__, 2) . '/src/Core/Env.php';
require_once dirname(__DIR__, 2) . '/src/Core/Response.php';
require_once dirname(__DIR__, 2) . '/src/Core/Security.php';
require_once dirname(__DIR__, 2) . '/src/Core/Database.php';
require_once dirname(__DIR__, 2) . '/src/Services/CoinGeckoService.php';
require_once dirname(__DIR__, 2) . '/src/Services/AiInsightService.php';
require_once dirname(__DIR__, 2) . '/src/Analysis/PatternEngine.php';

Env::load(dirname(__DIR__, 2) . '/.env');

$allowedOrigin = Env::get('APP_ALLOWED_ORIGIN', '*') ?? '*';
Security::applyHeaders();
Security::applyCors($allowedOrigin);

$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$rate = (int)(Env::get('APP_RATE_LIMIT_PER_MIN', '60') ?? '60');
Security::rateLimit($ip, $rate, dirname(__DIR__, 2) . '/storage');

$dbPath = dirname(__DIR__, 2) . '/' . (Env::get('DB_PATH', 'storage/market.sqlite') ?? 'storage/market.sqlite');
$db = new Database($dbPath);
