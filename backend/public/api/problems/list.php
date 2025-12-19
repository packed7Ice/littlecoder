<?php

declare(strict_types=1);

/**
 * GET /api/problems/list.php
 * 問題一覧を取得
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

use LittleCoder\Domain\ProblemRepository;
use LittleCoder\Infra\Db;
use LittleCoder\Infra\Env;

try {
    // 環境変数の読み込み
    $envPath = dirname(__DIR__, 3) . '/.env';
    if (file_exists($envPath)) {
        Env::load($envPath);
    }

    // DBが存在しない場合は初期化
    $dbPath = Env::get('DB_PATH', dirname(__DIR__, 3) . '/data/littlecoder.sqlite');
    if (!str_starts_with($dbPath, '/') && !preg_match('/^[A-Z]:/i', $dbPath)) {
        $dbPath = dirname(__DIR__, 3) . '/' . $dbPath;
    }

    if (!file_exists($dbPath)) {
        Db::initSchema();
    }

    $repo = new ProblemRepository();
    $problems = $repo->findAll();

    echo json_encode([
        'success' => true,
        'problems' => $problems,
    ]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
