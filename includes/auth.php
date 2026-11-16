
<?php

require_once __DIR__ . "/security.php";
startSecureSession();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$now = time();
$lastActivity = $_SESSION["admin_last_activity"] ?? $now;

if (!is_int($lastActivity) && !(is_string($lastActivity) && ctype_digit($lastActivity))) {
    $lastActivity = 0;
}

if ($now - (int) $lastActivity > 1800) {
    destroySecureSession();
    header("Location: login.php");
    exit;
}

$_SESSION["admin_last_activity"] = $now;
?>
