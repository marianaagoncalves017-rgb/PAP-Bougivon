<?php
$host = 'sql104.infinityfree.com';
$db   = 'if0_42214535_bougivon';
$user = 'if0_42214535';
$pass = 'iara31122003';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na ligação à base de dados: " . $e->getMessage());
}
?>