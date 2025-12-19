-- 追加問題データ

-- Problem 4: 偶数カウント
INSERT INTO problems (title, statement_md, template_code, holes_json, tests_json, time_limit_ms, memory_limit_kb) VALUES
(
    '偶数カウント',
    '## 問題

N個の整数が与えられます。その中に偶数がいくつあるか数えてください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 1000

### 出力
偶数の個数を1行で出力してください。

### 例
**入力**
```
5
1 2 3 4 5
```

**出力**
```
2
```',
    'n = int(input())
a = list(map(int, input().split()))

count = 0
for x in a:
    if __HOLE_1__:
        count __HOLE_2__ 1

print(count)',
    '[
        {
            "id": "HOLE_1",
            "label": "偶数判定条件",
            "options": [
                {"id": 0, "code": "x % 2 == 0"},
                {"id": 1, "code": "x % 2 == 1"},
                {"id": 2, "code": "x // 2 == 0"}
            ]
        },
        {
            "id": "HOLE_2",
            "label": "カウント操作",
            "options": [
                {"id": 0, "code": "+="},
                {"id": 1, "code": "-="},
                {"id": 2, "code": "*="}
            ]
        }
    ]',
    '[
        {"stdin": "5\n1 2 3 4 5\n", "stdout": "2\n"},
        {"stdin": "3\n2 4 6\n", "stdout": "3\n"},
        {"stdin": "4\n1 3 5 7\n", "stdout": "0\n"}
    ]',
    2000,
    131072
);

-- Problem 5: 最小値
INSERT INTO problems (title, statement_md, template_code, holes_json, tests_json, time_limit_ms, memory_limit_kb) VALUES
(
    '最小値',
    '## 問題

N個の整数が与えられます。これらの最小値を求めてください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 1000

### 出力
最小値を1行で出力してください。

### 例
**入力**
```
4
3 1 4 1
```

**出力**
```
1
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
                {"id": 0, "code": "a[i] < result"},
                {"id": 1, "code": "a[i] > result"},
                {"id": 2, "code": "a[i] <= result"}
            ]
        }
    ]',
    '[
        {"stdin": "4\n3 1 4 1\n", "stdout": "1\n"},
        {"stdin": "3\n5 5 5\n", "stdout": "5\n"},
        {"stdin": "1\n42\n", "stdout": "42\n"}
    ]',
    2000,
    131072
);

-- Problem 6: 平均値
INSERT INTO problems (title, statement_md, template_code, holes_json, tests_json, time_limit_ms, memory_limit_kb) VALUES
(
    '平均値（切り捨て）',
    '## 問題

N個の整数が与えられます。これらの平均値を**小数点以下切り捨て**で求めてください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 1000

### 出力
平均値（切り捨て）を1行で出力してください。

### 例
**入力**
```
3
10 20 30
```

**出力**
```
20
```',
    'n = int(input())
a = list(map(int, input().split()))

total = 0
for x in a:
    total += x

average = __HOLE_1__
print(average)',
    '[
        {
            "id": "HOLE_1",
            "label": "平均値の計算",
            "options": [
                {"id": 0, "code": "total // n"},
                {"id": 1, "code": "total / n"},
                {"id": 2, "code": "total % n"}
            ]
        }
    ]',
    '[
        {"stdin": "3\n10 20 30\n", "stdout": "20\n"},
        {"stdin": "4\n1 2 3 4\n", "stdout": "2\n"},
        {"stdin": "2\n5 6\n", "stdout": "5\n"}
    ]',
    2000,
    131072
);

-- Problem 7: 逆順出力
INSERT INTO problems (title, statement_md, template_code, holes_json, tests_json, time_limit_ms, memory_limit_kb) VALUES
(
    '逆順出力',
    '## 問題

N個の整数が与えられます。これらを逆順に出力してください。

### 入力
```
N
a_1 a_2 ... a_N
```

- 1 ≤ N ≤ 100
- 1 ≤ a_i ≤ 1000

### 出力
N個の整数を逆順にスペース区切りで1行で出力してください。

### 例
**入力**
```
3
1 2 3
```

**出力**
```
3 2 1
```',
    'n = int(input())
a = list(map(int, input().split()))

result = []
for i in range(__HOLE_1__):
    result.append(__HOLE_2__)

print(" ".join(map(str, result)))',
    '[
        {
            "id": "HOLE_1",
            "label": "ループ範囲",
            "options": [
                {"id": 0, "code": "n-1, -1, -1"},
                {"id": 1, "code": "n"},
                {"id": 2, "code": "0, n, 1"}
            ]
        },
        {
            "id": "HOLE_2",
            "label": "追加する要素",
            "options": [
                {"id": 0, "code": "a[i]"},
                {"id": 1, "code": "a[n-i]"},
                {"id": 2, "code": "i"}
            ]
        }
    ]',
    '[
        {"stdin": "3\n1 2 3\n", "stdout": "3 2 1\n"},
        {"stdin": "5\n5 4 3 2 1\n", "stdout": "1 2 3 4 5\n"},
        {"stdin": "1\n42\n", "stdout": "42\n"}
    ]',
    2000,
    131072
);

-- Problem 8: 2つの数の積
INSERT INTO problems (title, statement_md, template_code, holes_json, tests_json, time_limit_ms, memory_limit_kb) VALUES
(
    '2つの数の積',
    '## 問題

2つの整数 A, B が与えられます。A × B を計算してください。

### 入力
```
A B
```

- 1 ≤ A, B ≤ 1000

### 出力
A × B の値を1行で出力してください。

### 例
**入力**
```
3 4
```

**出力**
```
12
```',
    '__HOLE_1__ = map(int, input().split())

result = __HOLE_2__
print(result)',
    '[
        {
            "id": "HOLE_1",
            "label": "変数の受け取り",
            "options": [
                {"id": 0, "code": "a, b"},
                {"id": 1, "code": "a"},
                {"id": 2, "code": "[a, b]"}
            ]
        },
        {
            "id": "HOLE_2",
            "label": "積の計算",
            "options": [
                {"id": 0, "code": "a * b"},
                {"id": 1, "code": "a + b"},
                {"id": 2, "code": "a - b"}
            ]
        }
    ]',
    '[
        {"stdin": "3 4\n", "stdout": "12\n"},
        {"stdin": "10 10\n", "stdout": "100\n"},
        {"stdin": "1 999\n", "stdout": "999\n"}
    ]',
    2000,
    131072
);

-- Problem 9: FizzBuzz（簡易版）
INSERT INTO problems (title, statement_md, template_code, holes_json, tests_json, time_limit_ms, memory_limit_kb) VALUES
(
    'FizzBuzz判定',
    '## 問題

整数 N が与えられます。以下の条件で出力してください：
- N が 3 と 5 の両方で割り切れるなら「FizzBuzz」
- N が 3 で割り切れるなら「Fizz」
- N が 5 で割り切れるなら「Buzz」
- それ以外なら N そのもの

### 入力
```
N
```

- 1 ≤ N ≤ 100

### 出力
条件に応じた出力を1行で出力してください。

### 例
**入力**
```
15
```

**出力**
```
FizzBuzz
```',
    'n = int(input())

if __HOLE_1__:
    print("FizzBuzz")
elif n % 3 == 0:
    print("Fizz")
elif __HOLE_2__:
    print("Buzz")
else:
    print(n)',
    '[
        {
            "id": "HOLE_1",
            "label": "FizzBuzz条件",
            "options": [
                {"id": 0, "code": "n % 3 == 0 and n % 5 == 0"},
                {"id": 1, "code": "n % 3 == 0 or n % 5 == 0"},
                {"id": 2, "code": "n % 15 != 0"}
            ]
        },
        {
            "id": "HOLE_2",
            "label": "Buzz条件",
            "options": [
                {"id": 0, "code": "n % 5 == 0"},
                {"id": 1, "code": "n % 3 == 0"},
                {"id": 2, "code": "n % 2 == 0"}
            ]
        }
    ]',
    '[
        {"stdin": "15\n", "stdout": "FizzBuzz\n"},
        {"stdin": "9\n", "stdout": "Fizz\n"},
        {"stdin": "10\n", "stdout": "Buzz\n"},
        {"stdin": "7\n", "stdout": "7\n"}
    ]',
    2000,
    131072
);

-- Problem 10: 階乗
INSERT INTO problems (title, statement_md, template_code, holes_json, tests_json, time_limit_ms, memory_limit_kb) VALUES
(
    '階乗',
    '## 問題

整数 N が与えられます。N の階乗 (N!) を計算してください。

### 入力
```
N
```

- 1 ≤ N ≤ 10

### 出力
N! の値を1行で出力してください。

### 例
**入力**
```
5
```

**出力**
```
120
```

**補足**: 5! = 5 × 4 × 3 × 2 × 1 = 120',
    'n = int(input())

result = __HOLE_1__
for i in range(1, n + 1):
    result __HOLE_2__ i

print(result)',
    '[
        {
            "id": "HOLE_1",
            "label": "初期値",
            "options": [
                {"id": 0, "code": "1"},
                {"id": 1, "code": "0"},
                {"id": 2, "code": "n"}
            ]
        },
        {
            "id": "HOLE_2",
            "label": "演算子",
            "options": [
                {"id": 0, "code": "*="},
                {"id": 1, "code": "+="},
                {"id": 2, "code": "-="}
            ]
        }
    ]',
    '[
        {"stdin": "5\n", "stdout": "120\n"},
        {"stdin": "1\n", "stdout": "1\n"},
        {"stdin": "10\n", "stdout": "3628800\n"}
    ]',
    2000,
    131072
);
