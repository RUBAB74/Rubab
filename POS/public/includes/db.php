<?php
require_once __DIR__ . '/config.php';

$host = 'localhost';
$db   = 'pos_db';
$user = 'root';
$pass = ''; // set your DB password
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    // In production, don't echo error
    die('DB connection failed: ' . $e->getMessage());
}
?>
