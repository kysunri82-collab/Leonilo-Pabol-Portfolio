
<?php
require_once __DIR__ . '/../includes/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>

    <h1>Admin Dashboard</h1>

    <h2>
        Welcome, <?= htmlspecialchars($_SESSION["admin_username"], ENT_QUOTES, 'UTF-8') ?>!
    </h2>

    <p>You are successfully logged in.</p>

    <a href="logout.php">Logout</a>

</body>
</html>
