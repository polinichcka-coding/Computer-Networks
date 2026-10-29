< !-- Admin: admin@beauty.test / admin123
Master: sofia@beauty.test / master123
Client: anna@beauty.test / client123 
http://localhost/beauty-service/ -- >
<?php
require 'config/database.php';
require 'includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        header('Location: ' . $user['role'] . '/dashboard.php');
        exit;
    }

    $error = 'Invalid email or password.';
}

$title = 'Login';
include 'includes/header.php';
?>

<h1>Login</h1>

<?php if (isset($_GET['registered'])): ?>
    <p class="msg">Registration complete. Please log in.</p>
<?php endif; ?>

<?php if ($error): ?>
    <p class="msg error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post">
    <?= csrfField() ?>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
</form>

<?php include 'includes/footer.php'; ?>
