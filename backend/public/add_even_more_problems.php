<?php

// さらに問題を追加するスクリプト

require_once __DIR__ . '/../src/Infra/Env.php';
require_once __DIR__ . '/../src/Infra/Db.php';

use LittleCoder\Infra\Db;

$problems = [
    // Problem 21: 回文判定
    [
        'title' => '回文判定',
        'statement_md' => '## 問題

文字列 S が与えられます。S が回文（前から読んでも後ろから読んでも同じ）かどうか判定してください。

### 入力
```
S
```

- S は英小文字のみからなる文字列
- 1 ≤ |S| ≤ 100

### 出力
回文なら「Yes」、そうでなければ「No」を出力してください。

### 例
**入力**
```
madam
```

**出力**
```
Yes
```',
        'template_code' => 's = input()

reversed_s = __HOLE_1__

if __HOLE_2__:
    print("Yes")
else:
    print("No")',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "逆順にする方法", "options": [
                {"id": 0, "code": "s[::-1]"},
                {"id": 1, "code": "s[::1]"},
                {"id": 2, "code": "s.reverse()"}
            ]},
            {"id": "HOLE_2", "label": "比較条件", "options": [
                {"id": 0, "code": "s == reversed_s"},
                {"id": 1, "code": "s != reversed_s"},
                {"id": 2, "code": "s in reversed_s"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "madam\n", "stdout": "Yes\n"},
            {"stdin": "hello\n", "stdout": "No\n"},
            {"stdin": "a\n", "stdout": "Yes\n"}
        ]'
    ],
    // Problem 22: 素数判定
    [
        'title' => '素数判定',
        'statement_md' => '## 問題

整数 N が与えられます。N が素数かどうか判定してください。

### 入力
```
N
```

- 2 ≤ N ≤ 1000

### 出力
素数なら「Yes」、そうでなければ「No」を出力してください。

### 例
**入力**
```
7
```

**出力**
```
Yes
```',
        'template_code' => 'n = int(input())

is_prime = True
for i in range(2, __HOLE_1__):
    if __HOLE_2__:
        is_prime = False
        break

if is_prime:
    print("Yes")
else:
    print("No")',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "ループ上限", "options": [
                {"id": 0, "code": "int(n**0.5) + 1"},
                {"id": 1, "code": "n"},
                {"id": 2, "code": "n // 2"}
            ]},
            {"id": "HOLE_2", "label": "割り切れる条件", "options": [
                {"id": 0, "code": "n % i == 0"},
                {"id": 1, "code": "n % i != 0"},
                {"id": 2, "code": "n // i == 0"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "7\n", "stdout": "Yes\n"},
            {"stdin": "4\n", "stdout": "No\n"},
            {"stdin": "2\n", "stdout": "Yes\n"},
            {"stdin": "1000\n", "stdout": "No\n"}
        ]'
    ],
    // Problem 23: 最大公約数
    [
        'title' => '最大公約数',
        'statement_md' => '## 問題

2つの整数 A, B が与えられます。A と B の最大公約数を求めてください。

### 入力
```
A B
```

- 1 ≤ A, B ≤ 1000

### 出力
最大公約数を1行で出力してください。

### 例
**入力**
```
12 18
```

**出力**
```
6
```',
        'template_code' => 'a, b = map(int, input().split())

while __HOLE_1__:
    a, b = __HOLE_2__

print(a)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "ループ条件", "options": [
                {"id": 0, "code": "b != 0"},
                {"id": 1, "code": "a != 0"},
                {"id": 2, "code": "a > b"}
            ]},
            {"id": "HOLE_2", "label": "更新式", "options": [
                {"id": 0, "code": "b, a % b"},
                {"id": 1, "code": "a, b % a"},
                {"id": 2, "code": "a - b, b"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "12 18\n", "stdout": "6\n"},
            {"stdin": "7 3\n", "stdout": "1\n"},
            {"stdin": "100 25\n", "stdout": "25\n"}
        ]'
    ],
    // Problem 24: フィボナッチ数列
    [
        'title' => 'フィボナッチ数列',
        'statement_md' => '## 問題

整数 N が与えられます。フィボナッチ数列の第 N 項を求めてください。
F(1) = 1, F(2) = 1, F(n) = F(n-1) + F(n-2)

### 入力
```
N
```

- 1 ≤ N ≤ 30

### 出力
F(N) の値を1行で出力してください。

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

a, b = __HOLE_1__
for i in range(n - 1):
    a, b = __HOLE_2__

print(a)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "初期値", "options": [
                {"id": 0, "code": "1, 1"},
                {"id": 1, "code": "0, 1"},
                {"id": 2, "code": "1, 0"}
            ]},
            {"id": "HOLE_2", "label": "更新式", "options": [
                {"id": 0, "code": "b, a + b"},
                {"id": 1, "code": "a + b, a"},
                {"id": 2, "code": "a, a + b"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "10\n", "stdout": "55\n"},
            {"stdin": "1\n", "stdout": "1\n"},
            {"stdin": "20\n", "stdout": "6765\n"}
        ]'
    ],
    // Problem 25: 二分探索
    [
        'title' => '二分探索',
        'statement_md' => '## 問題

ソート済みの N 個の整数と、探したい整数 X が与えられます。二分探索で X の位置を見つけてください。

### 入力
```
N X
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 配列はソート済み
- X は必ず配列に存在する

### 出力
X の位置（0-indexed）を1行で出力してください。

### 例
**入力**
```
5 3
1 2 3 4 5
```

**出力**
```
2
```',
        'template_code' => 'line1 = input().split()
n, x = int(line1[0]), int(line1[1])
a = list(map(int, input().split()))

left, right = 0, n - 1

while left <= right:
    mid = __HOLE_1__
    if a[mid] == x:
        print(mid)
        break
    elif __HOLE_2__:
        left = mid + 1
    else:
        right = mid - 1',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "中央の計算", "options": [
                {"id": 0, "code": "(left + right) // 2"},
                {"id": 1, "code": "left + right // 2"},
                {"id": 2, "code": "(left + right) / 2"}
            ]},
            {"id": "HOLE_2", "label": "左に進む条件", "options": [
                {"id": 0, "code": "a[mid] < x"},
                {"id": 1, "code": "a[mid] > x"},
                {"id": 2, "code": "a[mid] <= x"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "5 3\n1 2 3 4 5\n", "stdout": "2\n"},
            {"stdin": "7 7\n1 3 5 7 9 11 13\n", "stdout": "3\n"},
            {"stdin": "3 1\n1 2 3\n", "stdout": "0\n"}
        ]'
    ],
    // Problem 26: 文字列の反転
    [
        'title' => '文字列の反転',
        'statement_md' => '## 問題

文字列 S が与えられます。S を反転して出力してください。

### 入力
```
S
```

- S は英小文字のみからなる文字列
- 1 ≤ |S| ≤ 100

### 出力
反転した文字列を1行で出力してください。

### 例
**入力**
```
hello
```

**出力**
```
olleh
```',
        'template_code' => 's = input()

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "反転方法", "options": [
                {"id": 0, "code": "s[::-1]"},
                {"id": 1, "code": "s[::1]"},
                {"id": 2, "code": "reversed(s)"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "hello\n", "stdout": "olleh\n"},
            {"stdin": "abc\n", "stdout": "cba\n"},
            {"stdin": "a\n", "stdout": "a\n"}
        ]'
    ],
    // Problem 27: 約数の個数
    [
        'title' => '約数の個数',
        'statement_md' => '## 問題

整数 N が与えられます。N の約数の個数を求めてください。

### 入力
```
N
```

- 1 ≤ N ≤ 1000

### 出力
約数の個数を1行で出力してください。

### 例
**入力**
```
12
```

**出力**
```
6
```

**補足**: 12の約数は 1, 2, 3, 4, 6, 12 の6個',
        'template_code' => 'n = int(input())

count = 0
for i in range(__HOLE_1__):
    if __HOLE_2__:
        count += 1

print(count)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "ループ範囲", "options": [
                {"id": 0, "code": "1, n + 1"},
                {"id": 1, "code": "n"},
                {"id": 2, "code": "1, n"}
            ]},
            {"id": "HOLE_2", "label": "約数判定", "options": [
                {"id": 0, "code": "n % i == 0"},
                {"id": 1, "code": "n // i == 0"},
                {"id": 2, "code": "i % n == 0"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "12\n", "stdout": "6\n"},
            {"stdin": "7\n", "stdout": "2\n"},
            {"stdin": "1\n", "stdout": "1\n"}
        ]'
    ],
    // Problem 28: 配列の積
    [
        'title' => '配列の積',
        'statement_md' => '## 問題

N個の整数が与えられます。これらの積を求めてください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 10
- 1 ≤ a_i ≤ 10

### 出力
積を1行で出力してください。

### 例
**入力**
```
3
2 3 4
```

**出力**
```
24
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

result = __HOLE_1__
for x in a:
    result __HOLE_2__ x

print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "初期値", "options": [
                {"id": 0, "code": "1"},
                {"id": 1, "code": "0"},
                {"id": 2, "code": "a[0]"}
            ]},
            {"id": "HOLE_2", "label": "演算子", "options": [
                {"id": 0, "code": "*="},
                {"id": 1, "code": "+="},
                {"id": 2, "code": "/="}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "3\n2 3 4\n", "stdout": "24\n"},
            {"stdin": "5\n1 2 3 4 5\n", "stdout": "120\n"},
            {"stdin": "1\n7\n", "stdout": "7\n"}
        ]'
    ],
    // Problem 29: 大文字変換
    [
        'title' => '大文字変換',
        'statement_md' => '## 問題

文字列 S が与えられます。S をすべて大文字に変換して出力してください。

### 入力
```
S
```

- S は英字のみからなる文字列
- 1 ≤ |S| ≤ 100

### 出力
大文字に変換した文字列を1行で出力してください。

### 例
**入力**
```
Hello
```

**出力**
```
HELLO
```',
        'template_code' => 's = input()

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "大文字変換", "options": [
                {"id": 0, "code": "s.upper()"},
                {"id": 1, "code": "s.lower()"},
                {"id": 2, "code": "s.capitalize()"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "Hello\n", "stdout": "HELLO\n"},
            {"stdin": "abc\n", "stdout": "ABC\n"},
            {"stdin": "XYZ\n", "stdout": "XYZ\n"}
        ]'
    ],
    // Problem 30: 配列のソート
    [
        'title' => '配列のソート',
        'statement_md' => '## 問題

N個の整数が与えられます。昇順にソートして出力してください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 1000

### 出力
ソート結果をスペース区切りで1行で出力してください。

### 例
**入力**
```
5
3 1 4 1 5
```

**出力**
```
1 1 3 4 5
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

__HOLE_1__
print(__HOLE_2__)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "ソート方法", "options": [
                {"id": 0, "code": "a.sort()"},
                {"id": 1, "code": "a.reverse()"},
                {"id": 2, "code": "sorted(a)"}
            ]},
            {"id": "HOLE_2", "label": "出力形式", "options": [
                {"id": 0, "code": "\" \".join(map(str, a))"},
                {"id": 1, "code": "a"},
                {"id": 2, "code": "str(a)"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "5\n3 1 4 1 5\n", "stdout": "1 1 3 4 5\n"},
            {"stdin": "3\n9 5 2\n", "stdout": "2 5 9\n"},
            {"stdin": "1\n42\n", "stdout": "42\n"}
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
    $stmt = $db->query('SELECT COUNT(*) as count FROM problems');
    $count = $stmt->fetch()['count'];

    echo "\n現在の問題数: {$count}問\n";
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
