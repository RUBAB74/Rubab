<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';
if (!isset($_SESSION['user_id'])) header('Location: ' . $BASE_URL . '/login.php');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ' . $BASE_URL . '/dashboard.php'); exit; }

$sale = $pdo->prepare("SELECT s.*, u.username, c.name AS customer_name FROM sales s LEFT JOIN users u ON s.created_by=u.id LEFT JOIN customers c ON s.customer_id=c.id WHERE s.id = ?");
$sale->execute([$id]);
$S = $sale->fetch();
if (!$S) { echo "Sale not found"; exit; }

$items = $pdo->prepare("SELECT si.*, p.name FROM sale_items si JOIN products p ON si.product_id = p.id WHERE si.sale_id = ?");
$items->execute([$id]);
$items = $items->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body { font-family:'Poppins',sans-serif; background:#e8dbcb; color:#51733f; padding:20px;}
.card { background:#fff; border-radius:20px; padding:25px; max-width:700px; margin:auto; box-shadow:0 8px 25px rgba(0,0,0,0.15);}
h4 { font-weight:700; margin-bottom:15px; display:flex; align-items:center; gap:10px;}
.table { width:100%; border-collapse: separate; border-spacing:0; }
.table th, .table td { padding:12px 15px; font-weight:500; vertical-align:middle; }
.table thead { background:#51733f; color:#e8dbcb; font-weight:700; border-radius:12px; }
.table tbody tr:nth-child(odd) { background: rgba(81,115,63,0.05); }
.table tbody tr:nth-child(even) { background: rgba(81,115,63,0.08); }
.table tbody tr:hover { background: rgba(81,115,63,0.15); transform: scale(1.01); transition:0.2s;}
.text-end p { margin:4px 0; font-weight:600;}
.btn { border-radius:12px; font-weight:600; padding:6px 14px; transition:0.3s; display:inline-flex; align-items:center; gap:6px;}
.btn-primary { background:#51733f; color:#e8dbcb; border:none; }
.btn-primary:hover { background:#3e5c2d; transform:scale(1.05);}
.btn-secondary { background:#e8dbcb; color:#51733f; border:none; }
.btn-secondary:hover { background:#d6c6b2; transform:scale(1.05);}
</style>

<div class="card">
  <h4><i class="fa-solid fa-receipt"></i> Receipt #<?= $S['id'] ?></h4>
  <p><i class="fa-solid fa-user-tie"></i> <strong>Cashier:</strong> <?= htmlspecialchars($S['username']) ?> | 
     <i class="fa-solid fa-user"></i> <strong>Customer:</strong> <?= htmlspecialchars($S['customer_name'] ?: 'Walk-in') ?></p>

  <div class="table-responsive">
    <table class="table table-sm rounded-3 overflow-hidden">
      <thead>
        <tr>
          <th><i class="fa-solid fa-box"></i> Item</th>
          <th><i class="fa-solid fa-hashtag"></i> Qty</th>
          <th><i class="fa-solid fa-money-bill-wave"></i> Price</th>
          <th><i class="fa-solid fa-calculator"></i> Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['name']) ?></td>
          <td><?= (int)$it['qty'] ?></td>
          <td><?= number_format($it['price'],2) ?></td>
          <td><?= number_format($it['subtotal'],2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="text-end mt-3">
    <p><i class="fa-solid fa-wallet"></i> <strong>Total:</strong> <?= number_format($S['total_amount'],2) ?></p>
    <p><i class="fa-solid fa-hand-holding-dollar"></i> <strong>Paid:</strong> <?= number_format($S['paid_amount'],2) ?> | 
       <i class="fa-solid fa-coins"></i> <strong>Change:</strong> <?= number_format($S['change_amount'],2) ?></p>
    <a class="btn btn-secondary" href="javascript:window.print()"><i class="fa-solid fa-print"></i> Print</a>
    <a class="btn btn-primary" href="<?= $BASE_URL ?>/dashboard.php"><i class="fa-solid fa-check"></i> Done</a>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
