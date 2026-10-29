<?php
require '../config/database.php';
require '../includes/auth.php';
requireRole('client');

$serviceId = (int)($_POST['service_id'] ?? $_GET['service_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM services WHERE id = ?');
$stmt->execute([$serviceId]);
$service = $stmt->fetch();

if (!$service) {
    exit('Service not found.');
}

$stmt = $pdo->prepare(
    "SELECT u.id, u.name
     FROM users u
     JOIN master_services ms ON ms.master_id = u.id
     WHERE ms.service_id = ? AND u.role = 'master'
     ORDER BY u.name"
);
$stmt->execute([$serviceId]);
$masters = $stmt->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();

    $masterId = (int)($_POST['master_id'] ?? 0);
    $date = $_POST['booking_date'] ?? '';
    $time = $_POST['booking_time'] ?? '';

    $validDate = DateTime::createFromFormat('!Y-m-d', $date);
    $validTime = DateTime::createFromFormat('!H:i', $time);

    if (!$validDate || $validDate->format('Y-m-d') !== $date ||
        !$validTime || $validTime->format('H:i') !== $time ||
        $date < date('Y-m-d') ||
        ($date === date('Y-m-d') && $time <= date('H:i'))) {
        $error = 'Choose a valid future date and time.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM master_services WHERE master_id = ? AND service_id = ?'
        );
        $stmt->execute([$masterId, $serviceId]);

        if (!$stmt->fetchColumn()) {
            $error = 'Choose a valid master.';
        } else {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM bookings
                 WHERE master_id = ? AND booking_date = ? AND booking_time = ?
                 AND status IN ('pending', 'confirmed')"
            );
            $stmt->execute([$masterId, $date, $time]);

            if ($stmt->fetchColumn()) {
                $error = 'This time is already booked.';
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO bookings
                     (client_id, master_id, service_id, booking_date, booking_time, status)
                     VALUES (?, ?, ?, ?, ?, 'pending')"
                );
                $stmt->execute([
                    $_SESSION['user']['id'],
                    $masterId,
                    $serviceId,
                    $date,
                    $time
                ]);

                header('Location: bookings.php?created=1');
                exit;
            }
        }
    }
}

$title = 'Book service';
include '../includes/header.php';
?>

<h1>Book <?= e($service['name']) ?></h1>

<?php if ($error): ?>
    <p class="msg error"><?= e($error) ?></p>
<?php endif; ?>

<?php if (!$masters): ?>
    <p>No masters are available for this service.</p>
<?php else: ?>
    <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="service_id" value="<?= $serviceId ?>">

        <label>Master</label>
        <select name="master_id" required>
            <option value="">Choose master</option>
            <?php foreach ($masters as $master): ?>
                <option value="<?= (int)$master['id'] ?>">
                    <?= e($master['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Date</label>
        <input type="date" name="booking_date" min="<?= date('Y-m-d') ?>" required>

        <label>Time</label>
        <input type="time" name="booking_time" required>

        <button type="submit">Create booking</button>
    </form>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
