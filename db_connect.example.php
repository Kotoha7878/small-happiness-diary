<?php

$dsn = 'mysql:dbname=YOUR_DATABASE_NAME;host=localhost';
$user = 'YOUR_DATABASE_USER';
$password = 'YOUR_DATABASE_PASSWORD';

$pdo = new PDO(
    $dsn,
    $user,
    $password,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)
);

?>