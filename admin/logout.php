
<?php

require_once __DIR__ . '/../includes/security.php';
startSecureSession();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    exit("Logout must be submitted using POST.");
}

if (!validateCsrfToken($_POST["csrf_token"] ?? null)) {
    http_response_code(400);
    exit("Invalid security token. Please refresh the page.");
}

destroySecureSession();

header("Location: login.php");
exit;
