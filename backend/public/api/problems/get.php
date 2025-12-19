<?php

declare(strict_types=1);

/**
 * GET /api/problems/get.php?id=N
 * 問題詳細を取得
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
use LittleCoder\Domain\CodeAssembler;
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

    // パラメータ検証
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing or invalid parameter: id',
        ]);
        exit;
    }

    $id = (int)$_GET['id'];

    $repo = new ProblemRepository();
    $problem = $repo->findById($id);

    if ($problem === null) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Problem not found',
        ]);
        exit;
    }

    // テストケースの内容は非公開（数のみ返す）
    $testCount = count($problem['tests']);
    unset($problem['tests']);

    // テンプレートを表示用に変換
    $assembler = new CodeAssembler();
    $problem['displayCode'] = $assembler->formatForDisplay($problem['template_code']);

    echo json_encode([
        'success' => true,
        'problem' => [
            'id' => $problem['id'],
            'title' => $problem['title'],
            'statementMd' => $problem['statement_md'],
            'templateCode' => $problem['template_code'],
            'displayCode' => $problem['displayCode'],
            'holes' => $problem['holes'],
            'testCount' => $testCount,
            'timeLimitMs' => $problem['time_limit_ms'],
            'difficulty' => $problem['difficulty'],
        ],
    ]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
