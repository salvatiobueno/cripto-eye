<?php
declare(strict_types=1);

use App\Core\Response;

require_once __DIR__ . '/bootstrap.php';

Response::json([
    'ok' => true,
    'service' => 'eye-market-intelligence',
    'time' => gmdate('c'),
    'php' => PHP_VERSION,
]);
