<?php
require 'config/database.php';
require 'includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $error = 'Enter a name, valid email and password of at least 6 characters.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password, role)
                 VALUES (?, ?, ?, 'client')"
            );

            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $exception) {
            $error = 'This email is already registered.';
        }
    }
}

$title = 'Register';
include 'includes/header.php';
?>

<h1>Create account</h1>

<?php if ($error): ?>
    <p class="msg error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post">
    <?= csrfField() ?>

    <label>Name</label>
    <input type="text" name="name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" minlength="6" required>

    <button type="submit">Register</button>
</form>

<?php include 'includes/footer.php'; ?>
