<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: messages.php");
    exit;
}

$token = $_POST["csrf_token"] ?? "";

if (!validateCsrfToken($token)) {
    die("Invalid security token. Please refresh the page.");
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid message ID.");
}

try {
    require_once __DIR__ . '/../includes/db.php';
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$id]);
} catch (Throwable $error) {
    header("Location: messages.php?error=delete");
    exit;
}

header("Location: messages.php?deleted=1");
exit;
?>
