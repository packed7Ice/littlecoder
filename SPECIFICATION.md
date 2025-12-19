# LittleCoder 仕様書

## 1. 概要
**LittleCoder** は、ブラウザで手軽に遊べる穴埋め式ミニ競技プログラミングゲームです。
ユーザーは Python コードの空欄を適切な選択肢で埋めることでアルゴリズムを完成させ、用意されたテストケースを通過させることを目指します。

## 2. 機能一覧

### 2.1 ユーザー機能
- **問題一覧表示**: 登録されている問題の一覧を閲覧、難易度順やID順でソート、難易度でフィルタリング可能。
- **問題詳細・解答**: 問題文と穴埋め式のコードエディタを表示。プルダウン形式で穴埋めを選択し、提出可能。
- **コード実行・判定**: 提出されたコードをサーバーサイド（Piston API）で実行し、判定結果（AC/WA/TLE等）を表示。
- **ランキング**: 各問題ごとのハイスコアランキングを表示。

### 2.2 システム機能
- **自動採点**: Python 3.10 環境でコードを実行し、標準入出力を用いて正誤判定を行う。
- **スコアリング**: 経過時間と試行回数に基づいてスコアを算出。

## 3. システムアーキテクチャ

### 構成概要
| レイヤー | 技術スタック | 役割 |
|---|---|---|
| Frontend | React, TypeScript, Vite, Tailwind CSS | UI/UX, API通信 |
| Backend | PHP 8.x | API提供, DB操作, Piston連携 |
| Database | SQLite | 問題データ, 提出履歴, ランキング保存 |
| Execution | Piston API | コードの安全な実行 (Sandbox) |

## 4. データモデル (Database Schema)

### `problems` (問題)
| カラム名 | 型 | 説明 |
|---|---|---|
| `id` | INTEGER (PK) | 問題ID |
| `title` | TEXT | 問題タイトル |
| `statement_md` | TEXT | 問題文（Markdown） |
| `template_code` | TEXT | テンプレートコード |
| `holes_json` | TEXT | 穴埋め情報のJSON定義 |
| `tests_json` | TEXT | テストケースのJSON定義 |
| `time_limit_ms` | INTEGER | 実行時間制限 (デフォルト2000ms) |
| `memory_limit_kb` | INTEGER | メモリ制限 (デフォルト131072KB) |

### `submissions` (提出)
| カラム名 | 型 | 説明 |
|---|---|---|
| `id` | INTEGER (PK) | 提出ID |
| `problem_id` | INTEGER (FK) | 問題ID |
| `session_id` | TEXT | ユーザー識別用セッションID |
| `answers_json` | TEXT | ユーザーの回答配列JSON |
| `status` | TEXT | 判定結果 (AC, WA, PENDING 等) |
| `score` | INTEGER | スコア |
| `elapsed_ms` | INTEGER | 解答にかかった時間(ms) |

## 5. API仕様

### 基底URL
`/api`

### エンドポイント一覧

#### 1. 問題一覧取得
- **GET** `/problems/list.php`
- **Response**: `ProblemListResponse`
    - `problems`: 問題の概要リスト (ID, タイトル, 難易度)

#### 2. 問題詳細取得
- **GET** `/problems/get.php?id={id}`
- **Response**: `ProblemDetailResponse`
    - `problem`: 詳細情報 (Markdown, コード, 穴埋め選択肢, テスト数など)
    - ※セキュリティのため、テストごとの入出力はクライアントには返さない

#### 3. 提出作成・実行
- **POST** `/submissions/create.php`
- **Request**: `SubmissionCreateRequest`
    - `problemId`: 問題ID
    - `answers`: 選択した穴埋めIDの配列
    - `startedAt`: 開始時刻タイムスタンプ
    - `userName`: プレイヤー名
- **Response**: `SubmissionCreateResponse`
    - `status`: 判定結果 (AC/WAなど)
    - `score`: 獲得スコア
    - `caseResults`: テストケースごとの詳細結果

#### 4. 提出結果取得
- **GET** `/submissions/result.php?id={id}`
- **Response**: `SubmissionResultResponse`
    - 過去の提出の詳細を取得

#### 5. ランキング取得
- **GET** `/leaderboard/get.php?problemId={id}`
- **Response**: `LeaderboardResponse`
    - `leaderboard`: ランキングエントリのリスト (順位, 名前, スコア, タイム)

## 6. フロントエンド仕様

### 主要画面コンポーネント
- `Home.tsx`: 問題カードのグリッド表示、検索・フィルタリング、ユーザー名入力
- `ProblemDetail.tsx`: 問題文表示、コードエディタ風UI、穴埋めプルダウン、実行ボタン
- `ResultModal.tsx`: 判定結果のアニメーション表示、スコア詳細

### 使用ライブラリ
- `react-markdown`: 問題文のレンダリング
- `react-router-dom`: ルーティング
- `tailwindcss`: スタイリング（v4系, `bg-linear-to-r` 等を使用）
