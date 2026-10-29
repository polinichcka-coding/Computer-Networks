<?php
require '../config/database.php';
require '../includes/auth.php';
requireRole('client');

$title = 'Client Dashboard';
include '../includes/header.php';
?>

<h1>Client dashboard</h1>
<p>Welcome, <?= e($_SESSION['user']['name']) ?>!</p>

<div class="grid">
    <div class="card">
        <h2>Services</h2>
        <p>Choose a service and book an appointment.</p>
        <a class="btn" href="services.php">View services</a>
    </div>

    <div class="card">
        <h2>My bookings</h2>
        <p>View or cancel your appointments.</p>
        <a class="btn" href="bookings.php">My bookings</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
