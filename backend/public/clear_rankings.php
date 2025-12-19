<?php

// ランキングデータをすべてクリアするスクリプト

require_once __DIR__ . '/../src/Infra/Env.php';
require_once __DIR__ . '/../src/Infra/Db.php';

use LittleCoder\Infra\Db;

try {
    $db = Db::getInstance();

    $db->exec('DELETE FROM submission_cases');
    $db->exec('DELETE FROM submissions');

    echo "すべての提出とランキングをクリアしました！\n";
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
