<?php
require '../config/database.php';
require '../includes/auth.php';
requireRole('master');

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM bookings WHERE master_id = ? AND status = 'pending'"
);
$stmt->execute([$_SESSION['user']['id']]);
$pending = $stmt->fetchColumn();

$title = 'Master Dashboard';
include '../includes/header.php';
?>

<h1>Master dashboard</h1>
<p>You have <?= (int)$pending ?> pending appointment(s).</p>
<a class="btn" href="bookings.php">View appointments</a>

<?php include '../includes/footer.php'; ?>
