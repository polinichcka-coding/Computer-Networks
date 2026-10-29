
<?php
require '../config/database.php';
require '../includes/auth.php';

requireRole('admin');

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { checkCsrf(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {

    $id = (int)($_POST['id'] ?? 0);

    $data = [
        trim($_POST['name']),
        trim($_POST['description']),
        (float)$_POST['price'],
        (int)$_POST['duration']
    ];

    if ($id) {
        $st = $pdo->prepare(
            'UPDATE services
             SET name=?, description=?, price=?, duration=?
             WHERE id=?'
        );

        $st->execute([...$data, $id]);

    } else {
        $st = $pdo->prepare(
            'INSERT INTO services(name,description,price,duration)
             VALUES(?,?,?,?)'
        );

        $st->execute($data);
    }

    $msg = 'Service saved.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {

    try {
        $st = $pdo->prepare(
            'DELETE FROM services WHERE id=?'
        );

        $st->execute([(int)$_POST['id']]);

    } catch (PDOException $e) {
        $msg = 'Service is already used in bookings and cannot be deleted.';
    }
}

$edit = null;

if (isset($_GET['edit'])) {
    $st = $pdo->prepare(
        'SELECT * FROM services WHERE id=?'
    );

    $st->execute([(int)$_GET['edit']]);
    $edit = $st->fetch();
}

$services = $pdo->query(
    'SELECT * FROM services ORDER BY name'
)->fetchAll();

$title = 'Services';
include '../includes/header.php';
?>

<h1>Services</h1>

<?php if ($msg): ?>
    <div class="msg"><?= e($msg) ?></div>
<?php endif; ?>

<div class="panel">

    <h2><?= $edit ? 'Edit' : 'Add' ?> service</h2>

    <form method="post"><?= csrfField() ?>

        <input type="hidden" name="save" value="1">

        <input type="hidden" name="id"
               value="<?= e($edit['id'] ?? '') ?>">

        <div class="field">
            <label>Name</label>
            <input name="name"
                   value="<?= e($edit['name'] ?? '') ?>" required>
        </div>

        <div class="field">
            <label>Description</label>
            <textarea name="description"><?= e($edit['description'] ?? '') ?></textarea>
        </div>

        <div class="field">
            <label>Price (€)</label>
            <input type="number" step="0.01" min="0"
                   name="price"
                   value="<?= e($edit['price'] ?? '') ?>" required>
        </div>

        <div class="field">
            <label>Duration (minutes)</label>
            <input type="number" min="1"
                   name="duration"
                   value="<?= e($edit['duration'] ?? '') ?>" required>
        </div>

        <button>Save</button>

    </form>
</div>

<div class="table-wrap"><table>
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Duration</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($services as $s): ?>
        <tr>
            <td><?= e($s['name']) ?></td>
            <td>€<?= number_format($s['price'], 2) ?></td>
            <td><?= $s['duration'] ?> min</td>

            <td class="actions">

                <a class="btn light" href="?edit=<?= $s['id'] ?>">
                    Edit
                </a>

                <form method="post"><?= csrfField() ?>
                    <input type="hidden" name="delete" value="1">
                    <input type="hidden" name="id"
                           value="<?= $s['id'] ?>">
                    <button class="btn danger">Delete</button>
                </form>

            </td>
        </tr>
    <?php endforeach; ?>
</table></div>

<?php include '../includes/footer.php'; ?>
