import { useEffect, useState, useRef } from 'react';
import { useParams, Link } from 'react-router-dom';
import ReactMarkdown from 'react-markdown';
import { getProblem, createSubmission } from '../lib/api';
import type { Problem as ProblemType, JudgeStatus, CaseResult } from '../types/domain';
import { CodeViewer } from '../components/CodeViewer';
import { HolePicker } from '../components/HolePicker';
import { ResultPanel } from '../components/ResultPanel';
import { Leaderboard } from '../components/Leaderboard';
import { formatTime } from '../lib/time';
import { useUser } from '../lib/useUser';
import { difficultyLabels } from '../lib/constants';

interface SubmissionResult {
  status: JudgeStatus;
  score: number;
  elapsedMs: number;
  passedCount: number;
  totalCount: number;
  caseResults: CaseResult[];
  tries: number;
}

export function Problem() {
  const { id } = useParams<{ id: string }>();
  const problemId = parseInt(id || '0', 10);
  const { userName } = useUser();

  const [problem, setProblem] = useState<ProblemType | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const [selectedAnswers, setSelectedAnswers] = useState<number[]>([]);
  const [startedAt, setStartedAt] = useState<number | null>(null);
  const [elapsedSeconds, setElapsedSeconds] = useState(0);

  const [submitting, setSubmitting] = useState(false);
  const [result, setResult] = useState<SubmissionResult | null>(null);
  const [submitError, setSubmitError] = useState<string | null>(null);

  const [leaderboardRefresh, setLeaderboardRefresh] = useState(0);

  const timerRef = useRef<number | null>(null);

  // 問題データを取得
  useEffect(() => {
    async function fetchProblem() {
      try {
        const response = await getProblem(problemId);
        setProblem(response.problem);
        // 穴の数だけ初期化（-1 = 未選択）
        setSelectedAnswers(new Array(response.problem.holes.length).fill(-1));
      } catch (e) {
        setError(e instanceof Error ? e.message : '問題の読み込みに失敗しました');
      } finally {
        setLoading(false);
      }
    }

    if (problemId > 0) {
      fetchProblem();
    }
  }, [problemId]);

  // タイマー開始（最初の穴を選択した時）
  useEffect(() => {
    if (selectedAnswers.some((a) => a !== -1) && startedAt === null) {
      setStartedAt(Date.now());
    }
  }, [selectedAnswers, startedAt]);

  // タイマー更新
  useEffect(() => {
    if (startedAt !== null && !result) {
      timerRef.current = window.setInterval(() => {
        setElapsedSeconds(Math.floor((Date.now() - startedAt) / 1000));
      }, 1000);

      return () => {
        if (timerRef.current) {
          clearInterval(timerRef.current);
        }
      };
    }
  }, [startedAt, result]);

  const handleAnswerChange = (holeIndex: number, optionId: number) => {
    setSelectedAnswers((prev) => {
      const newAnswers = [...prev];
      newAnswers[holeIndex] = optionId;
      return newAnswers;
    });
  };

  const canSubmit = selectedAnswers.every((a) => a !== -1) && !submitting;

  const handleSubmit = async () => {
    if (!canSubmit || !problem) return;

    setSubmitting(true);
    setSubmitError(null);

    try {
      const submitData = {
        problemId,
        answers: selectedAnswers,
        startedAt: startedAt || Date.now(),
        userName: userName || undefined,
      };
      console.log('Submitting:', submitData);
      const response = await createSubmission(submitData);

      setResult({
        status: response.status,
        score: response.score,
        elapsedMs: response.elapsedMs,
        passedCount: response.passedCount,
        totalCount: response.totalCount,
        caseResults: response.caseResults,
        tries: response.tries,
      });

      // ランキングを更新
      setLeaderboardRefresh((prev) => prev + 1);
    } catch (e) {
      setSubmitError(e instanceof Error ? e.message : '提出に失敗しました');
    } finally {
      setSubmitting(false);
    }
  };

  const handleReset = () => {
    setSelectedAnswers(new Array(problem?.holes.length || 0).fill(-1));
    setStartedAt(null);
    setElapsedSeconds(0);
    setResult(null);
    setSubmitError(null);
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="loading-spinner" />
      </div>
    );
  }

  if (error || !problem) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <p className="text-red-400 mb-4">{error || '問題が見つかりません'}</p>
          <Link to="/" className="btn btn-primary">
            ホームに戻る
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen py-8 px-4">
      <div className="max-w-6xl mx-auto">
        {/* ヘッダー */}
        <div className="flex items-center justify-between mb-8">
          <div className="flex items-center gap-4">
            <Link to="/" className="text-slate-400 hover:text-white transition-colors">
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </Link>
            <div>
              <div className="flex items-center gap-3">
                <h1 className="text-2xl font-bold">{problem.title}</h1>
                <span className={`text-xs px-2 py-0.5 rounded ${difficultyLabels[problem.difficulty || 1].bgColor} ${difficultyLabels[problem.difficulty || 1].color}`}>
                  {difficultyLabels[problem.difficulty || 1].text}
                </span>
              </div>
              <p className="text-sm text-slate-400">
                テストケース: {problem.testCount}個 / 制限時間: {problem.timeLimitMs}ms
              </p>
            </div>
          </div>

          {/* タイマー */}
          <div className="glass rounded-lg px-4 py-2">
            <div className="text-2xl font-mono font-bold text-indigo-400">
              {formatTime(elapsedSeconds * 1000)}
            </div>
          </div>
        </div>

        <div className="grid gap-8 lg:grid-cols-3">
          {/* 左側: 問題文とコード */}
          <div className="lg:col-span-2 space-y-6">
            {/* 問題文 */}
            <div className="card p-6">
              <h2 className="text-lg font-semibold mb-4 text-slate-200">問題文</h2>
              <div className="prose prose-invert prose-sm max-w-none">
                <ReactMarkdown>{problem.statementMd}</ReactMarkdown>
              </div>
            </div>

            {/* コード表示 */}
            <div className="card p-6">
              <h2 className="text-lg font-semibold mb-4 text-slate-200">コード</h2>
              <CodeViewer
                code={problem.templateCode}
                holes={problem.holes}
                selectedAnswers={selectedAnswers}
              />
            </div>

            {/* 穴埋め選択 */}
            <div className="card p-6">
              <HolePicker
                holes={problem.holes}
                selectedAnswers={selectedAnswers}
                onAnswerChange={handleAnswerChange}
                disabled={submitting || !!result}
              />
            </div>

            {/* 提出ボタン */}
            <div className="flex gap-4">
              <button
                onClick={handleSubmit}
                disabled={!canSubmit || !!result}
                className="btn btn-primary flex-1 flex items-center justify-center gap-2"
              >
                {submitting ? (
                  <>
                    <div className="loading-spinner" />
                    判定中...
                  </>
                ) : (
                  <>
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    提出する
                  </>
                )}
              </button>

              {result && (
                <button
                  onClick={handleReset}
                  className="btn bg-slate-700 hover:bg-slate-600 text-white"
                >
                  再挑戦
                </button>
              )}
            </div>

            {/* エラー表示 */}
            {submitError && (
              <div className="p-4 rounded-lg bg-red-900/30 border border-red-500 text-red-400">
                {submitError}
              </div>
            )}

            {/* 結果表示 */}
            {result && (
              <ResultPanel
                status={result.status}
                score={result.score}
                elapsedMs={result.elapsedMs}
                passedCount={result.passedCount}
                totalCount={result.totalCount}
                caseResults={result.caseResults}
                tries={result.tries}
              />
            )}
          </div>

          {/* 右側: ランキング */}
          <div className="lg:col-span-1">
            <div className="sticky top-8">
              <Leaderboard problemId={problemId} refreshTrigger={leaderboardRefresh} />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Problem;
