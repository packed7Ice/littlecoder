// API クライアント

import type {
  ProblemListResponse,
  ProblemDetailResponse,
  SubmissionCreateRequest,
  SubmissionCreateResponse,
  SubmissionResultResponse,
  LeaderboardResponse,
} from '../types/api';

const API_BASE = '/api';

async function fetchJson<T>(url: string, options?: RequestInit): Promise<T> {
  const response = await fetch(url, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      ...options?.headers,
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || `HTTP error: ${response.status}`);
  }

  if (!data.success) {
    throw new Error(data.error || 'Unknown error');
  }

  return data;
}

// 問題一覧を取得
export async function getProblems(): Promise<ProblemListResponse> {
  return fetchJson<ProblemListResponse>(`${API_BASE}/problems/list.php`);
}

// 問題詳細を取得
export async function getProblem(id: number): Promise<ProblemDetailResponse> {
  return fetchJson<ProblemDetailResponse>(`${API_BASE}/problems/get.php?id=${id}`);
}

// 提出を作成
export async function createSubmission(
  request: SubmissionCreateRequest
): Promise<SubmissionCreateResponse> {
  return fetchJson<SubmissionCreateResponse>(`${API_BASE}/submissions/create.php`, {
    method: 'POST',
    body: JSON.stringify(request),
  });
}

// 提出結果を取得
export async function getSubmissionResult(id: number): Promise<SubmissionResultResponse> {
  return fetchJson<SubmissionResultResponse>(`${API_BASE}/submissions/result.php?id=${id}`);
}

// ランキングを取得
export async function getLeaderboard(problemId: number): Promise<LeaderboardResponse> {
  // キャッシュを防ぐためにタイムスタンプを追加
  const t = Date.now();
  return fetchJson<LeaderboardResponse>(`${API_BASE}/leaderboard/get.php?problemId=${problemId}&_t=${t}`);
}
