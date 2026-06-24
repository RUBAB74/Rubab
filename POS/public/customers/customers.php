<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $BASE_URL . "/login.php");
    exit;
}

// ADD CUSTOMER
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_customer'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    if ($name === '') $errors[] = "Name is required";

    if (!$errors) {
        $insert = $pdo->prepare("INSERT INTO customers (name, phone, email, address) VALUES (?, ?, ?, ?)");
        $insert->execute([$name, $phone, $email, $address]);
        header("Location: customers.php?msg=Customer Added Successfully");
        exit;
    }
}

include __DIR__ . '/../includes/header.php';

$msg = $_GET['msg'] ?? '';
$customers = $pdo->query("SELECT * FROM customers ORDER BY id DESC")->fetchAll();
?>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* BODY */
body { font-family: 'Poppins', sans-serif; background: #e8dbcb; color: #51733f; padding: 20px; }

/* HEADER */
h4 { font-weight: 700; display:flex; align-items:center; gap:10px; }

/* BUTTONS */
.btn { border-radius:10px; display:inline-flex; align-items:center; gap:6px; font-weight:600; }
.btn-success, .btn-primary, .btn-warning, .btn-danger { border:none; color:#fff; }
.btn-success, .btn-primary { background:#51733f; }
.btn-warning { background:#b48d42; }
.btn-danger { background:#8b3a3a; }
.btn:hover { transform:scale(1.05); }

/* ALERT */
.alert { background:#51733f; color:#fff; padding:12px; border-radius:10px; margin-bottom:15px; }

/* TABLE */
.table-responsive { border-radius:15px; overflow:hidden; box-shadow:0 8px 20px rgba(0,0,0,0.15); }
.table th { background:#51733f; color:#fff; }
.table td, .table th { vertical-align:middle; padding:12px; }

/* Hover */
.table tbody tr:hover { background:rgba(81,115,63,0.15); }

/* MODAL */
.modal-content { background:#51733f; color:#fff; border-radius:15px; }
.modal-body input {
    background:#e8dbcb;
    color:#51733f;
    border:none;
    border-radius:10px;
    padding:10px;
}
.modal-body input:focus {
    box-shadow:0 0 6px #fff;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="fa-solid fa-users"></i> Customers</h4>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomer">
        <i class="fa-solid fa-plus"></i> Add
    </button>
</div>

<?php if ($msg): ?>
<div class="alert"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th><i class="fa-solid fa-user"></i> Name</th>
                <th><i class="fa-solid fa-phone"></i> Phone</th>
                <th><i class="fa-solid fa-envelope"></i> Email</th>
                <th><i class="fa-solid fa-location-dot"></i> Address</th>
                <th><i class="fa-solid fa-gear"></i> Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($customers as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['phone']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['address']) ?></td>

                <td>
                    <!-- EDIT -->
                    <a href="edit_customer.php?id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fa-solid fa-pen"></i>
                    </a>

                    <!-- DELETE -->
                    <a href="delete_customer.php?id=<?= $c['id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this customer?');">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>
</div>

<!-- ADD CUSTOMER MODAL -->
<div class="modal fade" id="addCustomer">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5><i class="fa-solid fa-user-plus"></i> Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="add_customer" value="1">

                <?php if ($errors): ?>
                    <div class="alert"><?= implode("<br>", $errors) ?></div>
                <?php endif; ?>

                <input name="name" class="form-control" placeholder="Name" required>
                <input name="phone" class="form-control" placeholder="Phone">
                <input name="email" class="form-control" placeholder="Email">
                <input name="address" class="form-control" placeholder="Address">
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Close
                </button>
                <button class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
