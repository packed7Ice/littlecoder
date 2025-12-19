<?php

// 難易度カラムを追加し、既存問題に難易度を設定

require_once __DIR__ . '/../src/Infra/Env.php';
require_once __DIR__ . '/../src/Infra/Db.php';

use LittleCoder\Infra\Db;

try {
    $db = Db::getInstance();

    // カラムが存在するかチェック
    $result = $db->query("PRAGMA table_info(problems)")->fetchAll();
    $hasDifficulty = false;
    foreach ($result as $col) {
        if ($col['name'] === 'difficulty') {
            $hasDifficulty = true;
            break;
        }
    }

    if (!$hasDifficulty) {
        $db->exec('ALTER TABLE problems ADD COLUMN difficulty INTEGER DEFAULT 1');
        echo "difficulty カラムを追加しました。\n";
    } else {
        echo "difficulty カラムは既に存在します。\n";
    }

    // 難易度を設定 (1=初級, 2=中級, 3=上級)
    $difficulties = [
        // 初級 (1) - 基本的な構文
        1 => 1,
        2 => 1,
        3 => 1,
        8 => 1,
        14 => 1,
        26 => 1,
        29 => 1,
        39 => 1,
        42 => 1,
        43 => 1,
        44 => 1,
        46 => 1,
        47 => 1,
        48 => 1,
        49 => 1,
        50 => 1,

        // 中級 (2) - ループ、条件分岐
        4 => 2,
        5 => 2,
        6 => 2,
        7 => 2,
        9 => 2,
        10 => 2,
        11 => 2,
        12 => 2,
        13 => 2,
        15 => 2,
        16 => 2,
        18 => 2,
        19 => 2,
        20 => 2,
        27 => 2,
        28 => 2,
        30 => 2,
        31 => 2,
        32 => 2,
        33 => 2,
        34 => 2,
        35 => 2,
        37 => 2,
        38 => 2,
        40 => 2,
        41 => 2,
        45 => 2,

        // 上級 (3) - アルゴリズム
        17 => 3,
        21 => 3,
        22 => 3,
        23 => 3,
        24 => 3,
        25 => 3,
        36 => 3,
    ];

    $stmt = $db->prepare('UPDATE problems SET difficulty = ? WHERE id = ?');
    foreach ($difficulties as $id => $diff) {
        $stmt->execute([$diff, $id]);
    }

    echo "\n難易度を設定しました！\n";

    // 確認
    $stmt = $db->query('SELECT difficulty, COUNT(*) as count FROM problems GROUP BY difficulty ORDER BY difficulty');
    $rows = $stmt->fetchAll();

    $labels = [1 => '初級', 2 => '中級', 3 => '上級'];
    echo "\n難易度別問題数:\n";
    foreach ($rows as $row) {
        $label = $labels[$row['difficulty']] ?? '不明';
        echo "  {$label}: {$row['count']}問\n";
    }
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
