<?php

declare(strict_types=1);

namespace LittleCoder\Infra;

use PDO;
use PDOException;

/**
 * SQLite データベース接続管理
 */
class Db
{
    private static ?PDO $instance = null;

    /**
     * PDOインスタンスを取得（シングルトン）
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dbPath = Env::get('DB_PATH', dirname(__DIR__, 2) . '/data/littlecoder.sqlite');

            // 相対パスを絶対パスに変換
            if (!str_starts_with($dbPath, '/') && !preg_match('/^[A-Z]:/i', $dbPath)) {
                $dbPath = dirname(__DIR__, 2) . '/' . $dbPath;
            }

            // データディレクトリが存在しない場合は作成
            $dbDir = dirname($dbPath);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }

            try {
                self::$instance = new PDO(
                    "sqlite:{$dbPath}",
                    null,
                    null,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );

                // WALモードを有効化（パフォーマンス向上）
                self::$instance->exec('PRAGMA journal_mode=WAL');
                self::$instance->exec('PRAGMA foreign_keys=ON');
            } catch (PDOException $e) {
                throw new \RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    /**
     * スキーマを初期化
     */
    public static function initSchema(): void
    {
        $schemaPath = dirname(__DIR__, 2) . '/sql/schema.sql';
        if (!file_exists($schemaPath)) {
            throw new \RuntimeException("Schema file not found: {$schemaPath}");
        }

        $sql = file_get_contents($schemaPath);
        $pdo = self::getInstance();
        $pdo->exec($sql);
    }

    /**
     * 接続をリセット（テスト用）
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
