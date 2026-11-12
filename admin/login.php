<?php

session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax'
]);

session_start();

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';

$csrfToken = generateCsrfToken();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!validateCsrfToken($_POST["csrf_token"] ?? null)) {

        $error = "Invalid form submission. Please try again.";

    } else {

        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";

        if ($username === "" || $password === "") {

            $error = "Invalid username or password.";

        } else {

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

            // Use the admin ID so username and email share one limit
            $loginIdentifier = $admin
                ? "admin:" . $admin["id"]
                : $username;

            if (isLoginLocked($pdo, $loginIdentifier)) {

                $error = "Too many failed attempts. Please try again in 15 minutes.";

            } elseif ($admin && password_verify($password, $admin["password"])) {

                clearLoginAttempts($pdo, $loginIdentifier);

                session_regenerate_id(true);

                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["admin_username"] = $admin["username"];

                header("Location: dashboard.php");
                exit;

            } else {

                recordFailedLogin($pdo, $loginIdentifier);

                if (isLoginLocked($pdo, $loginIdentifier)) {
                    $error = "Too many failed attempts. Please try again in 15 minutes.";
                } else {
                    $error = "Invalid username or password.";
                }
            }
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
    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    </form>

    <?php if ($error !== ""): ?>
    <p style="color: red;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

</body>
</html>
