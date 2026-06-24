<?php
session_start();
require_once __DIR__ . '/includes/config.php';
$_SESSION = [];
session_destroy();
header('Location: ' . $BASE_URL . '/login.php');
exit;
