import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { getProblems } from '../lib/api';
import { useUser } from '../lib/useUser';
import { NameInputModal } from '../components/NameInputModal';

interface ProblemSummary {
  id: number;
  title: string;
}

export function Home() {
  const { userName } = useUser();
  const [problems, setProblems] = useState<ProblemSummary[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showNameModal, setShowNameModal] = useState(false);

  useEffect(() => {
    async function fetchProblems() {
      try {
        const response = await getProblems();
        setProblems(response.problems);
      } catch (e) {
        setError(e instanceof Error ? e.message : '問題の読み込みに失敗しました');
      } finally {
        setLoading(false);
      }
    }

    fetchProblems();
  }, []);

  // 初回アクセス時に名前入力を促す
  useEffect(() => {
    if (!loading && !userName) {
      setShowNameModal(true);
    }
  }, [loading, userName]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="loading-spinner" />
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <p className="text-red-400 mb-4">{error}</p>
          <button
            onClick={() => window.location.reload()}
            className="btn btn-primary"
          >
            再読み込み
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen py-12 px-4">
      <div className="max-w-4xl mx-auto">
        {/* ユーザー名表示 */}
        <div className="flex justify-end mb-4">
          <button
            onClick={() => setShowNameModal(true)}
            className="flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-800/50 hover:bg-slate-700/50 transition-colors text-slate-300"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            {userName || 'ゲスト'}
          </button>
        </div>

        {/* ヘッダー */}
        <div className="text-center mb-12">
          <h1 className="text-5xl font-bold mb-4 bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
            LittleCoder
          </h1>
          <p className="text-slate-400 text-lg">
            穴埋め式ミニ競技プログラミング
          </p>
          <p className="text-slate-500 text-sm mt-2">
            Python の穴埋めを完成させて、すべてのテストケースを通過させよう！
          </p>
          {userName && (
            <p className="text-indigo-400 mt-4">
              ようこそ、<span className="font-semibold">{userName}</span> さん！
            </p>
          )}
        </div>

        {/* 問題一覧 */}
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {problems.map((problem, index) => (
            <Link
              key={problem.id}
              to={`/problem/${problem.id}`}
              className="card p-6 block hover:border-indigo-500 transition-colors"
            >
              <div className="flex items-start justify-between mb-2">
                <span className="text-indigo-400 text-sm font-medium">
                  Problem {index + 1}
                </span>
                <span className="text-xs text-slate-500">
                  Python
                </span>
              </div>
              <h2 className="text-xl font-semibold text-white mb-2">
                {problem.title}
              </h2>
              <div className="flex items-center text-sm text-slate-400">
                <span className="flex items-center gap-1">
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  挑戦する
                </span>
              </div>
            </Link>
          ))}
        </div>
      </div>

      {/* 名前入力モーダル */}
      <NameInputModal
        isOpen={showNameModal}
        onClose={() => setShowNameModal(false)}
      />
    </div>
  );
}

export default Home;
