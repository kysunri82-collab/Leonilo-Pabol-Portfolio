<?php
session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax'
]);

session_start();
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (
    !isset($_POST["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    $error = "Invalid form submission. Please try again.";
} else {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $sql = "SELECT * FROM admins
            WHERE username = :username
            OR email = :email
            LIMIT 1";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":username" => $username,
        ":email" => $username


        
    ]);

    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin["password"])) {

        session_regenerate_id(true);

        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["admin_username"] = $admin["username"];

        header("Location: dashboard.php");
        exit;

    } else {
        $error = "Invalid username or password.";
    }
}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Leonilo Pabol</title>
</head>
<body>

    <h1>Administrator Login</h1>
    <p>Welcome back! Please log in to manage your portfolio.</p>

    <form method="POST" action="login.php">

        <label for="username">Username or Email</label>
        <input
            type="text"
            id="username"
            name="username"
            required
        >

        <br><br>

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <br><br>

        <button type="submit" name="login">Login</button>
<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, 'UTF-8') ?>"
>
    </form>

    <?php if ($error !== ""): ?>
    <p style="color: red;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

</body>
</html>
