<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../includes/db.php';
include '../includes/header.php';

// Get product ID from URL
$id = $_GET['id'] ?? '';
if (!$id || !is_numeric($id)) {
    header('Location: products.php');
    exit;
}

// Fetch existing product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';

    if (!$sku || !$name || !$price || !is_numeric($price) || !is_numeric($stock)) {
        $error = "Please fill all fields correctly.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE sku = ? AND id != ?");
        $stmt->execute([$sku, $id]);
        if ($stmt->fetch()) {
            $error = "SKU already exists for another product.";
        } else {
            $stmt = $pdo->prepare("UPDATE products SET sku=?, name=?, description=?, price=?, stock=? WHERE id=?");
            $stmt->execute([$sku, $name, $description, $price, $stock, $id]);
            header('Location: products.php?msg=Product updated successfully');
            exit;
        }
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ===== Body ===== */
body {
    font-family: 'Poppins', sans-serif;
    background: #e8dbcb;
    color: #51733f;
    padding: 20px;
}

/* ===== Card ===== */
.card {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    animation: fadeInUp 0.5s ease forwards;
    background: #fdf6ed;
    border: 1px solid #d6c6b2;
}

/* Card Animation */
@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

/* ===== Headers ===== */
h4 {
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #51733f;
}

/* ===== Alerts ===== */
.alert {
    animation: slideIn 0.5s ease forwards;
    border-radius: 12px;
    padding: 12px 18px;
    margin-bottom: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.alert-danger {
    background: #51733f;
    color: #e8dbcb;
    font-weight: 600;
}
@keyframes slideIn {
    0% { opacity: 0; transform: translateX(-20px); }
    100% { opacity: 1; transform: translateX(0); }
}

/* ===== Form Controls ===== */
.form-control {
    border-radius: 12px;
    border: 1px solid #51733f;
    padding: 8px 12px;
    font-weight: 500;
    color: #51733f;
    background: #fdf6ed;
    transition: all 0.3s ease;
}
.form-control:focus {
    outline: none;
    box-shadow: 0 0 8px rgba(81,115,63,0.6);
}

/* ===== Buttons ===== */
.btn {
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-primary {
    background: #51733f;
    color: #e8dbcb;
    border: none;
}
.btn-primary:hover {
    background: #3e5c2d;
    transform: scale(1.05);
}
.btn-link {
    color: #51733f;
    text-decoration: none;
    transition: all 0.3s ease;
}
.btn-link:hover {
    color: #3e5c2d;
    text-decoration: underline;
}
</style>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm p-4">
      <h4 class="mb-3"><i class="fa-solid fa-pen-to-square"></i> Edit Product</h4>

      <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?=htmlspecialchars($error)?></div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-2">
          <label><i class="fa-solid fa-barcode"></i> SKU</label>
          <input type="text" name="sku" class="form-control" value="<?=htmlspecialchars($product['sku'])?>" required>
        </div>
        <div class="mb-2">
          <label><i class="fa-solid fa-box"></i> Name</label>
          <input type="text" name="name" class="form-control" value="<?=htmlspecialchars($product['name'])?>" required>
        </div>
        <div class="mb-2">
          <label><i class="fa-solid fa-align-left"></i> Description</label>
          <textarea name="description" class="form-control"><?=htmlspecialchars($product['description'])?></textarea>
        </div>
        <div class="mb-2">
          <label><i class="fa-solid fa-dollar-sign"></i> Price</label>
          <input type="number" step="0.01" name="price" class="form-control" value="<?=htmlspecialchars($product['price'])?>" required>
        </div>
        <div class="mb-2">
          <label><i class="fa-solid fa-layer-group"></i> Stock</label>
          <input type="number" name="stock" class="form-control" value="<?=htmlspecialchars($product['stock'])?>" required>
        </div>
        <div class="d-grid mt-3">
          <button class="btn btn-primary"><i class="fa-solid fa-check"></i> Update Product</button>
        </div>
      </form>

      <a href="products.php" class="btn btn-link mt-3"><i class="fa-solid fa-arrow-left"></i> Back to Products</a>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
