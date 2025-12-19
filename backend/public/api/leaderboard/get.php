<?php

declare(strict_types=1);

/**
 * GET /api/leaderboard/get.php?problemId=N
 * ランキングを取得
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// オートローダー
spl_autoload_register(function (string $class) {
    $prefix = 'LittleCoder\\';
    $baseDir = dirname(__DIR__, 3) . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use LittleCoder\Domain\SubmissionRepository;
use LittleCoder\Infra\Env;

try {
    // 環境変数の読み込み
    $envPath = dirname(__DIR__, 3) . '/.env';
    if (file_exists($envPath)) {
        Env::load($envPath);
    }

    // パラメータ検証
    if (!isset($_GET['problemId']) || !is_numeric($_GET['problemId'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing or invalid parameter: problemId',
        ]);
        exit;
    }

    $problemId = (int)$_GET['problemId'];
    $limit = isset($_GET['limit']) && is_numeric($_GET['limit'])
        ? min(50, max(1, (int)$_GET['limit']))
        : 10;

    $repo = new SubmissionRepository();
    $leaderboard = $repo->getLeaderboardFixed($problemId, $limit);

    echo json_encode([
        'success' => true,
        'problemId' => $problemId,
        'leaderboard' => $leaderboard,
    ]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
