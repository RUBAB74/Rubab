<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['user_id'])) header('Location: ' . $BASE_URL . '/login.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $desc = trim($_POST['description'] ?? '');

    if ($name === '') $errors[] = 'Name required';
    if ($sku === '') $errors[] = 'SKU required';
    if (!is_numeric($price) || $price < 0) $errors[] = 'Invalid price';
    if (!is_numeric($stock) || $stock < 0) $errors[] = 'Invalid stock';

    if (empty($errors)) {
        $ins = $pdo->prepare("INSERT INTO products (sku,name,description,price,stock) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([$sku,$name,$desc,$price,$stock]);
        header('Location: products.php?msg=added'); exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ===== Body ===== */
body {
    font-family: 'Poppins', sans-serif;
    background: #e8dbcb;
    margin: 0;
    padding: 20px;
    color: #333;
    min-height: 100vh;
}

/* ===== Card Container ===== */
.add-product-card {
    max-width: 700px;
    margin: auto;
    background: #51733f;
    color: #e8dbcb;
    border-radius: 20px;
    padding: 40px 30px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.25);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.add-product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.35);
}

/* ===== Heading ===== */
.add-product-card h4 {
    font-weight: 700;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.8rem;
}

.add-product-card h4 i {
    font-size: 24px;
}

/* ===== Form Fields ===== */
.form-control {
    border-radius: 12px;
    border: none;
    padding: 12px 15px;
    width: 100%;
    background: #e8dbcb;
    color: #51733f;
    font-weight: 500;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    box-shadow: 0 0 8px rgba(81,115,63,0.6);
}

/* ===== Labels ===== */
.form-label {
    font-weight: 600;
    margin-bottom: 6px;
    display: block;
}

/* ===== Buttons ===== */
.btn-save {
    background: #e8dbcb;
    color: #51733f;
    font-weight: 600;
    border-radius: 12px;
    padding: 12px;
    font-size: 16px;
    border: none;
    transition: all 0.3s ease;
}

.btn-save:hover {
    background: #d6c6b2;
    transform: scale(1.05);
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
}

/* ===== Alerts ===== */
.alert-danger {
    background: #e8dbcb;
    color: #51733f;
    font-weight: 600;
    padding: 12px 18px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .add-product-card {
        padding: 25px 20px;
    }
}
</style>

<div class="add-product-card">
    <h4><i class="fa-solid fa-box"></i> Add Product</h4>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= implode('<br>',$errors) ?></div>
    <?php endif; ?>

    <form method="post" onsubmit="return validateProductForm()">
        <div class="mb-3">
            <label class="form-label">SKU</label>
            <input name="sku" id="sku" class="form-control" value="<?= htmlspecialchars($_POST['sku'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" id="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Price</label>
                <input name="price" id="price" class="form-control" value="<?= htmlspecialchars($_POST['price'] ?? '0.00') ?>">
            </div>
            <div class="col">
                <label class="form-label">Stock</label>
                <input name="stock" id="stock" class="form-control" value="<?= htmlspecialchars($_POST['stock'] ?? '0') ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="d-grid">
            <button class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
function validateProductForm() {
    const sku = document.getElementById('sku').value.trim();
    const name = document.getElementById('name').value.trim();
    const price = parseFloat(document.getElementById('price').value);
    const stock = parseInt(document.getElementById('stock').value);

    if (!sku || !name || isNaN(price) || price < 0 || isNaN(stock) || stock < 0) {
        alert('Please fill all fields correctly.');
        return false;
    }
    return true;
}
</script>
