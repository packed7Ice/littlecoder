import { useEffect, useState, useMemo } from 'react';
import { Link } from 'react-router-dom';
import { getProblems } from '../lib/api';
import { useUser } from '../lib/useUser';
import { NameInputModal } from '../components/NameInputModal';
import { difficultyLabels } from '../lib/constants';

interface ProblemSummary {
  id: number;
  title: string;
  difficulty: number;
}

type SortType = 'id' | 'difficulty-asc' | 'difficulty-desc';

export function Home() {
  const { userName } = useUser();
  const [problems, setProblems] = useState<ProblemSummary[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showNameModal, setShowNameModal] = useState(false);
  const [sortType, setSortType] = useState<SortType>('id');
  const [filterDifficulty, setFilterDifficulty] = useState<number | null>(null);

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

  // ソートとフィルタリング
  const sortedProblems = useMemo(() => {
    let filtered = problems;
    
    // フィルタリング
    if (filterDifficulty !== null) {
      filtered = problems.filter(p => p.difficulty === filterDifficulty);
    }
    
    // ソート
    return [...filtered].sort((a, b) => {
      switch (sortType) {
        case 'difficulty-asc':
          return a.difficulty - b.difficulty || a.id - b.id;
        case 'difficulty-desc':
          return b.difficulty - a.difficulty || a.id - b.id;
        default:
          return a.id - b.id;
      }
    });
  }, [problems, sortType, filterDifficulty]);

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
            className="flex items-center gap-3 px-5 py-3 rounded-xl bg-gradient-to-r from-indigo-600/20 to-purple-600/20 border border-indigo-500/30 hover:border-indigo-400 transition-all text-white group"
          >
            <svg className="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <div className="text-left">
              <div className="text-sm text-slate-400">プレイヤー名</div>
              <div className="font-semibold text-lg">{userName || 'ゲスト'}</div>
            </div>
            <svg className="w-5 h-5 text-indigo-400 group-hover:text-indigo-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
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

        {/* ソート・フィルター */}
        <div className="flex flex-wrap gap-4 mb-6 items-center justify-between">
          <div className="flex gap-2">
            <span className="text-slate-400 text-sm self-center">難易度:</span>
            <button
              onClick={() => setFilterDifficulty(null)}
              className={`px-3 py-1 rounded-lg text-sm transition-colors ${
                filterDifficulty === null
                  ? 'bg-indigo-600 text-white'
                  : 'bg-slate-700 text-slate-300 hover:bg-slate-600'
              }`}
            >
              すべて
            </button>
            {[1, 2, 3].map(d => (
              <button
                key={d}
                onClick={() => setFilterDifficulty(filterDifficulty === d ? null : d)}
                className={`px-3 py-1 rounded-lg text-sm transition-colors ${
                  filterDifficulty === d
                    ? `${difficultyLabels[d].bgColor} ${difficultyLabels[d].color} border border-current`
                    : 'bg-slate-700 text-slate-300 hover:bg-slate-600'
                }`}
              >
                {difficultyLabels[d].text}
              </button>
            ))}
          </div>
          
          <div className="flex gap-2 items-center">
            <span className="text-slate-400 text-sm">並び順:</span>
            <select
              value={sortType}
              onChange={(e) => setSortType(e.target.value as SortType)}
              className="bg-slate-700 text-white px-3 py-1 rounded-lg text-sm border border-slate-600 focus:outline-none focus:border-indigo-500"
            >
              <option value="id">問題番号</option>
              <option value="difficulty-asc">難易度 (易→難)</option>
              <option value="difficulty-desc">難易度 (難→易)</option>
            </select>
          </div>
        </div>

        {/* 問題数表示 */}
        <div className="text-slate-400 text-sm mb-4">
          {sortedProblems.length} 問
        </div>

        {/* 問題一覧 */}
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {sortedProblems.map((problem) => {
            const diff = difficultyLabels[problem.difficulty] || difficultyLabels[1];
            return (
              <Link
                key={problem.id}
                to={`/problem/${problem.id}`}
                className="card p-6 block hover:border-indigo-500 transition-colors"
              >
                <div className="flex items-start justify-between mb-2">
                  <span className="text-indigo-400 text-sm font-medium">
                    #{problem.id}
                  </span>
                  <span className={`text-xs px-2 py-0.5 rounded ${diff.bgColor} ${diff.color}`}>
                    {diff.text}
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
            );
          })}
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
