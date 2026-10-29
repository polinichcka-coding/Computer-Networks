<?php
require '../config/database.php';
require '../includes/auth.php';
requireRole('master');

$stmt = $pdo->prepare(
    'SELECT b.*, s.name AS service_name, u.name AS client_name
     FROM bookings b
     JOIN services s ON s.id = b.service_id
     JOIN users u ON u.id = b.client_id
     WHERE b.master_id = ?
     ORDER BY b.booking_date, b.booking_time'
);
$stmt->execute([$_SESSION['user']['id']]);
$bookings = $stmt->fetchAll();

$title = 'Appointments';
include '../includes/header.php';
?>

<h1>My appointments</h1>

<div class="table-wrap">
    <table>
        <tr>
            <th>Client</th>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?= e($booking['client_name']) ?></td>
                <td><?= e($booking['service_name']) ?></td>
                <td><?= e($booking['booking_date']) ?></td>
                <td><?= e(substr($booking['booking_time'], 0, 5)) ?></td>
                <td><?= e($booking['status']) ?></td>
                <td>
                    <?php if ($booking['status'] === 'pending'): ?>
                        <form method="post" action="update_booking.php">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int)$booking['id'] ?>">
                            <button name="status" value="confirmed">Accept</button>
                            <button class="danger" name="status" value="rejected">Reject</button>
                        </form>
                    <?php elseif ($booking['status'] === 'confirmed'): ?>
                        <form method="post" action="update_booking.php">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int)$booking['id'] ?>">
                            <button name="status" value="completed">Complete</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
