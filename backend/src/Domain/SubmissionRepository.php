<?php

declare(strict_types=1);

namespace LittleCoder\Domain;

use LittleCoder\Infra\Db;
use PDO;

/**
 * 提出リポジトリ
 */
class SubmissionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * 提出を作成
     * 
     * @param int $problemId 問題ID
     * @param string $sessionId セッションID
     * @param array $answers 穴埋め回答の配列
     * @param string|null $userName ユーザー名
     * @return int 作成された提出ID
     */
    public function create(int $problemId, string $sessionId, array $answers, ?string $userName = null): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO submissions (problem_id, session_id, answers_json, status, user_name)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $problemId,
            $sessionId,
            json_encode($answers),
            'PENDING',
            $userName
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * 提出を更新
     */
    public function update(int $id, string $status, int $score, int $elapsedMs): void
    {
        $stmt = $this->db->prepare('
            UPDATE submissions
            SET status = ?, score = ?, elapsed_ms = ?
            WHERE id = ?
        ');
        $stmt->execute([$status, $score, $elapsedMs, $id]);
    }

    /**
     * 提出を取得
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('
            SELECT id, problem_id, session_id, answers_json, status, score, elapsed_ms, created_at
            FROM submissions
            WHERE id = ?
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return [
            'id' => (int)$row['id'],
            'problemId' => (int)$row['problem_id'],
            'sessionId' => $row['session_id'],
            'answers' => json_decode($row['answers_json'], true),
            'status' => $row['status'],
            'score' => (int)$row['score'],
            'elapsedMs' => (int)$row['elapsed_ms'],
            'createdAt' => $row['created_at'],
        ];
    }

    /**
     * セッションの問題に対する提出回数を取得
     */
    public function countBySessionAndProblem(string $sessionId, int $problemId): int
    {
        $stmt = $this->db->prepare('
            SELECT COUNT(*) as count
            FROM submissions
            WHERE session_id = ? AND problem_id = ?
        ');
        $stmt->execute([$sessionId, $problemId]);
        $row = $stmt->fetch();

        return (int)$row['count'];
    }

    /**
     * ケース別結果を保存
     */
    public function saveCaseResults(int $submissionId, array $caseResults): void
    {
        $stmt = $this->db->prepare('
            INSERT INTO submission_cases (submission_id, case_index, status, expected, actual)
            VALUES (?, ?, ?, ?, ?)
        ');

        foreach ($caseResults as $index => $result) {
            $stmt->execute([
                $submissionId,
                $index,
                $result['status'],
                $result['expected'] ?? null,
                $result['actual'] ?? null,
            ]);
        }
    }

    /**
     * ケース別結果を取得
     */
    public function getCaseResults(int $submissionId): array
    {
        $stmt = $this->db->prepare('
            SELECT case_index, status, expected, actual
            FROM submission_cases
            WHERE submission_id = ?
            ORDER BY case_index
        ');
        $stmt->execute([$submissionId]);

        return $stmt->fetchAll();
    }

    /**
     * 問題別ランキングを取得
     */
    public function getLeaderboard(int $problemId, int $limit = 10): array
    {
        $stmt = $this->db->prepare('
            SELECT 
                session_id,
                MAX(score) as best_score,
                MIN(elapsed_ms) as best_elapsed_ms,
                COUNT(*) as tries,
                MIN(created_at) as first_ac_at
            FROM submissions
            WHERE problem_id = ? AND status = ?
            GROUP BY session_id
            ORDER BY best_score DESC, best_elapsed_ms ASC
            LIMIT ?
        ');
        $stmt->execute([$problemId, 'AC', $limit]);

        return array_map(function ($row, $index) {
            return [
                'rank' => $index + 1,
                'sessionId' => substr($row['session_id'], 0, 8) . '...',
                'bestScore' => (int)$row['best_score'],
                'bestElapsedMs' => (int)$row['best_elapsed_ms'],
                'tries' => (int)$row['tries'],
            ];
        }, $stmt->fetchAll(), array_keys($stmt->fetchAll() ?: []));
    }

    /**
     * 問題別ランキングを取得（修正版）
     */
    public function getLeaderboardFixed(int $problemId, int $limit = 10): array
    {
        $stmt = $this->db->prepare('
            SELECT 
                session_id,
                user_name,
                MAX(score) as best_score,
                MIN(elapsed_ms) as best_elapsed_ms,
                COUNT(*) as tries
            FROM submissions
            WHERE problem_id = ? AND status = ?
            GROUP BY session_id
            ORDER BY best_score DESC, best_elapsed_ms ASC
            LIMIT ?
        ');
        $stmt->execute([$problemId, 'AC', $limit]);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $index => $row) {
            // ユーザー名があればそれを表示、なければセッションIDの一部
            $displayName = $row['user_name']
                ? $row['user_name']
                : substr($row['session_id'], 0, 8) . '...';

            $result[] = [
                'rank' => $index + 1,
                'sessionId' => $displayName,
                'bestScore' => (int)$row['best_score'],
                'bestElapsedMs' => (int)$row['best_elapsed_ms'],
                'tries' => (int)$row['tries'],
            ];
        }
        return $result;
    }
}
