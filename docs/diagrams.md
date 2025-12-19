# システム図 (System Diagrams)

## ページ遷移図

```mermaid
stateDiagram-v2
    [*] --> ProblemList: アクセス (/)

    state "問題一覧 (Home)" as ProblemList {
        [*] --> ListDisplay: 一覧表示
        ListDisplay --> NameInputModal: ユーザー名未設定時
        NameInputModal --> ListDisplay: 設定完了
    }

    ListDisplay --> Filling: 問題を選択 (URL /problem/id)

    state "問題詳細 (Problem)" as ProblemDetail {
        state "コード閲覧・穴埋め選択" as Filling
        state "提出中 (Submitting)" as Submitting
        state "判定結果表示 (ResultPanel)" as Result
        state "ランキング (Leaderboard)" as Leaderboard

        Filling --> ListDisplay: 戻る / ロゴクリック
        Filling --> Submitting: 提出ボタン押下
        Submitting --> Result: 判定完了 (Success)
        Submitting --> Filling: エラー (Error)
        Result --> Filling: 再挑戦 / リセット
        
        note right of Leaderboard
            常時右側に表示
            (スマホでは下部)
        end note
    }

```

**[図の読み方]**
アプリケーションの画面遷移を表しています。トップページは「問題一覧」で、ここから各「問題詳細」画面へ遷移します。
問題詳細画面では、コードの穴埋め作業（Filling）を行い、「提出」を行うとAPI通信中の状態（Submitting）を経て、判定結果（Result）が表示されます。
ランキング（Leaderboard）は問題詳細画面内に配置されており、提出完了時などに更新されます。ユーザー名は初回アクセス時または変更時にモーダルで入力します。

## 実行・判定フローチャート

```mermaid
flowchart TD
    subgraph Frontend ["フロントエンド (React)"]
        Start(("開始")) --> Input["穴埋め回答の選択"]
        Input --> Submit["提出ボタン押下"]
        Submit --> PostRequest["POST /api/submissions/create.php"]
    end

    subgraph Backend ["バックエンド (PHP)"]
        PostRequest --> RateCheck{"RateLimitチェック"}
        RateCheck -- 制限超過 --> Return429["429 Too Many Requests"]
        RateCheck -- OK --> Validate["入力値検証"]
        
        Validate --> ProblemRepo[("ProblemRepository")]
        ProblemRepo --> GetProblem["問題情報・テストケース取得"]
        
        GetProblem --> Assembler["CodeAssembler: コード組み立て"]
        Assembler -- 失敗 --> Return400["400 Bad Request"]
        
        Assembler -- 成功 --> CreateSub[("SubmissionRepository: 保存")]
        CreateSub --> SubPending{"提出作成 (PENDING)"}
        
        SubPending --> JudgeService["JudgeService: 判定開始"]
        
        subgraph JudgeLoop ["テストケース実行ループ"]
            JudgeService --> Judge0{"Judge0Client"}
            Judge0 --> PistonAPI["POST Piston API (Execute)"]
            PistonAPI -- stdout/stderr --> Normalize["Normalizer: 出力正規化"]
            Normalize --> Compare{"期待出力と比較"}
            
            Compare -- 一致 --> StatusAC[AC]
            Compare -- 不一致 --> StatusWA[WA]
            
            PistonAPI -- error/timeout --> StatusErr{"エラー判定"}
            StatusErr -- タイムアウト --> StatusTLE[TLE]
            StatusErr -- 構文エラー --> StatusCE[CE]
            StatusErr -- ランタイムエラー --> StatusRE[RE]
            StatusErr -- システムエラー --> StatusIE[IE]
        end
        
        JudgeLoop --> CalcScore["スコア計算 (時間・回数・正解数)"]
        CalcScore --> UpdateSub[("SubmissionRepository: 更新")]
        UpdateSub --> SaveResults[("結果保存")]
        
        SaveResults --> ReturnJSON["JSONレスポンス返却"]
    end

    subgraph FrontendResponse ["フロントエンド"]
        ReturnJSON --> ShowResult["結果パネル表示"]
        Return400 --> ShowError["エラー表示"]
        Return429 --> ShowRateLimit["待機時間表示"]
    end

    ShowResult --> End(("終了"))
```

**[図の読み方]**
ユーザーが「提出」を行ってから結果が表示されるまでの処理フローです。
フロントエンドから送信された回答IDは、バックエンドでテンプレートコードと結合され、実行可能なPythonコードになります。
その後、Piston API (Judge0Client) を通じてコードが実行され、その出力が期待値と比較（Normalizer）されます。
全てのテストケースの結果に基づきスコアが計算され、データベースに保存された後、結果がフロントエンドに返されます。
レート制限や不正な入力に対するガード処理も含まれています。

## コード組み立てロジック (Code Assembly Flow)

```mermaid
flowchart TD
    Start(("開始")) --> Init["テンプレート, 穴情報, 回答(ID)を受け取る"]
    Init --> LoopStart{"穴(Hole)ごとにループ"}
    
    LoopStart -- 次の穴へ --> FetchData["穴IDと回答IDを取得"]
    FetchData --> SearchOption["穴の選択肢リストから回答IDのOptionを検索"]
    
    SearchOption --> Found{"選択肢が見つかった？"}
    Found -- No --> ErrorOption["エラー: Invalid Answer ID"]
    Found -- Yes --> GetCode["選択コードを取得"]
    
    GetCode --> ReplaceMarker["テンプレート内のマーカー (__HOLE_ID__) を置換"]
    ReplaceMarker --> CheckMarker{"マーカーが存在した？"}
    CheckMarker -- No --> ErrorMarker["エラー: Marker Not Found"]
    CheckMarker -- Yes --> LoopStart
    
    LoopStart -- 全て完了 --> FinalCheck{"正規表現で未置換マーカーをチェック"}
    FinalCheck -- 発見 --> ErrorUnresolved["エラー: Unresolved Hole Marker"]
    
    FinalCheck -- なし --> ReturnCode["完成したコードを返す"]
    ReturnCode --> End(("終了"))
    
    ErrorOption --> EndError(("例外送出"))
    ErrorMarker --> EndError
    ErrorUnresolved --> EndError
```

**[図の読み方]**
`CodeAssembler` クラスが、穴埋め回答をテンプレートコードに埋め込んで実行可能なコードを生成する手順です。
セキュリティのため、ユーザーからの任意入力ではなく、事前に定義された `Option` のコードのみが埋め込まれます。
不正なIDやテンプレート不整合がある場合は例外がスローされます。

## 提出ステータス遷移図

```mermaid
stateDiagram-v2
    [*] --> PENDING: 提出作成 (API受付)
    
    state "判定中" as JudgeProcess {
        PENDING --> Running: Judge0へ送信
        Running --> Finished: 結果受信
    }

    Finished --> AC: 正解 (Accepted)
    Finished --> WA: 不正解 (Wrong Answer)
    Finished --> TLE: 時間切れ (Time Limit Exceeded)
    Finished --> CE: コンパイルエラー (Compilation Error)
    Finished --> RE: 実行時エラー (Runtime Error)
    Finished --> IE: 内部エラー (Internal Error)

    AC --> [*]
    WA --> [*]
    TLE --> [*]
    CE --> [*]
    RE --> [*]
    IE --> [*]
```

**[図の読み方]**
提出 (`Submission`) オブジェクトの状態遷移です。
最初は `PENDING` としてデータベースに保存され、判定処理が終わると結果ステータス (`AC`, `WA` 等) に更新されます。
`IE` はシステム側のエラー（Judge0 APIエラー等）を表します。

## 問題解答画面のUI状態遷移図 (Frontend Problem Solving UI State)

```mermaid
stateDiagram-v2
    [*] --> Loading: ページロード
    
    Loading --> Idle: 問題データ取得完了
    Loading --> Error: 通信エラー/404

    state "解答中 (Solving)" as Solving {
        Idle --> TimerActive: 最初の穴を選択
        TimerActive --> TimerActive: 他の穴を選択
    }

    TimerActive --> Submitting: 提出ボタン押下 (全穴埋め完了時)

    state "通信中" as Submitting {
        [*] --> PostSubmission: API呼び出し
        PostSubmission --> ResultReady: 成功
        PostSubmission --> SubmitError: 失敗
    }

    Submitting --> ResultDisplay: 成功 (Show Result)
    Submitting --> TimerActive: 失敗 (Show Error Toast)

    state "結果表示 (Result)" as ResultDisplay {
        [*] --> ShowPanel
        ShowPanel --> Idle: 再挑戦 (Reset)
    }

    Error --> [*]: ホームへ戻る
```

**[図の読み方]**
フロントエンド (`Problem.tsx`) におけるUIの状態遷移です。
ユーザーが最初の操作を行うとタイマーが開始される `TimerActive` 状態になります。
提出処理中はUIがロックされ、結果が返ってくると `ResultDisplay` 状態になります。
再挑戦ボタンを押すと、初期状態 (`Idle`) にリセットされます。

## データベース ER図 (Database ER Diagram)

```mermaid
erDiagram
    problems ||--o{ submissions : "has many"
    submissions ||--o{ submission_cases : "has many"

    problems {
        int id PK "自動増分"
        string title "タイトル"
        string statement_md "問題文(Markdown)"
        string template_code "テンプレートコード"
        json holes_json "穴埋め情報"
        json tests_json "テストケース"
        int time_limit_ms "制限時間(ms)"
        int memory_limit_kb "制限メモリ(KB)"
        datetime created_at
    }

    submissions {
        int id PK "自動増分"
        int problem_id FK "問題ID"
        string session_id "セッションID (匿名ユーザー識別)"
        string user_name "ユーザー名 (任意)"
        json answers_json "回答配列"
        string status "ステータス (PENDING/AC/WA...)"
        int score "スコア"
        int elapsed_ms "解答時間"
        datetime created_at
    }

    submission_cases {
        int id PK
        int submission_id FK "提出ID"
        int case_index "ケース番号"
        string status "個別ステータス"
        string expected "期待出力"
        string actual "実際出力"
    }
```

**[図の読み方]**
システムのデータ構造です。
`problems` (問題) に対して複数の `submissions` (提出) が紐付きます。
1つの提出には複数のテストケース結果 `submission_cases` が含まれます。
ユーザー情報は独立したテーブルを持たず、`submissions` テーブル内の `session_id` と `user_name` で管理されています（簡易的な実装）。

## システムアーキテクチャ図 (System Architecture)

```mermaid
graph TD
    Client["Web Browser"] -- HTTP/HTTPS --> Apache["Apache Web Server"]
    
    subgraph "Server (XAMPP)"
        Apache -- Static Files --> React["Frontend Build (React)"]
        Apache -- API Requests --> PHP["Backend API (PHP)"]
        
        PHP -- SQL --> SQLite[("SQLite Database")]
        PHP --> RateLimit{"Rate Limit (Session)"}
    end
    
    subgraph "External Service"
        PHP -- HTTP POST (JSON) --> Piston["Judge0 / Piston API"]
    end

    Client -.-> React
    Client -.-> PHP
```

**[図の読み方]**
システム全体の構成図です。
ユーザーはブラウザからアクセスし、Apache経由でReactの静的ファイルとPHPのAPIを利用します。
PHPバックエンドはSQLiteデータベースでデータを永続化し、コード実行のために外部のPiston API (Judge0互換) を呼び出します。

## フロントエンドコンポーネントマップ (Frontend Component Map)

```mermaid
graph TD
    App["App.tsx"] --> UserProvider["UserContextProvider"]
    UserProvider --> Router["BrowserRouter"]
    
    Router --> HomeRoute["Route: /"]
    Router --> ProblemRoute["Route: /problem/:id"]
    
    HomeRoute --> Home["Home.tsx"]
    Home --> NameInputModal["NameInputModal.tsx"]
    Home --> API_List["api.getProblems"]
    
    ProblemRoute --> Problem["Problem.tsx"]
    Problem --> CodeViewer["CodeViewer.tsx"]
    Problem --> HolePicker["HolePicker.tsx"]
    Problem --> ResultPanel["ResultPanel.tsx"]
    Problem --> Leaderboard["Leaderboard.tsx"]
    
    Problem --> API_Get["api.getProblem"]
    Problem --> API_Submit["api.createSubmission"]
    
    Leaderboard --> API_Rank["api.getLeaderboard"]
```

**[図の読み方]**
フロントエンドのコンポーネント構成と依存関係です。
`App.tsx` がルートとなり、ルーティングによって `Home` または `Problem` ページが表示されます。
`Problem` ページは機能ごとに複数のコンポーネント (`CodeViewer`, `HolePicker` 等) に分割されています。
`api.ts` を介してバックエンドと通信します。
