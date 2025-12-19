// 時間関連のユーティリティ

export function formatTime(ms: number): string {
  const seconds = Math.floor(ms / 1000);
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;

  if (minutes > 0) {
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
  }

  return `${seconds}s`;
}

export function formatElapsed(ms: number): string {
  if (ms < 1000) {
    return `${ms}ms`;
  }

  const seconds = (ms / 1000).toFixed(1);
  return `${seconds}s`;
}

export function formatTimeAgo(dateString: string): string {
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();

  const minutes = Math.floor(diffMs / (1000 * 60));
  const hours = Math.floor(diffMs / (1000 * 60 * 60));
  const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));

  if (days > 0) {
    return `${days}日前`;
  }
  if (hours > 0) {
    return `${hours}時間前`;
  }
  if (minutes > 0) {
    return `${minutes}分前`;
  }

  return 'たった今';
}
