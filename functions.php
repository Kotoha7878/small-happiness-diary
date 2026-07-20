<?php

// HTMLに安全に文字を表示する
function h($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// 投稿番号が正の整数か確認する
function isValidId($value)
{
    return $value !== ''
        && ctype_digit((string)$value)
        && (int)$value > 0;
}

// カテゴリが正しいか確認する
function isValidCategory($category)
{
    $categories = [
        '良かったこと',
        '癒されたこと',
        '嬉しかったこと'
    ];

    return in_array($category, $categories, true);
}

?>