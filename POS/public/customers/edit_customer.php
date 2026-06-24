<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $BASE_URL . "/login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

// Fetch customer
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch();

if (!$customer) {
    die("Customer not found!");
}

// Update customer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $up = $pdo->prepare("UPDATE customers SET name=?, phone=?, email=? WHERE id=?");
    $up->execute([$name, $phone, $email, $id]);

    header("Location: customers.php?msg=Customer updated successfully");
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Extra UI Styling -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    .edit-header {
        background: #495a4d;
        color: #e8dbcb;
        padding: 12px 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .edit-card {
        background: #ffffff;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        animation: fadeIn 0.5s ease-in-out;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #b8c2b8;
    }

    .btn-primary {
        background: #495a4d;
        border: none;
        transition: 0.3s;
    }
    .btn-primary:hover {
        background: #3d4d41;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #7d8b80;
        border: none;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="container mt-4">

    <div class="edit-header">
        <i class="fa-solid fa-user-pen fa-lg"></i>
        <h3 class="m-0">Edit Customer</h3>
    </div>

    <div class="edit-card">
        <form method="POST">

            <div class="mb-3">
                <label class="fw-bold">
                    <i class="fa-solid fa-user me-1"></i> Name
                </label>
                <input type="text" name="name" class="form-control" required 
                       value="<?= htmlspecialchars($customer['name']) ?>">
            </div>

            <div class="mb-3">
                <label class="fw-bold">
                    <i class="fa-solid fa-phone me-1"></i> Phone
                </label>
                <input type="text" name="phone" class="form-control" required 
                       value="<?= htmlspecialchars($customer['phone']) ?>">
            </div>

            <div class="mb-3">
                <label class="fw-bold">
                    <i class="fa-solid fa-envelope me-1"></i> Email
                </label>
                <input type="email" name="email" class="form-control" 
                       value="<?= htmlspecialchars($customer['email']) ?>">
            </div>

            <button class="btn btn-primary">
                <i class="fa-solid fa-check me-1"></i> Update
            </button>

            <a href="customers.php" class="btn btn-secondary">
                <i class="fa-solid fa-xmark me-1"></i> Cancel
            </a>

        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
