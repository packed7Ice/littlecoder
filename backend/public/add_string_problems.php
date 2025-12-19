<?php

// 非数学系の問題を追加

require_once __DIR__ . '/../src/Infra/Env.php';
require_once __DIR__ . '/../src/Infra/Db.php';

use LittleCoder\Infra\Db;

$problems = [
    // Problem 31: 母音カウント
    [
        'title' => '母音カウント',
        'statement_md' => '## 問題

文字列 S が与えられます。母音（a, e, i, o, u）の数を数えてください。

### 入力
```
S
```

- S は英小文字のみからなる文字列
- 1 ≤ |S| ≤ 100

### 出力
母音の個数を1行で出力してください。

### 例
**入力**
```
hello
```

**出力**
```
2
```',
        'template_code' => 's = input()
vowels = __HOLE_1__

count = 0
for c in s:
    if __HOLE_2__:
        count += 1

print(count)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "母音リスト", "options": [
                {"id": 0, "code": "\"aeiou\""},
                {"id": 1, "code": "[a, e, i, o, u]"},
                {"id": 2, "code": "\"abcde\""}
            ]},
            {"id": "HOLE_2", "label": "判定条件", "options": [
                {"id": 0, "code": "c in vowels"},
                {"id": 1, "code": "c == vowels"},
                {"id": 2, "code": "c not in vowels"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "hello\n", "stdout": "2\n"},
            {"stdin": "aeiou\n", "stdout": "5\n"},
            {"stdin": "xyz\n", "stdout": "0\n"}
        ]'
    ],
    // Problem 32: 単語分割
    [
        'title' => '単語数カウント',
        'statement_md' => '## 問題

スペース区切りの文字列 S が与えられます。単語の数を数えてください。

### 入力
```
S
```

- S はスペース区切りの単語からなる
- 1 ≤ 単語数 ≤ 20

### 出力
単語の個数を1行で出力してください。

### 例
**入力**
```
hello world
```

**出力**
```
2
```',
        'template_code' => 's = input()

words = __HOLE_1__
print(__HOLE_2__)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "分割方法", "options": [
                {"id": 0, "code": "s.split()"},
                {"id": 1, "code": "s.split(\",\")"},
                {"id": 2, "code": "list(s)"}
            ]},
            {"id": "HOLE_2", "label": "個数取得", "options": [
                {"id": 0, "code": "len(words)"},
                {"id": 1, "code": "words"},
                {"id": 2, "code": "sum(words)"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "hello world\n", "stdout": "2\n"},
            {"stdin": "a b c d e\n", "stdout": "5\n"},
            {"stdin": "python\n", "stdout": "1\n"}
        ]'
    ],
    // Problem 33: 文字の置換
    [
        'title' => '文字の置換',
        'statement_md' => '## 問題

文字列 S と2つの文字 A, B が与えられます。S 中の A をすべて B に置換して出力してください。

### 入力
```
S
A B
```

- S は英小文字のみからなる文字列
- 1 ≤ |S| ≤ 100
- A, B は英小文字1文字

### 出力
置換後の文字列を1行で出力してください。

### 例
**入力**
```
hello
l x
```

**出力**
```
hexxo
```',
        'template_code' => 's = input()
a, b = input().split()

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "置換方法", "options": [
                {"id": 0, "code": "s.replace(a, b)"},
                {"id": 1, "code": "s.replace(b, a)"},
                {"id": 2, "code": "s.split(a)"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "hello\nl x\n", "stdout": "hexxo\n"},
            {"stdin": "aaa\na b\n", "stdout": "bbb\n"},
            {"stdin": "xyz\na b\n", "stdout": "xyz\n"}
        ]'
    ],
    // Problem 34: リストの結合
    [
        'title' => 'リストの結合',
        'statement_md' => '## 問題

2つの配列が与えられます。これらを結合して1つの配列として出力してください。

### 入力
```
N M
a_1 a_2 ... a_N
b_1 b_2 ... b_M
```

- 1 ≤ N, M ≤ 50
- 1 ≤ 要素 ≤ 100

### 出力
結合した配列をスペース区切りで1行で出力してください。

### 例
**入力**
```
3 2
1 2 3
4 5
```

**出力**
```
1 2 3 4 5
```',
        'template_code' => 'n, m = map(int, input().split())
a = list(map(int, input().split()))
b = list(map(int, input().split()))

result = __HOLE_1__
print(__HOLE_2__)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "結合方法", "options": [
                {"id": 0, "code": "a + b"},
                {"id": 1, "code": "a - b"},
                {"id": 2, "code": "[a, b]"}
            ]},
            {"id": "HOLE_2", "label": "出力形式", "options": [
                {"id": 0, "code": "\" \".join(map(str, result))"},
                {"id": 1, "code": "result"},
                {"id": 2, "code": "\",\".join(result)"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "3 2\n1 2 3\n4 5\n", "stdout": "1 2 3 4 5\n"},
            {"stdin": "1 1\n10\n20\n", "stdout": "10 20\n"},
            {"stdin": "2 3\n1 2\n3 4 5\n", "stdout": "1 2 3 4 5\n"}
        ]'
    ],
    // Problem 35: 条件分岐 - 成績判定
    [
        'title' => '成績判定',
        'statement_md' => '## 問題

点数 N が与えられます。以下の基準で成績を判定してください：
- 90点以上: A
- 80点以上90点未満: B
- 70点以上80点未満: C
- 60点以上70点未満: D
- 60点未満: F

### 入力
```
N
```

- 0 ≤ N ≤ 100

### 出力
成績を1行で出力してください。

### 例
**入力**
```
85
```

**出力**
```
B
```',
        'template_code' => 'n = int(input())

if __HOLE_1__:
    print("A")
elif n >= 80:
    print("B")
elif n >= 70:
    print("C")
elif __HOLE_2__:
    print("D")
else:
    print("F")',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "A判定条件", "options": [
                {"id": 0, "code": "n >= 90"},
                {"id": 1, "code": "n > 90"},
                {"id": 2, "code": "n == 90"}
            ]},
            {"id": "HOLE_2", "label": "D判定条件", "options": [
                {"id": 0, "code": "n >= 60"},
                {"id": 1, "code": "n > 60"},
                {"id": 2, "code": "n < 60"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "85\n", "stdout": "B\n"},
            {"stdin": "100\n", "stdout": "A\n"},
            {"stdin": "59\n", "stdout": "F\n"},
            {"stdin": "60\n", "stdout": "D\n"}
        ]'
    ],
    // Problem 36: 辞書のキー取得
    [
        'title' => '辞書からの取得',
        'statement_md' => '## 問題

名前と点数のペアが N 組与えられます。指定された名前の点数を出力してください。

### 入力
```
N
name_1 score_1
name_2 score_2
...
name_N score_N
target_name
```

- 1 ≤ N ≤ 10
- 名前は英小文字のみ
- 点数は整数

### 出力
指定された名前の点数を1行で出力してください。

### 例
**入力**
```
3
alice 80
bob 90
charlie 70
bob
```

**出力**
```
90
```',
        'template_code' => 'n = int(input())
scores = __HOLE_1__

for i in range(n):
    line = input().split()
    name = line[0]
    score = int(line[1])
    __HOLE_2__

target = input()
print(scores[target])',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "辞書の初期化", "options": [
                {"id": 0, "code": "{}"},
                {"id": 1, "code": "[]"},
                {"id": 2, "code": "set()"}
            ]},
            {"id": "HOLE_2", "label": "辞書への追加", "options": [
                {"id": 0, "code": "scores[name] = score"},
                {"id": 1, "code": "scores.append(score)"},
                {"id": 2, "code": "scores.add(name)"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "3\nalice 80\nbob 90\ncharlie 70\nbob\n", "stdout": "90\n"},
            {"stdin": "2\nx 100\ny 50\nx\n", "stdout": "100\n"},
            {"stdin": "1\ntest 42\ntest\n", "stdout": "42\n"}
        ]'
    ],
    // Problem 37: リスト内包表記
    [
        'title' => '2乗のリスト',
        'statement_md' => '## 問題

N個の整数が与えられます。各要素を2乗したリストを作成して出力してください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 20
- 1 ≤ a_i ≤ 10

### 出力
2乗したリストをスペース区切りで1行で出力してください。

### 例
**入力**
```
3
1 2 3
```

**出力**
```
1 4 9
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

result = __HOLE_1__
print(" ".join(map(str, result)))',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "リスト内包表記", "options": [
                {"id": 0, "code": "[x**2 for x in a]"},
                {"id": 1, "code": "[x*2 for x in a]"},
                {"id": 2, "code": "[x+2 for x in a]"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "3\n1 2 3\n", "stdout": "1 4 9\n"},
            {"stdin": "4\n2 3 4 5\n", "stdout": "4 9 16 25\n"},
            {"stdin": "1\n10\n", "stdout": "100\n"}
        ]'
    ],
    // Problem 38: フィルタリング
    [
        'title' => '条件フィルタ',
        'statement_md' => '## 問題

N個の整数が与えられます。10以上の数だけを抽出して出力してください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 20
- 1 ≤ a_i ≤ 100

### 出力
10以上の数をスペース区切りで1行で出力してください。該当がなければ空行を出力。

### 例
**入力**
```
5
5 10 15 3 20
```

**出力**
```
10 15 20
```',
        'template_code' => 'n = int(input())
a = list(map(int, input().split()))

result = __HOLE_1__
if result:
    print(" ".join(map(str, result)))
else:
    print()',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "フィルタ条件", "options": [
                {"id": 0, "code": "[x for x in a if x >= 10]"},
                {"id": 1, "code": "[x for x in a if x < 10]"},
                {"id": 2, "code": "[x for x in a if x == 10]"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "5\n5 10 15 3 20\n", "stdout": "10 15 20\n"},
            {"stdin": "3\n1 2 3\n", "stdout": "\n"},
            {"stdin": "4\n100 50 10 9\n", "stdout": "100 50 10\n"}
        ]'
    ],
    // Problem 39: 文字列の連結
    [
        'title' => '文字列の連結',
        'statement_md' => '## 問題

2つの文字列 A, B が与えられます。A と B を連結して出力してください。

### 入力
```
A
B
```

- A, B は英小文字のみからなる文字列
- 1 ≤ |A|, |B| ≤ 50

### 出力
連結した文字列を1行で出力してください。

### 例
**入力**
```
hello
world
```

**出力**
```
helloworld
```',
        'template_code' => 'a = input()
b = input()

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "連結方法", "options": [
                {"id": 0, "code": "a + b"},
                {"id": 1, "code": "a - b"},
                {"id": 2, "code": "a * b"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "hello\nworld\n", "stdout": "helloworld\n"},
            {"stdin": "abc\nxyz\n", "stdout": "abcxyz\n"},
            {"stdin": "a\nb\n", "stdout": "ab\n"}
        ]'
    ],
    // Problem 40: インデックス検索
    [
        'title' => 'インデックス検索',
        'statement_md' => '## 問題

N個の整数と検索値 X が与えられます。X が最初に出現する位置（0-indexed）を出力してください。

### 入力
```
N X
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
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
        'template_code' => 'line = input().split()
n, x = int(line[0]), int(line[1])
a = list(map(int, input().split()))

result = __HOLE_1__
print(result)',
        'holes_json' => '[
            {"id": "HOLE_1", "label": "インデックス取得", "options": [
                {"id": 0, "code": "a.index(x)"},
                {"id": 1, "code": "a.find(x)"},
                {"id": 2, "code": "a.count(x)"}
            ]}
        ]',
        'tests_json' => '[
            {"stdin": "5 3\n1 2 3 4 5\n", "stdout": "2\n"},
            {"stdin": "4 4\n4 1 2 3\n", "stdout": "0\n"},
            {"stdin": "3 7\n5 6 7\n", "stdout": "2\n"}
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
