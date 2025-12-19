# Mermaid Export Tool

ARCHITECTURE.html に含まれる Mermaid 図を PNG 画像として一括出力するツールです。

## 概要

- HTML ファイル (`ARCHITECTURE.html`) を読み込み、`div.mermaid` 要素を個別にスクリーンショット撮影します。
- Playwright を使用してブラウザ上でレンダリングされた SVG をキャプチャするため、高精細な画像が得られます。
- ローカルの Mermaid 実行環境が動作しない場合でも、自動的にフォールバックモード（Mermaid コードを抽出して再描画）が動作します。

## セットアップ

このツールを使用するには、Node.js 環境と依存パッケージのインストールが必要です。

1. **パッケージのインストール**
   プロジェクトルートで以下のコマンドを実行してください。

   ```bash
   npm install -D playwright mermaid
   ```

   ※ `package.json` がない場合は、先に `npm init -y` を実行してください。

2. **ブラウザバイナリのインストール**
   Playwright が使用する Chromium ブラウザをインストールします（初回のみ）。

   ```bash
   npx playwright install chromium
   ```

## 使い方

以下のコマンドで実行します。

```bash
node tools/export-mermaid-png.mjs [オプション]
```

### オプション

| オプション | 省略時 | 説明 |
| --- | --- | --- |
| `--input <file>` | `ARCHITECTURE.html` | 入力元の HTML ファイルパス |
| `--out <dir>` | `png_out` | 出力先のディレクトリ（自動作成されます） |
| `--wait <ms>` | `1500` | ページ読み込み後の待機時間（ミリ秒） |

### 実行例

**基本実行（デフォルト設定）**
```bash
node tools/export-mermaid-png.mjs
```

**入力・出力を指定する場合**
```bash
node tools/export-mermaid-png.mjs --input docs/design.html --out docs/images
```

## 出力ファイル

出力ファイル名は以下の形式で生成されます：

```text
NN_見出しテキスト.png
```

- `NN`: 連番（01, 02...）
- `見出しテキスト`: 直前の `<h2>` タグの内容をファイル名として安全な文字（`_`など）に置換したもの。

例：
- `01_ページ遷移図.png`
- `02_実行・判定フローチャート.png`

## エラー処理

- 対象の図（`div.mermaid`）が1つも見つからない場合は、エラー終了します。
- 一部の図で出力に失敗した場合、処理は続行され、最後に失敗した図のリストが表示されます。
- エラー詳細は出力ディレクトリ内の `errors.log` に保存されます。

## トラブルシューティング

**Q. 画像が真っ白、またはレンダリングされていない**
Wait時間を増やしてみてください。
```bash
node tools/export-mermaid-png.mjs --wait 3000
```

**Q. `ARCHITECTURE.html` の読み込みに失敗する**
ファイルパスが間違っていないか確認してください。絶対パスまたはカレントディレクトリからの相対パスで指定します。
