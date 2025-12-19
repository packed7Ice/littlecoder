<?php

// さらに問題を追加するスクリプト

require_once __DIR__ . '/../src/Infra/Env.php';
require_once __DIR__ . '/../src/Infra/Db.php';

use LittleCoder\Infra\Db;

$problems = [
    // Problem 11: 累乗
    [
        'title' => '累乗',
        'statement_md' => '## 問題

整数 A と B が与えられます。A の B 乗を計算してください。

### 入力
```
A B
```

- 1 ≤ A ≤ 10
- 0 ≤ B ≤ 10

### 出力
A^B の値を1行で出力してください。

### 例
**入力**
```
2 10
```

**出力**
```
1024
```',
        'template_code' => 'a, b = map(int, input().split())

result = __HOLE_1__
for i in range(b):
    result __HOLE_2__ a

print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "初期値", "options": [
                {"id": 0, "code": "1"},
                {"id": 1, "code": "0"},
                {"id": 2, "code": "a"}
            ]},
            {"id": "HOLE_2", "label": "演算子", "options": [
                {"id": 0, "code": "*="},
                {"id": 1, "code": "+="},
                {"id": 2, "code": "/="}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "2 10\n", "stdout": "1024\n"},
            {"stdin": "3 3\n", "stdout": "27\n"},
            {"stdin": "5 0\n", "stdout": "1\n"}
        ]'
    ],
    // Problem 12: 絶対値
    [
        'title' => '絶対値',
        'statement_md' => '## 問題

整数 N が与えられます。N の絶対値を出力してください。

### 入力
```
N
```

- -1000 ≤ N ≤ 1000

### 出力
|N| の値を1行で出力してください。

### 例
**入力**
```
-5
```

**出力**
```
5
```',
        'template_code' => 'n = int(input())

if __HOLE_1__:
    result = __HOLE_2__
else:
    result = n

print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "条件", "options": [
                {"id": 0, "code": "n < 0"},
                {"id": 1, "code": "n > 0"},
                {"id": 2, "code": "n == 0"}
            ]},
            {"id": "HOLE_2", "label": "負の場合の処理", "options": [
                {"id": 0, "code": "-n"},
                {"id": 1, "code": "n"},
                {"id": 2, "code": "0"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "-5\n", "stdout": "5\n"},
            {"stdin": "10\n", "stdout": "10\n"},
            {"stdin": "0\n", "stdout": "0\n"}
        ]'
    ],
    // Problem 13: 奇数の合計
    [
        'title' => '奇数の合計',
        'statement_md' => '## 問題

N個の整数が与えられます。奇数だけの合計を求めてください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 1000

### 出力
奇数の合計を1行で出力してください。

### 例
**入力**
```
5
1 2 3 4 5
```

**出力**
```
9
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

total = 0
for x in a:
    if __HOLE_1__:
        total __HOLE_2__ x

print(total)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "奇数判定", "options": [
                {"id": 0, "code": "x % 2 == 1"},
                {"id": 1, "code": "x % 2 == 0"},
                {"id": 2, "code": "x > 0"}
            ]},
            {"id": "HOLE_2", "label": "演算子", "options": [
                {"id": 0, "code": "+="},
                {"id": 1, "code": "-="},
                {"id": 2, "code": "*="}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "5\n1 2 3 4 5\n", "stdout": "9\n"},
            {"stdin": "3\n2 4 6\n", "stdout": "0\n"},
            {"stdin": "4\n1 3 5 7\n", "stdout": "16\n"}
        ]'
    ],
    // Problem 14: 文字列の長さ
    [
        'title' => '文字列の長さ',
        'statement_md' => '## 問題

文字列 S が与えられます。S の長さを出力してください。

### 入力
```
S
```

- S は英小文字のみからなる文字列
- 1 ≤ |S| ≤ 100

### 出力
文字列の長さを1行で出力してください。

### 例
**入力**
```
hello
```

**出力**
```
5
```',
        'template_code' => 's = input()

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "長さの取得", "options": [
                {"id": 0, "code": "len(s)"},
                {"id": 1, "code": "s.count"},
                {"id": 2, "code": "s[0]"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "hello\n", "stdout": "5\n"},
            {"stdin": "a\n", "stdout": "1\n"},
            {"stdin": "programming\n", "stdout": "11\n"}
        ]'
    ],
    // Problem 15: 2倍にする
    [
        'title' => '全要素を2倍',
        'statement_md' => '## 問題

N個の整数が与えられます。各要素を2倍にして出力してください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 500

### 出力
各要素を2倍にしたものをスペース区切りで1行で出力してください。

### 例
**入力**
```
3
1 2 3
```

**出力**
```
2 4 6
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

result = []
for x in a:
    result.append(__HOLE_1__)

print(__HOLE_2__)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "2倍の計算", "options": [
                {"id": 0, "code": "x * 2"},
                {"id": 1, "code": "x + 2"},
                {"id": 2, "code": "x / 2"}
            ]},
            {"id": "HOLE_2", "label": "出力形式", "options": [
                {"id": 0, "code": "\" \".join(map(str, result))"},
                {"id": 1, "code": "result"},
                {"id": 2, "code": "str(result)"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "3\n1 2 3\n", "stdout": "2 4 6\n"},
            {"stdin": "4\n10 20 30 40\n", "stdout": "20 40 60 80\n"},
            {"stdin": "1\n5\n", "stdout": "10\n"}
        ]'
    ],
    // Problem 16: 正の数のみ出力
    [
        'title' => '正の数のみ出力',
        'statement_md' => '## 問題

N個の整数が与えられます。正の数（0より大きい数）のみを出力してください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- -100 ≤ a_i ≤ 100

### 出力
正の数をスペース区切りで1行で出力してください。正の数がない場合は何も出力しないでください。

### 例
**入力**
```
5
-1 2 -3 4 0
```

**出力**
```
2 4
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

result = []
for x in a:
    if __HOLE_1__:
        result.__HOLE_2__(x)

if result:
    print(" ".join(map(str, result)))',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "正の数の条件", "options": [
                {"id": 0, "code": "x > 0"},
                {"id": 1, "code": "x >= 0"},
                {"id": 2, "code": "x < 0"}
            ]},
            {"id": "HOLE_2", "label": "リストへの追加", "options": [
                {"id": 0, "code": "append"},
                {"id": 1, "code": "remove"},
                {"id": 2, "code": "pop"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "5\n-1 2 -3 4 0\n", "stdout": "2 4\n"},
            {"stdin": "3\n1 2 3\n", "stdout": "1 2 3\n"},
            {"stdin": "2\n-5 -10\n", "stdout": ""}
        ]'
    ],
    // Problem 17: 重複を除去
    [
        'title' => '重複を除去',
        'statement_md' => '## 問題

N個の整数が与えられます。重複を除去して、出現順に出力してください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 100

### 出力
重複を除去した数列を出現順にスペース区切りで1行で出力してください。

### 例
**入力**
```
6
1 2 2 3 1 4
```

**出力**
```
1 2 3 4
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

seen = __HOLE_1__
result = []
for x in a:
    if x not in seen:
        seen.__HOLE_2__(x)
        result.append(x)

print(" ".join(map(str, result)))',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "空の集合", "options": [
                {"id": 0, "code": "set()"},
                {"id": 1, "code": "[]"},
                {"id": 2, "code": "{}"}
            ]},
            {"id": "HOLE_2", "label": "集合への追加", "options": [
                {"id": 0, "code": "add"},
                {"id": 1, "code": "append"},
                {"id": 2, "code": "insert"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "6\n1 2 2 3 1 4\n", "stdout": "1 2 3 4\n"},
            {"stdin": "3\n5 5 5\n", "stdout": "5\n"},
            {"stdin": "4\n1 2 3 4\n", "stdout": "1 2 3 4\n"}
        ]'
    ],
    // Problem 18: 要素の存在確認
    [
        'title' => '要素の存在確認',
        'statement_md' => '## 問題

N個の整数と、探したい整数 X が与えられます。X が配列に含まれているかどうかを判定してください。

### 入力
```
N X
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ X, a_i ≤ 1000

### 出力
X が含まれていれば「Yes」、含まれていなければ「No」を出力してください。

### 例
**入力**
```
5 3
1 2 3 4 5
```

**出力**
```
Yes
```',
        'template_code' => 'n, x = map(int, input().split())
a = list(map(int, input().split()))

if __HOLE_1__:
    print(__HOLE_2__)
else:
    print("No")',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "存在確認", "options": [
                {"id": 0, "code": "x in a"},
                {"id": 1, "code": "x not in a"},
                {"id": 2, "code": "a in x"}
            ]},
            {"id": "HOLE_2", "label": "出力", "options": [
                {"id": 0, "code": "\"Yes\""},
                {"id": 1, "code": "\"No\""},
                {"id": 2, "code": "x"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "5 3\n1 2 3 4 5\n", "stdout": "Yes\n"},
            {"stdin": "3 10\n1 2 3\n", "stdout": "No\n"},
            {"stdin": "1 5\n5\n", "stdout": "Yes\n"}
        ]'
    ],
    // Problem 19: 1から N までの合計
    [
        'title' => '1からNまでの合計',
        'statement_md' => '## 問題

整数 N が与えられます。1 から N までの整数の合計を求めてください。

### 入力
```
N
```

- 1 ≤ N ≤ 1000

### 出力
1 + 2 + ... + N の値を1行で出力してください。

### 例
**入力**
```
10
```

**出力**
```
55
```',
        'template_code' => 'n = int(input())

total = 0
for i in range(__HOLE_1__):
    total += __HOLE_2__

print(total)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "ループ範囲", "options": [
                {"id": 0, "code": "1, n + 1"},
                {"id": 1, "code": "n"},
                {"id": 2, "code": "0, n"}
            ]},
            {"id": "HOLE_2", "label": "加算する値", "options": [
                {"id": 0, "code": "i"},
                {"id": 1, "code": "n"},
                {"id": 2, "code": "1"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "10\n", "stdout": "55\n"},
            {"stdin": "1\n", "stdout": "1\n"},
            {"stdin": "100\n", "stdout": "5050\n"}
        ]'
    ],
    // Problem 20: 2番目に大きい値
    [
        'title' => '2番目に大きい値',
        'statement_md' => '## 問題

N個の整数が与えられます。2番目に大きい値を求めてください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 2 ≤ N ≤ 100
- 1 ≤ a_i ≤ 1000
- すべての要素は異なる

### 出力
2番目に大きい値を1行で出力してください。

### 例
**入力**
```
5
3 1 4 1 5
```

**出力**
```
4
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

a.__HOLE_1__()
print(a[__HOLE_2__])',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "ソート方法", "options": [
                {"id": 0, "code": "sort"},
                {"id": 1, "code": "reverse"},
                {"id": 2, "code": "clear"}
            ]},
            {"id": "HOLE_2", "label": "インデックス", "options": [
                {"id": 0, "code": "-2"},
                {"id": 1, "code": "-1"},
                {"id": 2, "code": "1"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "5\n3 1 4 1 5\n", "stdout": "4\n"},
            {"stdin": "3\n10 20 30\n", "stdout": "20\n"},
            {"stdin": "2\n100 50\n", "stdout": "50\n"}
        ]'
    ]
];

try {
    $db = Db::getInstance();

    $stmt = $db->prepare('
        INSERT INTO problems (title, statement_md, template_code, holes_json, tests_json, time_limit_ms, memory_limit_kb)
        VALUES (?, ?, ?, ?, ?, 2000, 131072)
    ');

    foreach ($problems as $p) {
        $stmt->execute([
            $p['title'],
            $p['statement_md'],
            $p['template_code'],
            $p['holes_json'],
            $p['tests_json']
        ]);
    }

    echo "問題を追加しました！\n";

    // 確認
    $stmt = $db->query('SELECT id, title FROM problems ORDER BY id');
    $all = $stmt->fetchAll();

    echo "\n現在の問題一覧 (計" . count($all) . "問):\n";
    foreach ($all as $p) {
        echo "  {$p['id']}. {$p['title']}\n";
    }
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
