# LittleCoder - 穴埋め式ミニ競技プログラミング

ブラウザで遊べる「穴埋め式ミニ競プロ」ゲームです。Python コードの穴埋めを完成させて、すべてのテストケースを通過させましょう！

## 機能

- **穴埋め式コード問題**: Python のコードテンプレートから穴埋め部分を選択
- **自動判定**: Piston API（無料・APIキー不要）を使用してコードを実行・判定
- **ステータス表示**: AC / WA / TLE / RE / CE の判定結果を表示
- **スコアリング**: 経過時間と試行回数に基づくスコア計算
- **ランキング**: 問題ごとのハイスコアランキング

## 技術スタック

### Frontend
- React + TypeScript + Vite
- Tailwind CSS
- React Router

### Backend
- PHP 8.x
- SQLite
- Piston API (コード実行 - 無料・APIキー不要)

## セットアップ

### 1. 環境変数の設定

```bash
cd backend
cp .env.example .env
```

**Piston API を使用するため、APIキーの設定は不要です！**

### 2. データベースの初期化

初回アクセス時に自動的にデータベースが作成され、サンプル問題が挿入されます。

### 3. フロントエンドのビルド

```bash
cd frontend
npm install
npm run build
```

### 4. ビルド成果物の配置

`frontend/dist/` の内容を `backend/public/` にコピーします：

```bash
# Windows
xcopy /E /Y frontend\dist\* backend\public\

# Linux/Mac
cp -r frontend/dist/* backend/public/
```

### 5. 起動

#### 開発環境

**バックエンド（PHP built-in server）:**
```bash
cd backend/public
php -S localhost:8080
```

**フロントエンド（Vite dev server）:**
```bash
cd frontend
npm run dev
```

開発時は Vite の開発サーバー (http://localhost:5173) を使用し、API は自動的に http://localhost:8080 にプロキシされます。

#### 本番環境 (XAMPP)

XAMPP を使用している場合、プロジェクトを `htdocs` に配置すれば動作します。

http://localhost/littlecoder/backend/public/ でアクセスできます。

## 問題の追加方法

`backend/sql/schema.sql` または直接 SQLite データベースに INSERT します。

### 問題データの形式

```sql
INSERT INTO problems (title, statement_md, template_code, holes_json, tests_json) VALUES (
    '問題タイトル',
    '## 問題

問題文をMarkdown形式で記述...

### 入力
...

### 出力
...',
    'n = int(input())
# テンプレートコード
result = __HOLE_1__  # 穴マーカー
print(result)',
    '[
        {
            "id": "HOLE_1",
            "label": "穴1のラベル",
            "options": [
                {"id": 0, "code": "選択肢1"},
                {"id": 1, "code": "選択肢2"},
                {"id": 2, "code": "選択肢3"}
            ]
        }
    ]',
    '[
        {"stdin": "入力1\n", "stdout": "期待出力1\n"},
        {"stdin": "入力2\n", "stdout": "期待出力2\n"}
    ]'
);
```

### 穴マーカー形式

コード内で `__HOLE_1__`, `__HOLE_2__` のようにマーカーを記述します。番号は `holes_json` の配列順に対応します。

## API エンドポイント

| メソッド | エンドポイント | 説明 |
|---------|---------------|------|
| GET | `/api/problems/list.php` | 問題一覧を取得 |
| GET | `/api/problems/get.php?id=N` | 問題詳細を取得 |
| POST | `/api/submissions/create.php` | 提出を作成・判定 |
| GET | `/api/submissions/result.php?id=N` | 提出結果を取得 |
| GET | `/api/leaderboard/get.php?problemId=N` | ランキングを取得 |

## セキュリティ

- **穴埋め候補のみ送信**: ユーザーからはコード本文を受け取らず、選択した候補IDのみを送信
- **レート制限**: 1セッションあたり1分間に最大5回の提出制限
- **外部API**: Piston API は公開APIのため、APIキー管理は不要

## コード実行 API について

### Piston API (デフォルト - 無料)
- **費用**: 完全無料
- **APIキー**: 不要
- **制限**: 公開APIのため、高負荷時にレート制限がかかる可能性あり
- **URL**: https://emkc.org/api/v2/piston

### Judge0 (オプション - 有料)
より安定性が必要な場合は、`Judge0Client.php` を Judge0 用に書き換えて RapidAPI のキーを設定してください。

