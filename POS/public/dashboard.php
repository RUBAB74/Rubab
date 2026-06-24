<?php 
session_start(); 
require_once __DIR__ . '/includes/db.php'; 
require_once __DIR__ . '/includes/config.php'; 
if (!isset($_SESSION['user_id'])) header('Location: ' . $BASE_URL . '/login.php'); 
include __DIR__ . '/includes/header.php'; 

// stats
$totProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(); 
$totSales = $pdo->query("SELECT COUNT(*) FROM sales")->fetchColumn(); 
$totCustomers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn(); 
?>

<!-- FONT AWESOME ICONS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ---------- GLOBAL ---------- */
body {
    background: #e8dbcb;
    min-height: 100vh;
    padding: 20px;
    color: #51733f;
    font-family: 'Poppins', sans-serif;
}

/* ---------- STAT CARDS ---------- */
.stat-card {
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 25px 30px;
    border-left: 7px solid #51733f;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transition: all 0.4s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.25);
}

.stat-card small {
    font-size: 15px;
    font-weight: 500;
    color: #51733f;
    display: flex;
    align-items: center;
    gap: 6px;
}

.stat-card h2 {
    font-size: 36px;
    margin: 8px 0;
    color: #51733f;
}

.stat-icon-big {
    font-size: 65px;
    position: absolute;
    right: 20px;
    bottom: 10px;
    color: rgba(81,115,63,0.2);
    transition: 0.5s ease;
}

.stat-card:hover .stat-icon-big {
    transform: scale(1.2) rotate(10deg);
    color: rgba(81,115,63,0.4);
}

/* ---------- SECTION HEADER ---------- */
.section-title {
    font-weight: 600;
    margin-bottom: 15px;
    color: #51733f;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
}

.section-title i {
    font-size: 24px;
}

/* ---------- TABLE ---------- */
.table-responsive {
    border-radius: 20px;
    overflow: hidden;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.table th, .table td {
    padding: 14px 18px;
    font-size: 14px;
    vertical-align: middle;
    color: #51733f;
}

.table th {
    background: rgba(81,115,63,0.8);
    color: #e8dbcb;
    border: none;
    font-weight: 600;
}

.table th i {
    margin-right: 6px;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(255,255,255,0.05);
}

.table-striped tbody tr:nth-of-type(even) {
    background-color: rgba(255,255,255,0.08);
}

.table tbody tr:hover {
    background: rgba(81,115,63,0.2) !important;
    transform: scale(1.01);
    transition: all 0.2s ease-in-out;
    cursor: pointer;
}

/* ---------- ANIMATIONS ---------- */
@keyframes fadeIn {
    from {opacity:0; transform:translateY(15px);}
    to {opacity:1; transform:translateY(0);}
}

.stat-card {
    animation: fadeIn 0.6s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .stat-card {
        margin-bottom: 20px;
    }
}
</style>

<div class="row g-3">

    <!-- Products -->
    <div class="col-md-4">
        <div class="stat-card">
            <small><i class="fa fa-box"></i> Products</small>
            <h2><?= $totProducts ?></h2>
            <i class="fa-solid fa-box-open stat-icon-big"></i>
        </div>
    </div>

    <!-- Sales -->
    <div class="col-md-4">
        <div class="stat-card">
            <small><i class="fa fa-cart-shopping"></i> Sales</small>
            <h2><?= $totSales ?></h2>
            <i class="fa-solid fa-bag-shopping stat-icon-big"></i>
        </div>
    </div>

    <!-- Customers -->
    <div class="col-md-4">
        <div class="stat-card">
            <small><i class="fa fa-users"></i> Customers</small>
            <h2><?= $totCustomers ?></h2>
            <i class="fa-solid fa-user-group stat-icon-big"></i>
        </div>
    </div>

</div>

<div class="mt-4">

    <h5 class="section-title">
        <i class="fa fa-boxes-stacked"></i> Latest Products
    </h5>

    <div class="table-responsive">
        <table class="table table-striped table-sm rounded-3 overflow-hidden">
            <thead>
                <tr>
                    <th><i class="fa fa-barcode"></i> SKU</th>
                    <th><i class="fa fa-tag"></i> Name</th>
                    <th><i class="fa fa-dollar-sign"></i> Price</th>
                    <th><i class="fa fa-cubes"></i> Stock</th>
                </tr>
            </thead>

            <tbody>
                <?php 
                $rows = $pdo->query("SELECT sku,name,price,stock FROM products ORDER BY created_at DESC LIMIT 8")->fetchAll(); 
                foreach($rows as $r): 
                ?>
                <tr>
                    <td><?= htmlspecialchars($r['sku']) ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= number_format($r['price'],2) ?></td>
                    <td><?= (int)$r['stock'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
