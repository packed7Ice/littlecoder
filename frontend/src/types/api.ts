// API レスポンス型定義

export interface ApiResponse {
  success: boolean;
  error?: string;
}

export interface ProblemListResponse extends ApiResponse {
  problems: {
    id: number;
    title: string;
    difficulty: number;
  }[];
}

export interface ProblemDetailResponse extends ApiResponse {
  problem: {
    id: number;
    title: string;
    statementMd: string;
    templateCode: string;
    displayCode: string;
    holes: Hole[];
    testCount: number;
    timeLimitMs: number;
    difficulty: number;
  };
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

export interface SubmissionCreateRequest {
  problemId: number;
  answers: number[];
  startedAt: number;
  userName?: string;
}

export interface SubmissionCreateResponse extends ApiResponse {
  submissionId: number;
  status: JudgeStatus;
  score: number;
  elapsedMs: number;
  passedCount: number;
  totalCount: number;
  caseResults: CaseResult[];
  tries: number;
  remaining: number;
}

export interface CaseResult {
  caseIndex: number;
  status: JudgeStatus;
}

export type JudgeStatus = 'AC' | 'WA' | 'TLE' | 'RE' | 'CE' | 'IE' | 'PENDING';

export interface SubmissionResultResponse extends ApiResponse {
  submission: {
    id: number;
    problemId: number;
    status: JudgeStatus;
    score: number;
    elapsedMs: number;
    createdAt: string;
    caseResults: CaseResult[];
  };
}

export interface LeaderboardResponse extends ApiResponse {
  problemId: number;
  leaderboard: LeaderboardEntry[];
}

export interface LeaderboardEntry {
  rank: number;
  sessionId: string;
  bestScore: number;
  bestElapsedMs: number;
  tries: number;
}
