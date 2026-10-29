<?php
session_start();
$conn = new mysqli("localhost", "root", "", "stud1");
$conn->set_charset("utf8mb4");

function h($v) { return htmlspecialchars($v, ENT_QUOTES, "UTF-8"); }

if (isset($_POST["login"])) {
    $stmt = $conn->prepare("SELECT * FROM polina_sevastianova_users WHERE username = ?");
    $stmt->bind_param("s", $_POST["username"]);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($_POST["password"], $user["password"])) {
        $_SESSION["username"] = $user["username"];
        $_SESSION["email"] = $user["email"];
    }
    header("Location: index.php"); exit;
}

if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: index.php"); exit;
}

if (isset($_POST["delete_id"]) && isset($_SESSION["username"])) {
    $stmt = $conn->prepare("DELETE FROM polina_sevastianova_lab WHERE id = ? AND studentname = ?");
    $stmt->bind_param("is", $_POST["delete_id"], $_SESSION["username"]);
    $stmt->execute();
    header("Location: index.php"); exit;
}

if (isset($_POST["send"]) && isset($_SESSION["username"]) && trim($_POST["message"]) !== "") {
    $public = isset($_POST["public"]) ? 1 : 0;
    $stmt = $conn->prepare("INSERT INTO polina_sevastianova_lab (created, ip, studentname, email, message, public) VALUES (NOW(), ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $_SERVER["REMOTE_ADDR"], $_SESSION["username"], $_SESSION["email"], trim($_POST["message"]), $public);
    $stmt->execute();
    header("Location: index.php"); exit;
}

$filter = $_GET["filter"] ?? "all";
$logged = isset($_SESSION["username"]);

if ($filter !== "all") {
    $sql = $logged 
        ? "SELECT * FROM polina_sevastianova_lab WHERE studentname = ? ORDER BY id DESC"
        : "SELECT * FROM polina_sevastianova_lab WHERE studentname = ? AND public = 1 ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = $logged 
        ? "SELECT * FROM polina_sevastianova_lab ORDER BY id DESC"
        : "SELECT * FROM polina_sevastianova_lab WHERE public = 1 ORDER BY id DESC";
    $result = $conn->query($sql);
}
$users = $conn->query("SELECT DISTINCT studentname FROM polina_sevastianova_lab");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Guestbook</title>
<style>
    body { font-family: Arial; max-width: 800px; margin: 30px auto; background: #f5f5f5; padding: 20px; }
    .entry { background: #e9e9e9; padding: 12px; margin-bottom: 8px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
    form { background: #fff; padding: 20px; margin-top: 20px; border-radius: 6px; }
    input, textarea, select { width: 100%; padding: 8px; margin: 6px 0 12px; box-sizing: border-box; }
    button { padding: 8px 16px; cursor: pointer; }
</style>
</head>
<body>

<h1>Guestbook</h1>

<?php while($row = $result->fetch_assoc()): ?>
    <div class="entry">
        <div>
            <b><?= h($row["studentname"]) ?></b> (<?= h($row["email"]) ?>)<br>
            <small><?= h($row["created"]) ?> [<?= h($row["ip"]) ?>]</small>
            <p><?= nl2br(h($row["message"])) ?></p>
        </div>
        <?php if(isset($_SESSION["username"]) && $_SESSION["username"] === $row["studentname"]): ?>
            <form method="POST" style="margin:0; padding:0; background:none;">
                <input type="hidden" name="delete_id" value="<?= $row["id"] ?>">
                <button type="submit";  border:none; padding:5px 10px;>Delete</button>
            </form>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

<?php if(isset($_SESSION["username"])): ?>
    <p>Logged in as: <b><?= h($_SESSION["username"]) ?></b></p>
    <form method="POST">
        Message: <textarea name="message" required></textarea>
        <label><input type="checkbox" name="public" value="1" checked> Public</label><br>
        <button type="submit" name="send">Send</button>
    </form>
    <form method="POST" style="background:none; padding:0; margin-top:10px;"><button type="submit" name="logout">Logout</button></form>
<?php else: ?>
    <form method="POST">
        <h3>Login</h3>
        Username: <input type="text" name="username" required>
        Password: <input type="password" name="password" required>
        <button type="submit" name="login">Login</button>
    </form>

<form method="GET">
    Filter by user: 
    <select name="filter" onchange="this.form.submit()">
        <option value="all">All</option>
        <?php while($u = $users->fetch_assoc()): ?>
            <option value="<?= h($u['studentname']) ?>" <?= $filter === $u['studentname'] ? 'selected' : '' ?>><?= h($u['studentname']) ?></option>
        <?php endwhile; ?>
    </select>
</form>

<?php endif; ?>

</body>
</html>
<?php $conn->close(); ?>