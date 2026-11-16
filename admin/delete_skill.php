<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';

// Allow POST requests only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: skills.php");
    exit;
}

// Validate CSRF token
$token = $_POST["csrf_token"] ?? "";

if (!validateCsrfToken($token)) {
    die("Invalid security token. Please refresh the page.");
}

// Validate skill ID
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid skill ID.");
}

// Delete skill using a prepared statement
try {
    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->execute([$id]);
} catch (Throwable $error) {
    // Keep database errors out of the response shown to visitors.
}

header("Location: skills.php");
exit;
?>
