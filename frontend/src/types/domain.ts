// ドメイン型定義

export interface Problem {
  id: number;
  title: string;
  statementMd: string;
  templateCode: string;
  displayCode: string;
  holes: Hole[];
  testCount: number;
  timeLimitMs: number;
  difficulty: number;
}

export interface Hole {
  id: string;
  label: string;
  options: HoleOption[];
}

export interface HoleOption {
  id: number;
  code: string;
}

export interface Submission {
  id: number;
  problemId: number;
  status: JudgeStatus;
  score: number;
  elapsedMs: number;
  caseResults: CaseResult[];
  tries: number;
}

export interface CaseResult {
  caseIndex: number;
  status: JudgeStatus;
}

export type JudgeStatus = 'AC' | 'WA' | 'TLE' | 'RE' | 'CE' | 'IE' | 'PENDING';

export interface LeaderboardEntry {
  rank: number;
  sessionId: string;
  bestScore: number;
  bestElapsedMs: number;
  tries: number;
}

// ステータス表示用のヘルパー
export const statusLabels: Record<JudgeStatus, string> = {
  AC: 'Accepted',
  WA: 'Wrong Answer',
  TLE: 'Time Limit Exceeded',
  RE: 'Runtime Error',
  CE: 'Compilation Error',
  IE: 'Internal Error',
  PENDING: 'Pending',
};

export const statusColors: Record<JudgeStatus, string> = {
  AC: 'badge-ac',
  WA: 'badge-wa',
  TLE: 'badge-tle',
  RE: 'badge-re',
  CE: 'badge-ce',
  IE: 'badge-ce',
  PENDING: 'badge-pending',
};
