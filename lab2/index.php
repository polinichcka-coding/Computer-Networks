<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "stud";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed");
}

$conn->set_charset("utf8mb4");

$error = "";

if (isset($_POST["send"])) {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $message = trim($_POST["message"] ?? "");
    $ip = $_SERVER["REMOTE_ADDR"] ?? "unknown";

    if ($name != "" && filter_var($email, FILTER_VALIDATE_EMAIL)
        && $message != "") {

        $stmt = $conn->prepare(
            "INSERT INTO polina_sevastianova_lab
            (created, ip, studentname, email, message)
            VALUES (NOW(), ?, ?, ?, ?)"
        );

        $stmt->bind_param("ssss", $ip, $name, $email, $message);
        $stmt->execute();
        $stmt->close();

        header("Location: /lab2/");
        exit;

    } else {
        $error = "Please fill in all fields correctly.";
    }
}

if (isset($_POST["delete"])) {
    $id = (int)($_POST["id"] ?? 0);

    if ($id > 0) {
        $stmt = $conn->prepare(
            "DELETE FROM polina_sevastianova_lab WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        header("Location: /lab2/");
        exit;
    }
}


$result = $conn->query(
    "SELECT * FROM polina_sevastianova_lab
     ORDER BY created DESC, id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Guestbook</title>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 40px auto;
    padding: 0 20px;
    background: #f5f5f5;
}

h1 {
    margin-bottom: 25px;
}

.entry {
    display: grid;
    grid-template-columns: 1fr 1.5fr 1.5fr 2fr;
    gap: 15px;
    padding: 15px;
    margin-bottom: 10px;
    background: #e9e9e9;
    border-radius: 5px;
    overflow-wrap: anywhere;
}

.name {
    font-weight: bold;
}

form {
    background: white;
    padding: 25px;
    border-radius: 8px;
    margin-top: 20px;
}

label {
    display: block;
    font-weight: bold;
    margin-top: 15px;
    margin-bottom: 6px;
}

input, textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 10px;
    border: 1px solid #aaa;
    border-radius: 4px;
    font-size: 16px;
}

textarea {
    min-height: 100px;
}

button {
    margin-top: 15px;
    padding: 10px 20px;
    cursor: pointer;
}

@media (max-width: 600px) {
    .entry {
        grid-template-columns: 1fr 1fr;
    }
}
</style>

</head>

<body>

<h1>Guestbook</h1>

<?php while ($row = $result->fetch_assoc()): ?>

<div class="entry">

    <div class="name">
        <?= htmlspecialchars($row["studentname"]) ?>
    </div>

    <div>
        <?= htmlspecialchars($row["email"]) ?>
    </div>

    <div>
        <?= htmlspecialchars($row["created"]) ?>
        <br>
        (<?= htmlspecialchars($row["ip"]) ?>)
    </div>

    <div>
        <?= nl2br(htmlspecialchars($row["message"])) ?>
    </div>

    <form method="POST">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <button type="submit" name="delete">Delete</button>
    </form>
    

</div>

<?php endwhile; ?>

<?php if ($error != ""): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">

    <label>Name:</label>
    <input type="text" name="name" required maxlength="50">

    <label>Email:</label>
    <input type="email" name="email" required maxlength="50">

    <label>Message:</label>
    <textarea name="message" required></textarea>

    <button type="submit" name="send">Send</button>

</form>

</body>
</html>

<?php
$conn->close();
?>
