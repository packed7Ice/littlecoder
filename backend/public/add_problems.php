<?php

// 追加問題をデータベースに挿入するスクリプト

require_once __DIR__ . '/../src/Infra/Env.php';
require_once __DIR__ . '/../src/Infra/Db.php';

use LittleCoder\Infra\Db;

try {
    $db = Db::getInstance();

    $sqlPath = __DIR__ . '/../sql/add_problems.sql';
    $sql = file_get_contents($sqlPath);

    $db->exec($sql);

    echo "問題を追加しました！\n";

    // 確認
    $stmt = $db->query('SELECT id, title FROM problems ORDER BY id');
    $problems = $stmt->fetchAll();

    echo "\n現在の問題一覧:\n";
    foreach ($problems as $p) {
        echo "  {$p['id']}. {$p['title']}\n";
    }
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
