<?php

declare(strict_types=1);

/**
 * POST /api/submissions/create.php
 * 提出を作成して判定を実行
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed',
    ]);
    exit;
}

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
use LittleCoder\Domain\SubmissionRepository;
use LittleCoder\Domain\CodeAssembler;
use LittleCoder\Domain\JudgeService;
use LittleCoder\Infra\Db;
use LittleCoder\Infra\Env;
use LittleCoder\Infra\RateLimit;

try {
    // 環境変数の読み込み
    $envPath = dirname(__DIR__, 3) . '/.env';
    if (file_exists($envPath)) {
        Env::load($envPath);
    }

    // セッション開始
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $sessionId = session_id();

    // レート制限チェック
    $rateLimit = new RateLimit();
    $rateLimitResult = $rateLimit->check($sessionId);

    if (!$rateLimitResult['allowed']) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'error' => 'Rate limit exceeded. Please wait.',
            'resetAt' => $rateLimitResult['resetAt'],
            'remaining' => $rateLimitResult['remaining'],
        ]);
        exit;
    }

    // リクエストボディをパース
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['problemId']) || !is_int($input['problemId'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing or invalid parameter: problemId',
        ]);
        exit;
    }

    if (!isset($input['answers']) || !is_array($input['answers'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing or invalid parameter: answers',
        ]);
        exit;
    }

    $problemId = $input['problemId'];
    $answers = $input['answers'];
    $startedAt = $input['startedAt'] ?? null;
    $userName = isset($input['userName']) ? trim($input['userName']) : null;

    // ユーザー名をセッションに保存
    if ($userName) {
        $_SESSION['user_name'] = $userName;
    }

    // DBが存在しない場合は初期化
    $dbPath = Env::get('DB_PATH', dirname(__DIR__, 3) . '/data/littlecoder.sqlite');
    if (!str_starts_with($dbPath, '/') && !preg_match('/^[A-Z]:/i', $dbPath)) {
        $dbPath = dirname(__DIR__, 3) . '/' . $dbPath;
    }

    if (!file_exists($dbPath)) {
        Db::initSchema();
    }

    // 問題を取得
    $problemRepo = new ProblemRepository();
    $problem = $problemRepo->findForJudge($problemId);

    if ($problem === null) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Problem not found',
        ]);
        exit;
    }

    // コードを組み立て
    $assembler = new CodeAssembler();

    try {
        $code = $assembler->assemble($problem['template_code'], $problem['holes'], $answers);
    } catch (\InvalidArgumentException $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
        ]);
        exit;
    }

    // 提出回数を取得
    $submissionRepo = new SubmissionRepository();
    $tries = $submissionRepo->countBySessionAndProblem($sessionId, $problemId) + 1;

    // 提出を作成（PENDING状態）
    $submissionId = $submissionRepo->create($problemId, $sessionId, $answers, $userName);

    // レート制限カウントを増やす
    $rateLimit->increment($sessionId);

    // 判定を実行
    $judgeService = new JudgeService();

    try {
        $judgeResult = $judgeService->judge(
            $code,
            $problem['tests'],
            $problem['time_limit_ms'],
            $problem['memory_limit_kb']
        );
    } catch (\Exception $e) {
        // Judge0 APIエラー
        $submissionRepo->update($submissionId, 'IE', 0, 0);

        http_response_code(503);
        echo json_encode([
            'success' => false,
            'error' => 'Judge system error: ' . $e->getMessage(),
            'submissionId' => $submissionId,
        ]);
        exit;
    }

    // 経過時間を計算
    $elapsedMs = 0;
    if ($startedAt !== null && is_numeric($startedAt)) {
        $nowMs = (int)(microtime(true) * 1000);
        $startMs = (int)$startedAt;
        $elapsedMs = max(0, $nowMs - $startMs);

        // 経過時間が異常に大きい場合（1時間以上）はリセット
        // これはクライアントのタイムスタンプが不正な場合に発生する
        if ($elapsedMs > 3600000) {
            $elapsedMs = 60000; // デフォルト1分として扱う
        }
    }

    // スコアを計算
    $score = $judgeService->calculateScore(
        $judgeResult['status'],
        $elapsedMs,
        $tries,
        $judgeResult['passedCount'],
        $judgeResult['totalCount']
    );

    // 提出を更新
    $submissionRepo->update($submissionId, $judgeResult['status'], $score, $elapsedMs);

    // ケース別結果を保存
    $submissionRepo->saveCaseResults($submissionId, $judgeResult['caseResults']);

    // レスポンス
    echo json_encode([
        'success' => true,
        'submissionId' => $submissionId,
        'status' => $judgeResult['status'],
        'score' => $score,
        'elapsedMs' => $elapsedMs,
        'passedCount' => $judgeResult['passedCount'],
        'totalCount' => $judgeResult['totalCount'],
        'caseResults' => array_map(function ($r) {
            // 期待値と実際の出力は返さない（ヒント防止）
            return [
                'caseIndex' => $r['caseIndex'],
                'status' => $r['status'],
            ];
        }, $judgeResult['caseResults']),
        'tries' => $tries,
        'remaining' => $rateLimit->getRemaining($sessionId),
    ]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
