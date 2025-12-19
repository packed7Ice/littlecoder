<?php

// user_name カラムを追加するマイグレーションスクリプト

require_once __DIR__ . '/../src/Infra/Env.php';
require_once __DIR__ . '/../src/Infra/Db.php';

use LittleCoder\Infra\Db;

try {
    $db = Db::getInstance();

    // カラムが存在するかチェック
    $result = $db->query("PRAGMA table_info(submissions)")->fetchAll();
    $hasUserName = false;
    foreach ($result as $col) {
        if ($col['name'] === 'user_name') {
            $hasUserName = true;
            break;
        }
    }

    if (!$hasUserName) {
        $db->exec('ALTER TABLE submissions ADD COLUMN user_name TEXT DEFAULT NULL');
        echo "user_name カラムを追加しました。\n";
    } else {
        echo "user_name カラムは既に存在します。\n";
    }
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
