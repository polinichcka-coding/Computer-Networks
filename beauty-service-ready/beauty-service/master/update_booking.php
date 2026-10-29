<?php
require '../config/database.php';
require '../includes/auth.php';
requireRole('master');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();

    $bookingId = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $masterId = $_SESSION['user']['id'];

    if ($status === 'confirmed' || $status === 'rejected') {
        $stmt = $pdo->prepare(
            "UPDATE bookings SET status = ?
             WHERE id = ? AND master_id = ? AND status = 'pending'"
        );
        $stmt->execute([$status, $bookingId, $masterId]);
    }

    if ($status === 'completed') {
        $stmt = $pdo->prepare(
            "UPDATE bookings SET status = 'completed'
             WHERE id = ? AND master_id = ? AND status = 'confirmed'"
        );
        $stmt->execute([$bookingId, $masterId]);
    }
}

header('Location: bookings.php');
exit;
