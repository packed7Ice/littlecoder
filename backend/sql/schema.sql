-- LittleCoder Database Schema
-- SQLite

-- 問題テーブル
CREATE TABLE IF NOT EXISTS problems (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    statement_md TEXT NOT NULL,
    template_code TEXT NOT NULL,
    holes_json TEXT NOT NULL,
    tests_json TEXT NOT NULL,
    time_limit_ms INTEGER NOT NULL DEFAULT 2000,
    memory_limit_kb INTEGER NOT NULL DEFAULT 131072,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

-- 提出テーブル
CREATE TABLE IF NOT EXISTS submissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    problem_id INTEGER NOT NULL,
    session_id TEXT NOT NULL,
    answers_json TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'PENDING',
    score INTEGER NOT NULL DEFAULT 0,
    elapsed_ms INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (problem_id) REFERENCES problems(id)
);

-- ケース別結果テーブル（オプション）
CREATE TABLE IF NOT EXISTS submission_cases (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    submission_id INTEGER NOT NULL,
    case_index INTEGER NOT NULL,
    status TEXT NOT NULL,
    expected TEXT,
    actual TEXT,
    FOREIGN KEY (submission_id) REFERENCES submissions(id)
);

-- インデックス
CREATE INDEX IF NOT EXISTS idx_submissions_problem_id ON submissions(problem_id);
CREATE INDEX IF NOT EXISTS idx_submissions_session_id ON submissions(session_id);
CREATE INDEX IF NOT EXISTS idx_submissions_status ON submissions(status);
CREATE INDEX IF NOT EXISTS idx_submission_cases_submission_id ON submission_cases(submission_id);

-- 初期問題データ
INSERT INTO problems (title, statement_md, template_code, holes_json, tests_json, time_limit_ms, memory_limit_kb) VALUES
(
    '配列の合計',
    '## 問題

N個の整数が与えられます。これらの合計を求めてください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 1000

### 出力
合計値を1行で出力してください。

### 例
**入力**
```
3
1 2 3
```

**出力**
```
6
```',
    'n = int(input())
a = list(map(int, input().split()))

total = 0
for i in __HOLE_1__:
    total += __HOLE_2__

print(total)',
    '[
        {
            "id": "HOLE_1",
            "label": "ループの範囲",
            "options": [
                {"id": 0, "code": "range(n)"},
                {"id": 1, "code": "range(n-1)"},
                {"id": 2, "code": "range(1, n+1)"}
            ]
        },
        {
            "id": "HOLE_2",
            "label": "加算する値",
            "options": [
                {"id": 0, "code": "a[i]"},
                {"id": 1, "code": "i"},
                {"id": 2, "code": "n"}
            ]
        }
    ]',
    '[
        {"stdin": "3\n1 2 3\n", "stdout": "6\n"},
        {"stdin": "5\n1 1 1 1 1\n", "stdout": "5\n"},
        {"stdin": "1\n100\n", "stdout": "100\n"}
    ]',
    2000,
    131072
),
(
    '最大値',
    '## 問題

N個の整数が与えられます。これらの最大値を求めてください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 1000

### 出力
最大値を1行で出力してください。

### 例
**入力**
```
4
3 1 4 1
```

**出力**
```
4
```',
    'n = int(input())
a = list(map(int, input().split()))

result = __HOLE_1__
for i in range(n):
    if __HOLE_2__:
        result = a[i]

print(result)',
    '[
        {
            "id": "HOLE_1",
            "label": "初期値",
            "options": [
                {"id": 0, "code": "a[0]"},
                {"id": 1, "code": "0"},
                {"id": 2, "code": "1000"}
            ]
        },
        {
            "id": "HOLE_2",
            "label": "更新条件",
            "options": [
                {"id": 0, "code": "a[i] > result"},
                {"id": 1, "code": "a[i] < result"},
                {"id": 2, "code": "a[i] >= result"}
            ]
        }
    ]',
    '[
        {"stdin": "4\n3 1 4 1\n", "stdout": "4\n"},
        {"stdin": "3\n5 5 5\n", "stdout": "5\n"},
        {"stdin": "1\n42\n", "stdout": "42\n"}
    ]',
    2000,
    131072
),
(
    '文字数カウント',
    '## 問題

文字列Sと文字Cが与えられます。S中にCが何回出現するか数えてください。

### 入力
```
S
C
```

- Sは英小文字のみからなる文字列
- 1 ≤ |S| ≤ 100
- Cは英小文字1文字

### 出力
出現回数を1行で出力してください。

### 例
**入力**
```
hello
l
```

**出力**
```
2
```',
    's = input()
c = input()

count = 0
for char in __HOLE_1__:
    if __HOLE_2__:
        count += 1

print(count)',
    '[
        {
            "id": "HOLE_1",
            "label": "ループ対象",
            "options": [
                {"id": 0, "code": "s"},
                {"id": 1, "code": "c"},
                {"id": 2, "code": "range(len(s))"}
            ]
        },
        {
            "id": "HOLE_2",
            "label": "比較条件",
            "options": [
                {"id": 0, "code": "char == c"},
                {"id": 1, "code": "char != c"},
                {"id": 2, "code": "char in c"}
            ]
        }
    ]',
    '[
        {"stdin": "hello\nl\n", "stdout": "2\n"},
        {"stdin": "aaaaaa\na\n", "stdout": "6\n"},
        {"stdin": "abcdef\nz\n", "stdout": "0\n"}
    ]',
    2000,
    131072
);
