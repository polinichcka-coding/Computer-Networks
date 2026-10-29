<?php
require 'config/database.php';

$accounts = [
    ['Administrator', 'admin@beauty.test', 'admin123', 'admin'],
    ['Sofia Master', 'sofia@beauty.test', 'master123', 'master'],
    ['Anna Client', 'anna@beauty.test', 'client123', 'client']
];

foreach ($accounts as $account) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$account[1]]);

    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $account[0],
            $account[1],
            password_hash($account[2], PASSWORD_DEFAULT),
            $account[3]
        ]);
    }
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute(['sofia@beauty.test']);
$masterId = $stmt->fetchColumn();

$stmt = $pdo->prepare(
    'INSERT IGNORE INTO master_services (master_id, service_id)
     SELECT ?, id FROM services'
);
$stmt->execute([$masterId]);
?>

<h1>Setup complete</h1>
<p>Delete setup.php after first use.</p>
<p>Admin: admin@beauty.test / admin123</p>
<p>Master: sofia@beauty.test / master123</p>
<p>Client: anna@beauty.test / client123</p>
<a href="login.php">Login</a>
