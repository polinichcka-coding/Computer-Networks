
<?php
require '../config/database.php';
require '../includes/auth.php';

requireRole('admin');

$stats = [
    'users' => $pdo->query(
        'SELECT COUNT(*) FROM users'
    )->fetchColumn(),

    'services' => $pdo->query(
        'SELECT COUNT(*) FROM services'
    )->fetchColumn(),

    'bookings' => $pdo->query(
        'SELECT COUNT(*) FROM bookings'
    )->fetchColumn()
];

$title = 'Admin Dashboard';

include '../includes/header.php';
?>

<h1>Admin dashboard</h1>

<div class="grid">

    <div class="card">
        <h2><?= $stats['users'] ?></h2>
        <p>Users</p>
        <a class="btn" href="users.php">Manage</a>
    </div>

    <div class="card">
        <h2><?= $stats['services'] ?></h2>
        <p>Services</p>
        <a class="btn" href="services.php">Manage</a>
    </div>

    <div class="card">
        <h2><?= $stats['bookings'] ?></h2>
        <p>Bookings</p>
        <a class="btn" href="bookings.php">View</a>
    </div>
    <div class="card">
        <h2>Master services</h2>
        <p>Assign services to your specialists.</p>
        <a class="btn" href="masters.php">Manage</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
