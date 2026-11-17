<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: achievements.php");
    exit;
}

$token = $_POST["csrf_token"] ?? "";

if (!validateCsrfToken($token)) {
    die("Invalid security token. Please refresh the page.");
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid achievement ID.");
}

try {
    require_once __DIR__ . '/../includes/db.php';
    $stmt = $pdo->prepare("DELETE FROM achievements WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() < 1) {
        header("Location: achievements.php?error=not_found");
        exit;
    }
} catch (Throwable $error) {
    header("Location: achievements.php?error=delete");
    exit;
}

header("Location: achievements.php?deleted=1");
exit;
?>
