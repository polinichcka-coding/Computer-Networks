<?php
require '../config/database.php';
require '../includes/auth.php';
requireRole('client');

$stmt = $pdo->prepare(
    'SELECT b.*, s.name AS service_name, s.price, u.name AS master_name
     FROM bookings b
     JOIN services s ON s.id = b.service_id
     JOIN users u ON u.id = b.master_id
     WHERE b.client_id = ?
     ORDER BY b.booking_date DESC, b.booking_time DESC'
);
$stmt->execute([$_SESSION['user']['id']]);
$bookings = $stmt->fetchAll();

$title = 'My Bookings';
include '../includes/header.php';
?>

<h1>My bookings</h1>

<?php if (isset($_GET['created'])): ?>
    <p class="msg">Booking created. Waiting for confirmation.</p>
<?php endif; ?>

<div class="table-wrap">
    <table>
        <tr>
            <th>Service</th>
            <th>Master</th>
            <th>Date</th>
            <th>Time</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?= e($booking['service_name']) ?></td>
                <td><?= e($booking['master_name']) ?></td>
                <td><?= e($booking['booking_date']) ?></td>
                <td><?= e(substr($booking['booking_time'], 0, 5)) ?></td>
                <td>€<?= number_format($booking['price'], 2) ?></td>
                <td><?= e($booking['status']) ?></td>
                <td>
                    <?php if (in_array($booking['status'], ['pending', 'confirmed'], true)): ?>
                        <form method="post" action="cancel_booking.php">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int)$booking['id'] ?>">
                            <button class="danger" type="submit">Cancel</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
