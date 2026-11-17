
<?php

$appEnvironment = getenv("APP_ENV");
$appEnvironment = $appEnvironment === false
    ? ""
    : strtolower(trim($appEnvironment));

$remoteAddress = $_SERVER["REMOTE_ADDR"] ?? "";
$serverName = strtolower(trim((string) ($_SERVER["SERVER_NAME"] ?? "")));
// Development defaults are restricted to local requests using XAMPP's localhost host.
$isLocalXamppRequest = in_array($remoteAddress, ["127.0.0.1", "::1"], true) &&
    in_array($serverName, ["localhost", "127.0.0.1", "::1"], true);
$isDevelopment = $isLocalXamppRequest &&
    ($appEnvironment === "" || $appEnvironment === "development");

$environmentValues = [
    "DB_HOST" => getenv("DB_HOST"),
    "DB_NAME" => getenv("DB_NAME"),
    "DB_USER" => getenv("DB_USER"),
    "DB_PASSWORD" => getenv("DB_PASSWORD")
];
$databasePortValue = getenv("DB_PORT");

if ($databasePortValue === false) {
    $databasePort = 3306;
} elseif (
    !preg_match('/^\d+$/D', $databasePortValue) ||
    (int) $databasePortValue < 1 ||
    (int) $databasePortValue > 65535
) {
    http_response_code(500);
    die("Database port configuration is invalid.");
} else {
    $databasePort = (int) $databasePortValue;
}

if ($isDevelopment) {
    $host = $environmentValues["DB_HOST"] !== false && trim($environmentValues["DB_HOST"]) !== ""
        ? trim($environmentValues["DB_HOST"])
        : "localhost";
    $dbname = $environmentValues["DB_NAME"] !== false && trim($environmentValues["DB_NAME"]) !== ""
        ? trim($environmentValues["DB_NAME"])
        : "leonilo_portfolio";
    $username = $environmentValues["DB_USER"] !== false && trim($environmentValues["DB_USER"]) !== ""
        ? trim($environmentValues["DB_USER"])
        : "root";
    $password = $environmentValues["DB_PASSWORD"] !== false
        ? $environmentValues["DB_PASSWORD"]
        : "";
} else {
    foreach ($environmentValues as $name => $value) {
        if ($value === false || trim($value) === "") {
            http_response_code(500);
            die("Database configuration is incomplete.");
        }
    }

    $host = trim($environmentValues["DB_HOST"]);
    $dbname = trim($environmentValues["DB_NAME"]);
    $username = trim($environmentValues["DB_USER"]);
    $password = $environmentValues["DB_PASSWORD"];
}

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$databasePort;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die("Database connection failed.");
}
?>
