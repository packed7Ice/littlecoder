<?php

declare(strict_types=1);

/**
 * GET /api/submissions/result.php?id=N
 * 提出結果を取得
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
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing or invalid parameter: id',
        ]);
        exit;
    }

    $id = (int)$_GET['id'];

    $repo = new SubmissionRepository();
    $submission = $repo->findById($id);

    if ($submission === null) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Submission not found',
        ]);
        exit;
    }

    // ケース別結果を取得
    $caseResults = $repo->getCaseResults($id);

    echo json_encode([
        'success' => true,
        'submission' => [
            'id' => $submission['id'],
            'problemId' => $submission['problemId'],
            'status' => $submission['status'],
            'score' => $submission['score'],
            'elapsedMs' => $submission['elapsedMs'],
            'createdAt' => $submission['createdAt'],
            'caseResults' => array_map(function ($r) {
                return [
                    'caseIndex' => (int)$r['case_index'],
                    'status' => $r['status'],
                ];
            }, $caseResults),
        ],
    ]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
