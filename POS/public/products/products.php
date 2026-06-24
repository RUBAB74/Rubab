<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $BASE_URL . '/login.php');
    exit;
}

include __DIR__ . '/../includes/header.php';

$msg = $_GET['msg'] ?? '';
$products = $pdo->query("SELECT * FROM products ORDER BY name")->fetchAll();
?>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e8dbcb, #51733f);
    color: #222;
    margin: 0;
    padding: 20px;
}

/* Container */
.products-container {
    max-width: 1200px;
    margin: auto;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
}

/* Header */
.products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.products-title {
    font-weight: 700;
    font-size: 2rem;
    color: #51733f;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Buttons */
.btn {
    font-weight: 600;
    border-radius: 12px;
    padding: 6px 14px;
    font-size: 0.9rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-add { 
    background: #51733f; 
    color: #e8dbcb; 
}

.btn-edit { 
    background: #e8dbcb; 
    color: #51733f; 
}

.btn-delete { 
    background: #51733f; 
    color: #e8dbcb; 
}

/* Modern Table */
.table-container {
    overflow-x: auto;
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 15px;
    overflow: hidden;
    min-width: 700px;
    background: rgba(255,255,255,0.05);
    transition: all 0.3s ease;
}

.table th, .table td {
    padding: 16px 20px;
    text-align: left;
    color: #51733f;
    transition: all 0.2s ease;
}

.table thead {
    background: linear-gradient(90deg, #51733f, #e8dbcb);
    color: #fff;
    font-weight: 700;
}

.table-striped tbody tr:nth-child(odd) {
    background: rgba(232,219,203,0.1);
}

.table-striped tbody tr:nth-child(even) {
    background: rgba(81,115,63,0.15);
}

.table tbody tr:hover {
    background: rgba(232,219,203,0.25) !important;
    transform: scale(1.02);
}

.table td .btn {
    padding: 4px 10px;
    font-size: 0.85rem;
}

/* Icons */
.btn i {
    margin-right: 5px;
}
</style>

<div class="products-container">

    <div class="products-header">
        <h3 class="products-title"><i class="fa-solid fa-box"></i> Products</h3>
        <a class="btn btn-add btn-sm" href="add_product.php">
            <i class="fa-solid fa-plus"></i> Add Product
        </a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success" style="background:#51733f; color:#e8dbcb; padding:10px; border-radius:10px;">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table class="table table-striped table-sm rounded-3">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Price (Rs)</th>
                    <th>Stock</th>
                    <th style="width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['sku']) ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= number_format($p['price'], 2) ?></td>
                    <td><?= (int)$p['stock'] ?></td>
                    <td>
                        <a class="btn btn-sm btn-edit" href="edit_product.php?id=<?= $p['id'] ?>">
                            <i class="fa-solid fa-pencil"></i> Edit
                        </a>
                        <a class="btn btn-sm btn-delete" href="delete_product.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete product?')">
                            <i class="fa-solid fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
