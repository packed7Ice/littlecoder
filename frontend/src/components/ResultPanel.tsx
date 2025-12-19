import type { JudgeStatus, CaseResult } from '../types/domain';
import { statusLabels, statusColors } from '../types/domain';
import { formatElapsed } from '../lib/time';

interface ResultPanelProps {
  status: JudgeStatus;
  score: number;
  elapsedMs: number;
  passedCount: number;
  totalCount: number;
  caseResults: CaseResult[];
  tries: number;
}

export function ResultPanel({
  status,
  score,
  elapsedMs,
  passedCount,
  totalCount,
  caseResults,
  tries,
}: ResultPanelProps) {
  const isAccepted = status === 'AC';

  return (
    <div className={`rounded-xl p-6 ${isAccepted ? 'bg-emerald-900/30 border-emerald-500' : 'bg-slate-800/50 border-slate-700'} border`}>
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center gap-4">
          <span className={`badge ${statusColors[status]}`}>
            {statusLabels[status]}
          </span>
          <span className="text-2xl font-bold">
            {score} <span className="text-sm font-normal text-slate-400">点</span>
          </span>
        </div>
        <div className="text-right text-sm text-slate-400">
          <div>提出回数: {tries}</div>
          <div>経過時間: {formatElapsed(elapsedMs)}</div>
        </div>
      </div>

      <div className="mb-4">
        <div className="text-sm text-slate-400 mb-2">
          テストケース: {passedCount} / {totalCount} 通過
        </div>
        <div className="w-full bg-slate-700 rounded-full h-2">
          <div
            className={`h-2 rounded-full transition-all ${isAccepted ? 'bg-emerald-500' : 'bg-indigo-500'}`}
            style={{ width: `${(passedCount / totalCount) * 100}%` }}
          />
        </div>
      </div>

      <div className="grid grid-cols-5 sm:grid-cols-10 gap-2">
        {caseResults.map((result) => (
          <div
            key={result.caseIndex}
            className={`
              w-8 h-8 rounded flex items-center justify-center text-xs font-bold
              ${result.status === 'AC' 
                ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/50' 
                : 'bg-red-500/20 text-red-400 border border-red-500/50'
              }
            `}
            title={`Case ${result.caseIndex + 1}: ${result.status}`}
          >
            {result.caseIndex + 1}
          </div>
        ))}
      </div>

      {isAccepted && (
        <div className="mt-4 text-center">
          <span className="text-4xl">🎉</span>
          <p className="text-emerald-400 font-semibold mt-2">おめでとうございます！</p>
        </div>
      )}
    </div>
  );
}

export default ResultPanel;
