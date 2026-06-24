<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';
if (isset($_SESSION['user_id'])) header('Location: ' . $BASE_URL . '/dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Enter username & password';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: ' . $BASE_URL . '/dashboard.php'); exit;
        } else {
            $error = 'Invalid credentials';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ---------- BODY ---------- */
body {
    background: linear-gradient(135deg, #e8dbcb, #51733f);
    min-height: 100vh;
    margin: 0;
    font-family: 'Poppins', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* ---------- LOGIN CARD ---------- */
.login-card {
    border-radius: 25px;
    padding: 40px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(15px);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
    animation: fadeIn 0.7s ease;
    width: 100%;
    max-width: 400px;
    color: #51733f;
    border: 1px solid rgba(255,255,255,0.3);
    transition: all 0.3s ease;
}

.login-card:hover {
    box-shadow: 0 20px 50px rgba(0,0,0,0.35);
    transform: translateY(-3px);
}

/* ---------- HEADINGS ---------- */
.login-card h3 {
    text-align: center;
    margin-bottom: 30px;
    font-weight: 700;
    color: #51733f;
}

/* ---------- INPUTS ---------- */
.input-group-custom {
    position: relative;
    margin-bottom: 20px;
}

.form-control {
    height: 50px;
    border-radius: 15px;
    border: 1px solid #51733f;
    padding-left: 45px;
    background: rgba(232,219,203,0.7);
    color: #51733f;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #51733f;
    box-shadow: 0 0 10px rgba(81,115,63,0.5);
    background: rgba(232,219,203,0.9);
    color: #51733f;
}

/* ---------- ICONS ---------- */
.input-icon {
    position: absolute;
    left: 15px;
    top: 14px;
    color: #51733f;
    font-size: 18px;
    transition: all 0.3s ease;
}

.form-control:focus + .input-icon {
    color: #3e5228;
}

/* ---------- BUTTON ---------- */
button {
    height: 50px;
    border-radius: 15px;
    background: rgba(81,115,63,0.9);
    color: #e8dbcb;
    font-size: 17px;
    font-weight: 700;
    border: none;
    transition: all 0.3s ease;
    backdrop-filter: blur(8px);
}

button:hover {
    background: rgba(65,90,50,0.95);
    transform: scale(1.05);
    box-shadow: 0 10px 25px rgba(0,0,0,0.35);
}

/* ---------- ALERT ---------- */
.alert {
    text-align: center;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.3);
    color: #51733f;
    font-weight: 500;
}

/* ---------- FOOTER ---------- */
.login-footer {
    text-align: center;
    margin-top: 18px;
    font-size: 13px;
    color: #ffffff;
    opacity: 0.85;
}

/* ---------- ANIMATION ---------- */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(25px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>

<div class="login-card">

    <h3>Welcome</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="input-group-custom">
            <input name="username" class="form-control" placeholder="Enter Username">
            <i class="fa fa-user input-icon"></i>
        </div>

        <div class="input-group-custom">
            <input name="password" type="password" class="form-control" placeholder="Enter Password">
            <i class="fa fa-lock input-icon"></i>
        </div>

        <div class="d-grid">
            <button type="submit"><i class="fa fa-sign-in-alt"></i> Login</button>
        </div>
    </form>

    <p class="login-footer">
        Run <code>setup.php</code> once to create admin if not present.
    </p>

</div>

</body>
</html>
