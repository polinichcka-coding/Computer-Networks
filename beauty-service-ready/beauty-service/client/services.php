<?php
require '../config/database.php';
require '../includes/auth.php';
requireRole('client');

$services = $pdo->query('SELECT * FROM services ORDER BY name')->fetchAll();
$title = 'Services';
include '../includes/header.php';
?>

<h1>Services</h1>

<div class="grid">
    <?php foreach ($services as $service): ?>
        <div class="card">
            <h2><?= e($service['name']) ?></h2>
            <p><?= e($service['description']) ?></p>
            <p><?= (int)$service['duration'] ?> minutes</p>
            <p>€<?= number_format($service['price'], 2) ?></p>
            <a class="btn" href="book.php?service_id=<?= (int)$service['id'] ?>">Book</a>
        </div>
    <?php endforeach; ?>
</div>

<?php include '../includes/footer.php'; ?>
