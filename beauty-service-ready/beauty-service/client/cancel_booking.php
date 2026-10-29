<?php
require '../config/database.php';
require '../includes/auth.php';
requireRole('client');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();

    $bookingId = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare(
        "UPDATE bookings SET status = 'cancelled'
         WHERE id = ? AND client_id = ?
         AND status IN ('pending', 'confirmed')"
    );
    $stmt->execute([$bookingId, $_SESSION['user']['id']]);
}

header('Location: bookings.php');
exit;
