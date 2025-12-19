<?php

declare(strict_types=1);

/**
 * LittleCoder - メインエントリーポイント
 */

// CORSヘッダー
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// OPTIONSリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// オートローダー
spl_autoload_register(function (string $class) {
    $prefix = 'LittleCoder\\';
    $baseDir = __DIR__ . '/../src/';

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

// 環境変数の読み込み
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    \LittleCoder\Infra\Env::load($envPath);
}

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// フロントエンドのビルド済みファイルを提供（静的ファイル）
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// APIリクエストでない場合は index.html を返す（SPA対応）
if (!str_starts_with($path, '/api/')) {
    $publicPath = __DIR__;
    $filePath = $publicPath . $path;

    // 静的ファイルが存在する場合はそれを返す
    if ($path !== '/' && file_exists($filePath) && is_file($filePath)) {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'js' => 'application/javascript',
            'css' => 'text/css',
            'html' => 'text/html',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];

        if (isset($mimeTypes[$extension])) {
            header('Content-Type: ' . $mimeTypes[$extension]);
        }

        readfile($filePath);
        exit;
    }

    // index.html を返す（SPA）
    $indexPath = $publicPath . '/index.html';
    if (file_exists($indexPath)) {
        header('Content-Type: text/html');
        readfile($indexPath);
        exit;
    }

    // index.html がない場合はウェルカムメッセージ
    header('Content-Type: application/json');
    echo json_encode([
        'name' => 'LittleCoder API',
        'version' => '1.0.0',
        'endpoints' => [
            'GET /api/problems/list.php' => '問題一覧',
            'GET /api/problems/get.php?id=N' => '問題詳細',
            'POST /api/submissions/create.php' => '提出',
            'GET /api/submissions/result.php?id=N' => '提出結果',
            'GET /api/leaderboard/get.php?problemId=N' => 'ランキング',
        ],
    ]);
}
