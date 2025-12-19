<?php

// さらに10問追加

require_once __DIR__ . '/../src/Infra/Env.php';
require_once __DIR__ . '/../src/Infra/Db.php';

use LittleCoder\Infra\Db;

$problems = [
    // Problem 41: 最頻値
    [
        'title' => '最頻値',
        'statement_md' => '## 問題

N個の整数が与えられます。最も多く出現する値を出力してください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 10
- 最頻値は一意に定まる

### 出力
最頻値を1行で出力してください。

### 例
**入力**
```
7
1 2 2 3 3 3 4
```

**出力**
```
3
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

count = {}
for x in a:
    if x in count:
        count[x] __HOLE_1__ 1
    else:
        count[x] = 1

max_val = __HOLE_2__
print(max_val)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "カウント増加", "options": [
                {"id": 0, "code": "+="},
                {"id": 1, "code": "-="},
                {"id": 2, "code": "="}
            ]},
            {"id": "HOLE_2", "label": "最大値のキー取得", "options": [
                {"id": 0, "code": "max(count, key=count.get)"},
                {"id": 1, "code": "max(count.values())"},
                {"id": 2, "code": "max(count.keys())"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "7\n1 2 2 3 3 3 4\n", "stdout": "3\n"},
            {"stdin": "5\n5 5 5 5 5\n", "stdout": "5\n"},
            {"stdin": "3\n1 2 1\n", "stdout": "1\n"}
        ]'
    ],
    // Problem 42: 文字列の繰り返し
    [
        'title' => '文字列の繰り返し',
        'statement_md' => '## 問題

文字列 S と整数 N が与えられます。S を N 回繰り返した文字列を出力してください。

### 入力
```
S N
```

- S は英小文字のみからなる文字列
- 1 ≤ |S| ≤ 10
- 1 ≤ N ≤ 10

### 出力
繰り返した文字列を1行で出力してください。

### 例
**入力**
```
abc 3
```

**出力**
```
abcabcabc
```',
        'template_code' => 'line = input().split()
s = line[0]
n = int(line[1])

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "繰り返し方法", "options": [
                {"id": 0, "code": "s * n"},
                {"id": 1, "code": "s + n"},
                {"id": 2, "code": "s ** n"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "abc 3\n", "stdout": "abcabcabc\n"},
            {"stdin": "x 5\n", "stdout": "xxxxx\n"},
            {"stdin": "hello 1\n", "stdout": "hello\n"}
        ]'
    ],
    // Problem 43: 先頭と末尾
    [
        'title' => '先頭と末尾',
        'statement_md' => '## 問題

N個の整数が与えられます。先頭の要素と末尾の要素を出力してください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 1000

### 出力
先頭と末尾をスペース区切りで1行で出力してください。

### 例
**入力**
```
5
1 2 3 4 5
```

**出力**
```
1 5
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

first = __HOLE_1__
last = __HOLE_2__
print(first, last)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "先頭取得", "options": [
                {"id": 0, "code": "a[0]"},
                {"id": 1, "code": "a[1]"},
                {"id": 2, "code": "a[-1]"}
            ]},
            {"id": "HOLE_2", "label": "末尾取得", "options": [
                {"id": 0, "code": "a[-1]"},
                {"id": 1, "code": "a[0]"},
                {"id": 2, "code": "a[n]"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "5\n1 2 3 4 5\n", "stdout": "1 5\n"},
            {"stdin": "3\n10 20 30\n", "stdout": "10 30\n"},
            {"stdin": "1\n42\n", "stdout": "42 42\n"}
        ]'
    ],
    // Problem 44: スライス
    [
        'title' => '配列のスライス',
        'statement_md' => '## 問題

N個の整数が与えられます。最初の3要素を出力してください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 3 ≤ N ≤ 100
- 1 ≤ a_i ≤ 1000

### 出力
最初の3要素をスペース区切りで1行で出力してください。

### 例
**入力**
```
5
1 2 3 4 5
```

**出力**
```
1 2 3
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

result = __HOLE_1__
print(" ".join(map(str, result)))',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "スライス", "options": [
                {"id": 0, "code": "a[:3]"},
                {"id": 1, "code": "a[3:]"},
                {"id": 2, "code": "a[0:2]"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "5\n1 2 3 4 5\n", "stdout": "1 2 3\n"},
            {"stdin": "4\n10 20 30 40\n", "stdout": "10 20 30\n"},
            {"stdin": "3\n7 8 9\n", "stdout": "7 8 9\n"}
        ]'
    ],
    // Problem 45: 論理演算
    [
        'title' => '論理演算 AND',
        'statement_md' => '## 問題

2つの真偽値 A, B が与えられます（1が真、0が偽）。A AND B の結果を出力してください。

### 入力
```
A B
```

- A, B は 0 または 1

### 出力
AND の結果（0 または 1）を1行で出力してください。

### 例
**入力**
```
1 1
```

**出力**
```
1
```',
        'template_code' => 'a, b = map(int, input().split())

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "AND演算", "options": [
                {"id": 0, "code": "1 if a and b else 0"},
                {"id": 1, "code": "1 if a or b else 0"},
                {"id": 2, "code": "a + b"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "1 1\n", "stdout": "1\n"},
            {"stdin": "1 0\n", "stdout": "0\n"},
            {"stdin": "0 0\n", "stdout": "0\n"}
        ]'
    ],
    // Problem 46: 3つの最大値
    [
        'title' => '3つの最大値',
        'statement_md' => '## 問題

3つの整数 A, B, C が与えられます。最大値を出力してください。

### 入力
```
A B C
```

- 1 ≤ A, B, C ≤ 1000

### 出力
最大値を1行で出力してください。

### 例
**入力**
```
3 1 2
```

**出力**
```
3
```',
        'template_code' => 'a, b, c = map(int, input().split())

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "最大値", "options": [
                {"id": 0, "code": "max(a, b, c)"},
                {"id": 1, "code": "min(a, b, c)"},
                {"id": 2, "code": "a + b + c"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "3 1 2\n", "stdout": "3\n"},
            {"stdin": "5 5 5\n", "stdout": "5\n"},
            {"stdin": "1 100 50\n", "stdout": "100\n"}
        ]'
    ],
    // Problem 47: 割り算の余り
    [
        'title' => '割り算の余り',
        'statement_md' => '## 問題

2つの整数 A, B が与えられます。A を B で割った余りを出力してください。

### 入力
```
A B
```

- 1 ≤ A ≤ 1000
- 1 ≤ B ≤ 100

### 出力
余りを1行で出力してください。

### 例
**入力**
```
10 3
```

**出力**
```
1
```',
        'template_code' => 'a, b = map(int, input().split())

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "余り演算", "options": [
                {"id": 0, "code": "a % b"},
                {"id": 1, "code": "a // b"},
                {"id": 2, "code": "a / b"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "10 3\n", "stdout": "1\n"},
            {"stdin": "15 5\n", "stdout": "0\n"},
            {"stdin": "7 2\n", "stdout": "1\n"}
        ]'
    ],
    // Problem 48: 文字のカウント
    [
        'title' => '特定文字のカウント',
        'statement_md' => '## 問題

文字列 S と文字 C が与えられます。S に C が何回出現するか数えてください。

### 入力
```
S
C
```

- S は英小文字のみからなる文字列
- 1 ≤ |S| ≤ 100
- C は英小文字1文字

### 出力
出現回数を1行で出力してください。

### 例
**入力**
```
banana
a
```

**出力**
```
3
```',
        'template_code' => 's = input()
c = input()

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "カウント方法", "options": [
                {"id": 0, "code": "s.count(c)"},
                {"id": 1, "code": "s.find(c)"},
                {"id": 2, "code": "len(s)"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "banana\na\n", "stdout": "3\n"},
            {"stdin": "hello\nl\n", "stdout": "2\n"},
            {"stdin": "xyz\na\n", "stdout": "0\n"}
        ]'
    ],
    // Problem 49: 数字の桁数
    [
        'title' => '桁数',
        'statement_md' => '## 問題

正の整数 N が与えられます。N の桁数を出力してください。

### 入力
```
N
```

- 1 ≤ N ≤ 10^9

### 出力
桁数を1行で出力してください。

### 例
**入力**
```
12345
```

**出力**
```
5
```',
        'template_code' => 'n = input()

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "桁数取得", "options": [
                {"id": 0, "code": "len(n)"},
                {"id": 1, "code": "int(n)"},
                {"id": 2, "code": "sum(n)"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "12345\n", "stdout": "5\n"},
            {"stdin": "1\n", "stdout": "1\n"},
            {"stdin": "1000000000\n", "stdout": "10\n"}
        ]'
    ],
    // Problem 50: 偶奇判定
    [
        'title' => '偶奇判定',
        'statement_md' => '## 問題

整数 N が与えられます。偶数なら「Even」、奇数なら「Odd」を出力してください。

### 入力
```
N
```

- 1 ≤ N ≤ 1000

### 出力
「Even」または「Odd」を出力してください。

### 例
**入力**
```
4
```

**出力**
```
Even
```',
        'template_code' => 'n = int(input())

if __HOLE_1__:
    print(__HOLE_2__)
else:
    print("Odd")',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "偶数判定", "options": [
                {"id": 0, "code": "n % 2 == 0"},
                {"id": 1, "code": "n % 2 == 1"},
                {"id": 2, "code": "n // 2 == 0"}
            ]},
            {"id": "HOLE_2", "label": "出力", "options": [
                {"id": 0, "code": "\"Even\""},
                {"id": 1, "code": "\"Odd\""},
                {"id": 2, "code": "n"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "4\n", "stdout": "Even\n"},
            {"stdin": "7\n", "stdout": "Odd\n"},
            {"stdin": "100\n", "stdout": "Even\n"}
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

    $stmt = $db->query('SELECT COUNT(*) as count FROM problems');
    $count = $stmt->fetch()['count'];

    echo "\n現在の問題数: {$count}問\n";
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
