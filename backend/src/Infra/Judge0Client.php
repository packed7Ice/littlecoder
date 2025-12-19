<?php

declare(strict_types=1);

namespace LittleCoder\Infra;

/**
 * コード実行 API クライアント
 * Piston API (無料・APIキー不要) を使用
 * https://github.com/engineer-man/piston
 */
class Judge0Client
{
    // Piston API (無料)
    private const PISTON_URL = 'https://emkc.org/api/v2/piston/execute';

    /**
     * 同期実行
     * 
     * @param string $sourceCode ソースコード
     * @param string $stdin 標準入力
     * @param int $timeLimitMs 時間制限（ミリ秒）- Pistonでは秒単位
     * @param int $memoryLimitKb メモリ制限（KB）- Pistonでは未使用
     * @return array 実行結果
     */
    public function executeSync(
        string $sourceCode,
        string $stdin,
        int $timeLimitMs = 2000,
        int $memoryLimitKb = 131072
    ): array {
        $payload = [
            'language' => 'python',
            'version' => '3.10.0',
            'files' => [
                [
                    'name' => 'main.py',
                    'content' => $sourceCode,
                ]
            ],
            'stdin' => $stdin,
            'run_timeout' => max(1000, $timeLimitMs), // ミリ秒
        ];

        $response = $this->request($payload);

        // Piston レスポンスを Judge0 互換形式に変換
        return $this->convertResponse($response, $timeLimitMs);
    }

    /**
     * Piston レスポンスを Judge0 互換形式に変換
     */
    private function convertResponse(array $response, int $timeLimitMs): array
    {
        $run = $response['run'] ?? [];
        $compile = $response['compile'] ?? null;

        // コンパイルエラー（Pythonの場合は構文エラー）
        if ($compile && !empty($compile['stderr'])) {
            return [
                'status' => [
                    'id' => 6, // Compilation Error
                    'description' => 'Compilation Error',
                ],
                'stdout' => null,
                'stderr' => $compile['stderr'],
                'compile_output' => $compile['stderr'],
                'time' => null,
                'memory' => null,
                'exit_code' => $compile['code'] ?? 1,
            ];
        }

        // 実行結果
        $exitCode = $run['code'] ?? 0;
        $signal = $run['signal'] ?? null;
        $stdout = $run['stdout'] ?? '';
        $stderr = $run['stderr'] ?? '';

        // シグナルによるタイムアウト検出
        if ($signal === 'SIGKILL' || str_contains($stderr, 'timed out')) {
            return [
                'status' => [
                    'id' => 5, // Time Limit Exceeded
                    'description' => 'Time Limit Exceeded',
                ],
                'stdout' => $stdout,
                'stderr' => $stderr,
                'compile_output' => null,
                'time' => $timeLimitMs / 1000,
                'memory' => null,
                'exit_code' => $exitCode,
            ];
        }

        // ランタイムエラー
        if ($exitCode !== 0 || !empty($stderr)) {
            // Python の SyntaxError はランタイムエラーとして扱う
            if (str_contains($stderr, 'SyntaxError')) {
                return [
                    'status' => [
                        'id' => 6, // Compilation Error (Pythonの構文エラー)
                        'description' => 'Compilation Error',
                    ],
                    'stdout' => null,
                    'stderr' => $stderr,
                    'compile_output' => $stderr,
                    'time' => null,
                    'memory' => null,
                    'exit_code' => $exitCode,
                ];
            }

            return [
                'status' => [
                    'id' => 11, // Runtime Error (NZEC)
                    'description' => 'Runtime Error',
                ],
                'stdout' => $stdout,
                'stderr' => $stderr,
                'compile_output' => null,
                'time' => null,
                'memory' => null,
                'exit_code' => $exitCode,
            ];
        }

        // 正常終了
        return [
            'status' => [
                'id' => 3, // Accepted (正常終了)
                'description' => 'Accepted',
            ],
            'stdout' => $stdout,
            'stderr' => $stderr,
            'compile_output' => null,
            'time' => null,
            'memory' => null,
            'exit_code' => 0,
        ];
    }

    /**
     * HTTPリクエストを送信
     */
    private function request(array $payload): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => self::PISTON_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        // PHP 8.0+ では curl_close は不要（自動的にクローズされる）

        if ($error) {
            throw new \RuntimeException("cURL error: {$error}");
        }

        if ($httpCode >= 400) {
            throw new \RuntimeException("Piston API error (HTTP {$httpCode}): {$response}");
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON response: {$response}");
        }

        return $decoded;
    }

    // 以下は互換性のためのダミーメソッド（Pistonでは使用しない）

    public function createSubmission(
        string $sourceCode,
        string $stdin,
        int $timeLimitMs = 2000,
        int $memoryLimitKb = 131072
    ): string {
        throw new \RuntimeException('Async submission not supported with Piston API');
    }

    public function getSubmission(string $token): array
    {
        throw new \RuntimeException('Async submission not supported with Piston API');
    }

    public function waitForResult(string $token, int $maxWaitMs = 10000, int $pollIntervalMs = 500): array
    {
        throw new \RuntimeException('Async submission not supported with Piston API');
    }
}
