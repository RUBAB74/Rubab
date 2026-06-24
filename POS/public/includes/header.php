<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Basic POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.

  <link rel="stylesheet" href="<?= $BASE_URL ?>/assets/css/styles.css">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
   

    /* Navbar Customization */
    .navbar-custom {
      background-color: #495a4d;
    }
    .navbar-custom .nav-link,
    .navbar-custom .navbar-brand {
      color: #e8dbcb;
      font-weight: 600;
      transition: color 0.3s ease;
    }
    .navbar-custom .nav-link:hover {
      color: #d6c6b2;
    }
    .navbar-custom .nav-link.active {
      color: #f0ead6;
      font-weight: 700;
    }
    /* Add subtle shadow */
    .navbar-custom {
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }


    <style>
    .pos-footer {
        width: 100%;
        padding: 20px 0;
        background: linear-gradient(135deg, #5a4fcf, #7d74e6);
        color: #fff;
        text-align: center;
        border-radius: 16px 16px 0 0;
        margin-top: 40px;
        box-shadow: 0 -3px 12px rgba(0,0,0,0.15);
    }

    .footer-content {
        max-width: 900px;
        margin: auto;
    }

    .pos-footer p {
        font-size: 15px;
        letter-spacing: .3px;
        margin-bottom: 8px;
    }

    .footer-icons i {
        font-size: 20px;
        margin: 0 8px;
        opacity: 0.85;
        transition: .3s;
    }

    .footer-icons i:hover {
        opacity: 1;
        transform: scale(1.2);
    }
</style>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
  <div class="container-fluid">

    <!-- Logo -->
    <a class="navbar-brand fw-bold" href="<?= $BASE_URL ?>/dashboard.php">
      <i class="fa-solid fa-cash-register me-1"></i> Basic POS
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navmenu">

      <!-- Left Menu -->
      <ul class="navbar-nav ms-3">
        <li class="nav-item">
          <a class="nav-link" href="<?= $BASE_URL ?>/products/products.php">
            <i class="fa-solid fa-boxes-stacked me-1"></i> Products
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= $BASE_URL ?>/customers/customers.php">
            <i class="fa-solid fa-users me-1"></i> Customers
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= $BASE_URL ?>/sales/new_sale.php">
            <i class="fa-solid fa-cart-shopping me-1"></i> New Sale
          </a>
        </li>
      </ul>

      <!-- Right Menu -->
      <ul class="navbar-nav ms-auto">
        <?php if (!empty($_SESSION['username'])): ?>
          <li class="nav-item">
            <span class="nav-link">
              <i class="fa-solid fa-user me-1"></i> <?= htmlspecialchars($_SESSION['username']) ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $BASE_URL ?>/logout.php">
              <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= $BASE_URL ?>/login.php">
              <i class="fa-solid fa-right-to-bracket me-1"></i> Login
            </a>
          </li>
        <?php endif; ?>
      </ul>

    </div>
  </div>
</nav>

<main class="container my-4">
