
<?php
require '../config/database.php';
require '../includes/auth.php';

requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') { checkCsrf(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mid = (int)$_POST['master_id'];

    try {
        $pdo->beginTransaction();
        $pdo->prepare('DELETE FROM master_services WHERE master_id=?')->execute([$mid]);
        $ins = $pdo->prepare('INSERT INTO master_services(master_id,service_id) VALUES(?,?)');
        foreach ($_POST['services'] ?? [] as $sid) {
            $ins->execute([$mid, (int)$sid]);
        }
        $pdo->commit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = 'Could not save master services.';
    }
}

$masters = $pdo->query(
    "SELECT id,name FROM users WHERE role='master' ORDER BY name"
)->fetchAll();

$services = $pdo->query(
    'SELECT id,name FROM services ORDER BY name'
)->fetchAll();

$links = $pdo->query(
    'SELECT master_id,service_id FROM master_services'
)->fetchAll();

$map = [];

foreach ($links as $l) {
    $map[$l['master_id']][] = $l['service_id'];
}

$title = 'Master Services';
include '../includes/header.php';
?>

<h1>Master services</h1>
<?php if (!empty($error)): ?><div class="msg error"><?= e($error) ?></div><?php endif; ?>

<p class="muted">
    Choose which services each master can perform.
</p>

<?php foreach ($masters as $m): ?>

    <div class="card">

        <h3><?= e($m['name']) ?></h3>

        <form method="post"><?= csrfField() ?>

            <input
                type="hidden"
                name="master_id"
                value="<?= $m['id'] ?>"
            >

            <?php foreach ($services as $s): ?>

                <label>
                    <input
                        style="width:auto"
                        type="checkbox"
                        name="services[]"
                        value="<?= $s['id'] ?>"
                        <?= in_array(
                            $s['id'],
                            $map[$m['id']] ?? []
                        ) ? 'checked' : '' ?>
                    >

                    <?= e($s['name']) ?>
                </label>

                <br>

            <?php endforeach; ?>

            <br>

            <button>Save services</button>

        </form>

    </div>

<?php endforeach; ?>

<?php include '../includes/footer.php'; ?>
