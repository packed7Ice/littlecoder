<?php

declare(strict_types=1);

namespace LittleCoder\Infra;

/**
 * セッションベースのレート制限
 */
class RateLimit
{
    private int $maxRequests;
    private int $windowSeconds;

    public function __construct(?int $maxRequests = null, ?int $windowSeconds = null)
    {
        $this->maxRequests = $maxRequests ?? (int)(Env::get('RATE_LIMIT_MAX_REQUESTS', '5'));
        $this->windowSeconds = $windowSeconds ?? (int)(Env::get('RATE_LIMIT_WINDOW_SECONDS', '60'));
    }

    /**
     * リクエストが許可されるかチェック
     * 
     * @return array{allowed: bool, remaining: int, resetAt: int}
     */
    public function check(string $sessionId): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $now = time();
        $key = "rate_limit_{$sessionId}";

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 0,
                'window_start' => $now,
            ];
        }

        $data = &$_SESSION[$key];

        // ウィンドウがリセットされた場合
        if ($now - $data['window_start'] >= $this->windowSeconds) {
            $data['count'] = 0;
            $data['window_start'] = $now;
        }

        $remaining = max(0, $this->maxRequests - $data['count']);
        $resetAt = $data['window_start'] + $this->windowSeconds;

        return [
            'allowed' => $remaining > 0,
            'remaining' => $remaining,
            'resetAt' => $resetAt,
        ];
    }

    /**
     * リクエストをカウント
     */
    public function increment(string $sessionId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $key = "rate_limit_{$sessionId}";

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 0,
                'window_start' => time(),
            ];
        }

        $_SESSION[$key]['count']++;
    }

    /**
     * 残りリクエスト数を取得
     */
    public function getRemaining(string $sessionId): int
    {
        return $this->check($sessionId)['remaining'];
    }
}
