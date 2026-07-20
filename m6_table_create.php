<?php

include 'db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS good_things (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_date DATE NOT NULL,
    category VARCHAR(20) NOT NULL,
    title VARCHAR(100) NOT NULL,
    event_text TEXT NOT NULL,
    feeling TEXT NOT NULL,
    happiness_level INT NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$result = $pdo->query($sql);

if ($result !== false) {
    echo 'good_thingsテーブルを作成しました。';
} else {
    echo 'テーブルを作成できませんでした。';
}

?>