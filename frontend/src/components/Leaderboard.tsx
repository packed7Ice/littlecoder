import { useEffect, useState } from 'react';
import { getLeaderboard } from '../lib/api';
import type { LeaderboardEntry } from '../types/domain';
import { formatElapsed } from '../lib/time';

interface LeaderboardProps {
  problemId: number;
  refreshTrigger?: number;
}

export function Leaderboard({ problemId, refreshTrigger }: LeaderboardProps) {
  const [entries, setEntries] = useState<LeaderboardEntry[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function fetchLeaderboard() {
      try {
        setLoading(true);
        const response = await getLeaderboard(problemId);
        setEntries(response.leaderboard);
        setError(null);
      } catch (e) {
        setError(e instanceof Error ? e.message : '読み込みに失敗しました');
      } finally {
        setLoading(false);
      }
    }

    fetchLeaderboard();
  }, [problemId, refreshTrigger]);

  if (loading) {
    return (
      <div className="p-6 rounded-xl bg-slate-800/50 border border-slate-700">
        <h3 className="text-lg font-semibold mb-4 text-slate-200">🏆 ランキング</h3>
        <div className="flex justify-center py-4">
          <div className="loading-spinner" />
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="p-6 rounded-xl bg-slate-800/50 border border-slate-700">
        <h3 className="text-lg font-semibold mb-4 text-slate-200">🏆 ランキング</h3>
        <p className="text-red-400 text-sm">{error}</p>
      </div>
    );
  }

  if (entries.length === 0) {
    return (
      <div className="p-6 rounded-xl bg-slate-800/50 border border-slate-700">
        <h3 className="text-lg font-semibold mb-4 text-slate-200">🏆 ランキング</h3>
        <p className="text-slate-400 text-sm text-center py-4">
          まだACした人がいません。最初のACを目指しましょう！
        </p>
      </div>
    );
  }

  const getRankIcon = (rank: number) => {
    switch (rank) {
      case 1: return '🥇';
      case 2: return '🥈';
      case 3: return '🥉';
      default: return `#${rank}`;
    }
  };

  return (
    <div className="p-6 rounded-xl bg-slate-800/50 border border-slate-700">
      <h3 className="text-lg font-semibold mb-4 text-slate-200">🏆 ランキング</h3>
      <div className="space-y-2">
        {entries.map((entry) => (
          <div
            key={entry.sessionId}
            className={`
              flex items-center justify-between p-3 rounded-lg
              ${entry.rank <= 3 ? 'bg-indigo-900/30 border border-indigo-500/30' : 'bg-slate-700/30'}
            `}
          >
            <div className="flex items-center gap-3">
              <span className="w-8 text-center font-bold">
                {getRankIcon(entry.rank)}
              </span>
              <span className="text-slate-300 font-mono text-sm">
                {entry.sessionId}
              </span>
            </div>
            <div className="flex items-center gap-4 text-sm">
              <span className="text-indigo-400 font-bold">
                {entry.bestScore}点
              </span>
              <span className="text-slate-400">
                {formatElapsed(entry.bestElapsedMs)}
              </span>
              <span className="text-slate-500">
                {entry.tries}回
              </span>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

export default Leaderboard;
