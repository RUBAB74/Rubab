<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../includes/db.php';

// Get product ID from URL
$id = $_GET['id'] ?? '';
if (!$id || !is_numeric($id)) {
    header('Location: products.php?msg=Invalid product ID');
    exit;
}

// Delete the product
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
if ($stmt->execute([$id])) {
    header('Location: products.php?msg=Product deleted successfully');
    exit;
} else {
    header('Location: products.php?msg=Failed to delete product');
    exit;
}
?>
