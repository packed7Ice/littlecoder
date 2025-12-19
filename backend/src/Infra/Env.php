<?php

declare(strict_types=1);

namespace LittleCoder\Infra;

/**
 * 環境変数管理クラス
 */
class Env
{
    private static ?array $vars = null;

    /**
     * .envファイルを読み込む
     */
    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new \RuntimeException(".env file not found: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        self::$vars = [];

        foreach ($lines as $line) {
            // コメント行をスキップ
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // KEY=VALUE 形式をパース
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // クォートを除去
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                self::$vars[$key] = $value;
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }

    /**
     * 環境変数を取得
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        if (self::$vars === null) {
            // 自動読み込み
            $envPath = dirname(__DIR__, 2) . '/.env';
            if (file_exists($envPath)) {
                self::load($envPath);
            } else {
                self::$vars = [];
            }
        }

        return self::$vars[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }

    /**
     * 必須環境変数を取得（未設定の場合は例外）
     */
    public static function require(string $key): string
    {
        $value = self::get($key);
        if ($value === null || $value === '') {
            throw new \RuntimeException("Required environment variable not set: {$key}");
        }
        return $value;
    }
}
