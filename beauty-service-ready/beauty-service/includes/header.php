<?php
require_once __DIR__ . '/auth.php';

$role = $_SESSION['user']['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Beauty Space') ?> | Beauty Space</title>
    <link rel="stylesheet" href="/beauty-service/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <nav class="site-nav">
        <a class="brand" href="/beauty-service/index.php">Beauty Space</a>

        <div class="nav-links">
            <a href="/beauty-service/index.php">Home</a>

            <?php if ($role === 'admin'): ?>
                <a href="/beauty-service/admin/dashboard.php">Dashboard</a>
                <a href="/beauty-service/admin/users.php">Users</a>
                <a href="/beauty-service/admin/services.php">Services</a>
                <a href="/beauty-service/admin/masters.php">Master services</a>
                <a href="/beauty-service/admin/bookings.php">Bookings</a>
            <?php elseif ($role === 'master'): ?>
                <a href="/beauty-service/master/dashboard.php">Dashboard</a>
                <a href="/beauty-service/master/bookings.php">Appointments</a>
            <?php elseif ($role === 'client'): ?>
                <a href="/beauty-service/client/dashboard.php">Dashboard</a>
                <a href="/beauty-service/client/services.php">Services</a>
                <a href="/beauty-service/client/bookings.php">My bookings</a>
            <?php endif; ?>
        </div>

        <div class="nav-account">
            <?php if ($role): ?>
                <span><?= e($_SESSION['user']['name']) ?> (<?= e($role) ?>)</span>
                <a href="/beauty-service/logout.php">Logout</a>
            <?php else: ?>
                <a href="/beauty-service/login.php">Login</a>
                <a href="/beauty-service/register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main>
