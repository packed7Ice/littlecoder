<?php

declare(strict_types=1);

namespace LittleCoder\Domain;

use LittleCoder\Infra\Db;
use PDO;

/**
 * 問題リポジトリ
 */
class ProblemRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * 問題一覧を取得
     * 
     * @return array{id: int, title: string, difficulty: int}[]
     */
    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT id, title, COALESCE(difficulty, 1) as difficulty FROM problems ORDER BY id');
        $rows = $stmt->fetchAll();

        return array_map(function ($row) {
            return [
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'difficulty' => (int)$row['difficulty'],
            ];
        }, $rows);
    }

    /**
     * 問題詳細を取得
     * 
     * @return array{
     *     id: int,
     *     title: string,
     *     statement_md: string,
     *     template_code: string,
     *     holes: array,
     *     tests: array,
     *     time_limit_ms: int,
     *     memory_limit_kb: int
     * }|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('
            SELECT id, title, statement_md, template_code, holes_json, tests_json, time_limit_ms, memory_limit_kb, COALESCE(difficulty, 1) as difficulty
            FROM problems
            WHERE id = ?
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return [
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'statement_md' => $row['statement_md'],
            'template_code' => $row['template_code'],
            'holes' => json_decode($row['holes_json'], true),
            'tests' => json_decode($row['tests_json'], true),
            'time_limit_ms' => (int)$row['time_limit_ms'],
            'memory_limit_kb' => (int)$row['memory_limit_kb'],
            'difficulty' => (int)$row['difficulty'],
        ];
    }

    /**
     * 問題の内部データを取得（テストケース含む、判定用）
     */
    public function findForJudge(int $id): ?array
    {
        return $this->findById($id);
    }

    /**
     * 問題のテンプレートと穴埋め情報のみ取得
     */
    public function findTemplateData(int $id): ?array
    {
        $stmt = $this->db->prepare('
            SELECT template_code, holes_json
            FROM problems
            WHERE id = ?
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return [
            'template_code' => $row['template_code'],
            'holes' => json_decode($row['holes_json'], true),
        ];
    }
}
