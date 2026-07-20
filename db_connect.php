<?php

$dsn = 'mysql:dbname=XXXDB;host=localhost';
$user = 'XXXUSER';
$password = 'XXXPASSWORD';

$pdo = new PDO(
    $dsn,
    $user,
    $password,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)
);

?>