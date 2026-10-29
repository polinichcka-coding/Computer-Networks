<?php
require 'config/database.php';
require 'includes/auth.php';

$services = $pdo->query('SELECT * FROM services ORDER BY id')->fetchAll();
$title = 'Beauty Space';
include 'includes/header.php';
?>

<h1>Beauty Space</h1>
<p>Choose a service and book an appointment.</p>

<?php if (empty($_SESSION['user'])): ?>
    <a class="btn" href="login.php">Login to book</a>
<?php elseif ($_SESSION['user']['role'] === 'client'): ?>
    <a class="btn" href="client/services.php">Book now</a>
<?php else: ?>
    <a class="btn" href="<?= e($_SESSION['user']['role']) ?>/dashboard.php">Dashboard</a>
<?php endif; ?>

<h2>Services</h2>
<div class="grid">
    <?php foreach ($services as $service): ?>
        <div class="card">
            <h3><?= e($service['name']) ?></h3>
            <p><?= e($service['description']) ?></p>
            <p><?= (int)$service['duration'] ?> minutes</p>
            <p>€<?= number_format($service['price'], 2) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
