<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['user_id'])) header('Location: ' . $BASE_URL . '/login.php');

$products = $pdo->query("SELECT id, name, price, stock FROM products ORDER BY name")->fetchAll();
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_ids = $_POST['product_id'] ?? [];
    $qtys = $_POST['qty'] ?? [];
    $customer_id = !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $paid_amount = floatval($_POST['paid_amount'] ?? 0);

    $items = []; $total = 0;
    foreach ($product_ids as $i => $pid) {
        $pid = intval($pid);
        $qty = intval($qtys[$i]);
        if ($qty <= 0) continue;
        $stmt = $pdo->prepare("SELECT id, price, stock FROM products WHERE id = ?");
        $stmt->execute([$pid]);
        $p = $stmt->fetch();
        if (!$p) continue;
        if ($qty > $p['stock']) { $error = "Qty for product {$p['id']} exceeds stock"; break; }
        $subtotal = $p['price'] * $qty;
        $total += $subtotal;
        $items[] = ['product_id'=>$pid,'qty'=>$qty,'price'=>$p['price'],'subtotal'=>$subtotal];
    }

    if (empty($items)) $error = 'No items selected';
    if ($paid_amount < $total) $error = 'Paid amount less than total';

    if (empty($error)) {
        try {
            $pdo->beginTransaction();
            $ins = $pdo->prepare("INSERT INTO sales (customer_id,total_amount,paid_amount,change_amount,payment_method,created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $change = $paid_amount - $total;
            $ins->execute([$customer_id, $total, $paid_amount, $change, $payment_method, $_SESSION['user_id']]);
            $sale_id = $pdo->lastInsertId();

            $si = $pdo->prepare("INSERT INTO sale_items (sale_id,product_id,qty,price,subtotal) VALUES (?, ?, ?, ?, ?)");
            $up = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            foreach ($items as $it) {
                $si->execute([$sale_id,$it['product_id'],$it['qty'],$it['price'],$it['subtotal']]);
                $up->execute([$it['qty'],$it['product_id']]);
            }
            $pdo->commit();
            header('Location: ' . $BASE_URL . '/sale_receipt.php?id=' . $sale_id);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Transaction failed: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #e8dbcb;
    color: #51733f;
    padding: 20px;
}
h4 {
    font-weight:700;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
}
.alert {
    background:#51733f;
    color:#e8dbcb;
    padding:12px 18px;
    border-radius:12px;
    margin-bottom:20px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}
.table-responsive {
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
    background: rgba(81,115,63,0.05);
}
.table {
    width:100%;
    border-collapse:separate;
    border-spacing:0;
    backdrop-filter:blur(6px);
}
.table th, .table td {
    padding:14px 18px;
    font-weight:500;
    vertical-align:middle;
}
.table thead {
    background:#51733f;
    color:#e8dbcb;
    font-weight:700;
}
.table-striped tbody tr:nth-child(odd) {
    background: rgba(81,115,63,0.08);
}
.table-striped tbody tr:nth-child(even) {
    background: rgba(81,115,63,0.12);
}
.table tbody tr:hover {
    background: rgba(81,115,63,0.25);
    transform:scale(1.01);
    transition:0.3s;
}
.form-control {
    border-radius:12px;
    border:1px solid #51733f;
    padding:6px 10px;
}
.form-control:focus {
    outline:none;
    box-shadow:0 0 8px rgba(81,115,63,0.6);
}
.btn {
    border-radius:12px;
    font-weight:600;
    transition:all 0.3s ease;
}
.btn-success {
    background:#51733f;
    color:#e8dbcb;
    border:none;
}
.btn-success:hover {
    background:#3e5c2d;
    transform:scale(1.05);
}
.btn-primary {
    background:#51733f;
    color:#e8dbcb;
    border:none;
}
.btn-primary:hover {
    background:#3e5c2d;
    transform:scale(1.05);
}
.btn-secondary {
    background:#e8dbcb;
    color:#51733f;
    border:none;
}
.btn-secondary:hover {
    background:#d6c6b2;
    transform:scale(1.05);
}
</style>

<h4><i class="fa-solid fa-cart-shopping"></i> New Sale</h4>

<?php if ($error): ?>
<div class="alert"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" id="saleForm" onsubmit="return validateSaleForm()">
  <div class="row">
    <div class="col-md-8">
      <div class="table-responsive">
        <table class="table table-sm table-striped rounded-3 overflow-hidden">
          <thead>
            <tr>
              <th><i class="fa-solid fa-box"></i> Product</th>
              <th><i class="fa-solid fa-dollar-sign"></i> Price</th>
              <th><i class="fa-solid fa-hashtag"></i> Qty</th>
            </tr>
          </thead>
          <tbody id="productTable">
            <?php foreach ($products as $p): ?>
            <tr>
              <td>
                <input type="hidden" name="product_id[]" value="<?= $p['id'] ?>">
                <?= htmlspecialchars($p['name']) ?>
              </td>
              <td><?= number_format($p['price'],2) ?></td>
              <td><input class="form-control form-control-sm qty-input" name="qty[]" type="number" min="0" value="0"></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="col-md-4">
      <div class="mb-2">
        <label><i class="fa-solid fa-user"></i> Customer</label>
        <select name="customer_id" class="form-select">
          <option value="">Walk-in</option>
          <?php foreach ($customers as $c): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-2">
        <label><i class="fa-solid fa-credit-card"></i> Payment Method</label>
        <select name="payment_method" class="form-select">
          <option value="cash">Cash</option>
          <option value="card">Card</option>
          <option value="other">Other</option>
        </select>
      </div>

      <div class="mb-2">
        <label><i class="fa-solid fa-calculator"></i> Total</label>
        <input id="totalAmount" type="text" class="form-control" readonly value="0.00">
      </div>

      <div class="mb-2">
        <label><i class="fa-solid fa-money-bill-wave"></i> Paid Amount</label>
        <input id="paidAmount" name="paid_amount" type="number" step="0.01" class="form-control" value="0.00">
      </div>

      <div class="d-grid">
        <button class="btn btn-success"><i class="fa-solid fa-check"></i> Complete Sale</button>
      </div>
    </div>
  </div>
</form>

<script>
const prices = <?= json_encode(array_column($products,'price')) ?>;
document.addEventListener('input', e => { if (e.target && e.target.classList.contains('qty-input')) computeTotal(); });

function computeTotal(){
  const rows = document.querySelectorAll('#productTable tr');
  let total = 0;
  rows.forEach((r,i) => {
    const q = parseInt(r.querySelector('.qty-input').value) || 0;
    total += q * parseFloat(prices[i] || 0);
  });
  document.getElementById('totalAmount').value = total.toFixed(2);
}

function validateSaleForm(){
  computeTotal();
  const total = parseFloat(document.getElementById('totalAmount').value) || 0;
  const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
  if (total <= 0) { alert('Select at least one product'); return false; }
  if (paid < total) { alert('Paid is less than total'); return false; }
  return true;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
