
<?php
require '../config/database.php';
require '../includes/auth.php';

requireRole('admin');

$rows = $pdo->query(
    'SELECT
        b.*,
        s.name service_name,
        c.name client_name,
        m.name master_name
     FROM bookings b
     JOIN services s ON s.id = b.service_id
     JOIN users c ON c.id = b.client_id
     JOIN users m ON m.id = b.master_id
     ORDER BY b.booking_date DESC,
              b.booking_time DESC'
)->fetchAll();

$title = 'All Bookings';

include '../includes/header.php';
?>

<h1>All bookings</h1>

<div class="table-wrap"><table>
    <tr>
        <th>Client</th>
        <th>Master</th>
        <th>Service</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
    </tr>

    <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= e($r['client_name']) ?></td>
            <td><?= e($r['master_name']) ?></td>
            <td><?= e($r['service_name']) ?></td>
            <td><?= e($r['booking_date']) ?></td>
            <td><?= e(substr($r['booking_time'], 0, 5)) ?></td>
            <td>
                <span class="badge">
                    <?= e($r['status']) ?>
                </span>
            </td>
        </tr>
    <?php endforeach; ?>
</table></div>

<?php include '../includes/footer.php'; ?>
