<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $BASE_URL . "/login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
$stmt->execute([$id]);

header("Location: customers.php?msg=Customer deleted successfully");
exit;
?>
