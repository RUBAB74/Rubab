<?php
// setup.php - run once to create an admin user
require_once __DIR__ . '/includes/db.php';

$default_user = 'admin';
$default_pass = 'admin123'; // change after first login

// Check if admin exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$default_user]);
if ($stmt->fetch()) {
    echo "Admin already exists. Delete user to recreate.";
    exit;
}

$hash = password_hash($default_pass, PASSWORD_DEFAULT);
$ins = $pdo->prepare("INSERT INTO users (username,password,full_name,role) VALUES (?, ?, ?, ?)");
$ins->execute([$default_user, $hash, 'Administrator', 'admin']);
echo "Admin user created: username=admin password=admin123. Please delete/disable setup.php now.";
