<?php

declare(strict_types=1);

namespace LittleCoder\Domain;

use LittleCoder\Infra\Judge0Client;

/**
 * 判定サービス
 */
class JudgeService
{
    private Judge0Client $judge0;
    private Normalizer $normalizer;

    // Judge0 ステータスコード
    private const STATUS_ACCEPTED = 3;      // 正常終了
    private const STATUS_WRONG_ANSWER = 4;  // 不正解（Judge0では使わない）
    private const STATUS_TLE = 5;           // Time Limit Exceeded
    private const STATUS_COMPILATION_ERROR = 6;
    private const STATUS_RUNTIME_ERROR_SIGSEGV = 7;
    private const STATUS_RUNTIME_ERROR_SIGXFSZ = 8;
    private const STATUS_RUNTIME_ERROR_SIGFPE = 9;
    private const STATUS_RUNTIME_ERROR_SIGABRT = 10;
    private const STATUS_RUNTIME_ERROR_NZEC = 11;
    private const STATUS_RUNTIME_ERROR_OTHER = 12;
    private const STATUS_INTERNAL_ERROR = 13;
    private const STATUS_EXEC_FORMAT_ERROR = 14;

    public function __construct()
    {
        $this->judge0 = new Judge0Client();
        $this->normalizer = new Normalizer();
    }

    /**
     * コードを判定
     * 
     * @param string $code 実行するコード
     * @param array $tests テストケース配列
     * @param int $timeLimitMs 時間制限（ミリ秒）
     * @param int $memoryLimitKb メモリ制限（KB）
     * @return array{
     *     status: string,
     *     caseResults: array,
     *     passedCount: int,
     *     totalCount: int
     * }
     */
    public function judge(
        string $code,
        array $tests,
        int $timeLimitMs = 2000,
        int $memoryLimitKb = 131072
    ): array {
        $caseResults = [];
        $allPassed = true;
        $finalStatus = 'AC';

        foreach ($tests as $index => $test) {
            try {
                $result = $this->judge0->executeSync(
                    $code,
                    $test['stdin'],
                    $timeLimitMs,
                    $memoryLimitKb
                );

                $caseResult = $this->evaluateResult($result, $test['stdout']);
                $caseResult['caseIndex'] = $index;
                $caseResults[] = $caseResult;

                if ($caseResult['status'] !== 'AC') {
                    $allPassed = false;
                    // 最初のエラーステータスを保持
                    if ($finalStatus === 'AC') {
                        $finalStatus = $caseResult['status'];
                    }
                }
            } catch (\Exception $e) {
                $caseResults[] = [
                    'caseIndex' => $index,
                    'status' => 'IE',  // Internal Error
                    'expected' => $test['stdout'],
                    'actual' => null,
                    'error' => $e->getMessage(),
                ];
                $allPassed = false;
                if ($finalStatus === 'AC') {
                    $finalStatus = 'IE';
                }
            }
        }

        $passedCount = count(array_filter($caseResults, fn($r) => $r['status'] === 'AC'));

        return [
            'status' => $finalStatus,
            'caseResults' => $caseResults,
            'passedCount' => $passedCount,
            'totalCount' => count($tests),
        ];
    }

    /**
     * 単一の実行結果を評価
     */
    private function evaluateResult(array $result, string $expectedOutput): array
    {
        $statusId = $result['status']['id'] ?? 0;

        // コンパイルエラー
        if ($statusId === self::STATUS_COMPILATION_ERROR) {
            return [
                'status' => 'CE',
                'expected' => $expectedOutput,
                'actual' => null,
                'error' => $result['compile_output'] ?? 'Compilation error',
            ];
        }

        // Time Limit Exceeded
        if ($statusId === self::STATUS_TLE) {
            return [
                'status' => 'TLE',
                'expected' => $expectedOutput,
                'actual' => null,
                'time' => $result['time'] ?? null,
            ];
        }

        // Runtime Error
        if ($statusId >= self::STATUS_RUNTIME_ERROR_SIGSEGV && $statusId <= self::STATUS_RUNTIME_ERROR_OTHER) {
            return [
                'status' => 'RE',
                'expected' => $expectedOutput,
                'actual' => null,
                'error' => $result['stderr'] ?? 'Runtime error',
            ];
        }

        // Internal/Other Error
        if ($statusId >= self::STATUS_INTERNAL_ERROR) {
            return [
                'status' => 'IE',
                'expected' => $expectedOutput,
                'actual' => null,
                'error' => $result['status']['description'] ?? 'Internal error',
            ];
        }

        // 正常終了 - 出力を比較
        if ($statusId === self::STATUS_ACCEPTED) {
            $actualOutput = $result['stdout'] ?? '';

            if ($this->normalizer->compare($expectedOutput, $actualOutput)) {
                return [
                    'status' => 'AC',
                    'expected' => $expectedOutput,
                    'actual' => $actualOutput,
                    'time' => $result['time'] ?? null,
                    'memory' => $result['memory'] ?? null,
                ];
            } else {
                return [
                    'status' => 'WA',
                    'expected' => $expectedOutput,
                    'actual' => $actualOutput,
                    'time' => $result['time'] ?? null,
                    'memory' => $result['memory'] ?? null,
                ];
            }
        }

        // 不明なステータス
        return [
            'status' => 'IE',
            'expected' => $expectedOutput,
            'actual' => null,
            'error' => 'Unknown status: ' . ($result['status']['description'] ?? 'unknown'),
        ];
    }

    /**
     * スコアを計算
     * 
     * @param string $status 判定結果
     * @param int $elapsedMs 経過時間（ミリ秒）
     * @param int $tries 試行回数
     * @param int $passedCount 通過ケース数
     * @param int $totalCount 総ケース数
     * @return int スコア
     */
    public function calculateScore(
        string $status,
        int $elapsedMs,
        int $tries,
        int $passedCount,
        int $totalCount
    ): int {
        // AC の場合のみフルスコア計算
        if ($status === 'AC') {
            // 基本スコア: 1000点
            // 経過時間ペナルティ: 最大100点（10分で100点減）
            // 試行回数ペナルティ: 1回につき20点
            $timeBonus = max(0, 100 - (int)($elapsedMs / 6000)); // 6秒で1点減
            $triesPenalty = max(0, ($tries - 1) * 20); // 初回はペナルティなし

            $score = 900 + $timeBonus - $triesPenalty;
            return max(100, min(1000, $score)); // 最低100点、最高1000点
        }

        // 部分点（オプション）: 通過ケース数に応じた点数
        if ($passedCount > 0) {
            $partialScore = (int)(($passedCount / $totalCount) * 500);
            return max(0, $partialScore - ($tries * 10));
        }

        return 0;
    }
}
