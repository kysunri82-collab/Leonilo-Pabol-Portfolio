<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';

// Allow POST requests only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: projects.php");
    exit;
}

// Validate CSRF token
$token = $_POST["csrf_token"] ?? "";

if (!validateCsrfToken($token)) {
    die("Invalid security token. Please refresh the page.");
}

// Validate project ID
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid project ID.");
}

// Delete project using a prepared statement
$stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
$stmt->execute([$id]);

header("Location: projects.php?deleted=1");
exit;
?>