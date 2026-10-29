
<?php
require '../config/database.php';
require '../includes/auth.php';

requireRole('admin');

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { checkCsrf(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    if (!in_array($_POST['role'] ?? '', ['client', 'master', 'admin'], true) ||
        !filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ||
        trim($_POST['name'] ?? '') === '' || strlen($_POST['password'] ?? '') < 6) {
        $msg = 'Enter valid user details (password at least 6 characters).';
    } else {
    try {
        $st = $pdo->prepare(
            'INSERT INTO users(name,email,password,role)
             VALUES(?,?,?,?)'
        );

        $st->execute([
            trim($_POST['name']),
            trim($_POST['email']),
            password_hash($_POST['password'], PASSWORD_DEFAULT),
            $_POST['role']
        ]);

        $msg = 'User created.';

    } catch (PDOException $e) {
        $msg = 'Could not create user. Email may already exist.';
    }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = (int)$_POST['id'];

    if ($id !== (int)$_SESSION['user']['id']) {
        $st = $pdo->prepare(
            'DELETE FROM users WHERE id=?'
        );

        try {
            $st->execute([$id]);

        } catch (PDOException $e) {
            $msg = 'User has related bookings/services and cannot be deleted.';
        }
    }
}

$users = $pdo->query(
    'SELECT id,name,email,role,created_at
     FROM users
     ORDER BY role,name'
)->fetchAll();

$title = 'Users';

include '../includes/header.php';
?>

<h1>Users</h1>
<a class="btn" href="/beauty-service/admin/dashboard.php">
    ← Back to Dashboard
</a>

<?php if ($msg): ?>
    <div class="msg"><?= e($msg) ?></div>
<?php endif; ?>

<div class="panel">
    <h2>Add user</h2>

    <form method="post"><?= csrfField() ?>
        <input type="hidden" name="create" value="1">

        <div class="field">
            <label>Name</label>
            <input name="name" required>
        </div>

        <div class="field">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="field">
            <label>Password</label>
            <input type="password" name="password" minlength="6" required>
        </div>

        <div class="field">
            <label>Role</label>
            <select name="role">
                <option>client</option>
                <option>master</option>
                <option>admin</option>
            </select>
        </div>

        <button>Add user</button>
    </form>
</div>

<div class="table-wrap"><table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th></th>
    </tr>

    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= e($u['name']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['role']) ?></td>

            <td>
                <?php if ($u['id'] != (int)$_SESSION['user']['id']): ?>
                    <form method="post"><?= csrfField() ?>
                        <input type="hidden" name="delete" value="1">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button class="btn danger">Delete</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table></div>

<?php include '../includes/footer.php'; ?>
