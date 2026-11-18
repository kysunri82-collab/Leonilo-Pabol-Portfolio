
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
    $hasEnvironmentConfiguration = true;

    foreach ($environmentValues as $value) {
        if ($value === false || trim($value) === "") {
            $hasEnvironmentConfiguration = false;
            break;
        }
    }

    if ($hasEnvironmentConfiguration) {
        $host = trim($environmentValues["DB_HOST"]);
        $dbname = trim($environmentValues["DB_NAME"]);
        $username = trim($environmentValues["DB_USER"]);
        $password = $environmentValues["DB_PASSWORD"];
    } else {
        $configPath = __DIR__ . '/../private_config/db_config.php';

        if (!is_file($configPath) || !is_readable($configPath)) {
            http_response_code(500);
            die("Database configuration is incomplete.");
        }

        try {
            $privateDatabaseConfig = require $configPath;
        } catch (Throwable $e) {
            http_response_code(500);
            die("Database configuration is incomplete.");
        }

        if (
            !is_array($privateDatabaseConfig) ||
            !isset(
                $privateDatabaseConfig["host"],
                $privateDatabaseConfig["database"],
                $privateDatabaseConfig["username"],
                $privateDatabaseConfig["password"],
                $privateDatabaseConfig["port"]
            ) ||
            !is_string($privateDatabaseConfig["host"]) ||
            !is_string($privateDatabaseConfig["database"]) ||
            !is_string($privateDatabaseConfig["username"]) ||
            !is_string($privateDatabaseConfig["password"]) ||
            (!is_string($privateDatabaseConfig["port"]) && !is_int($privateDatabaseConfig["port"]))
        ) {
            http_response_code(500);
            die("Database configuration is incomplete.");
        }

        $host = trim($privateDatabaseConfig["host"]);
        $dbname = trim($privateDatabaseConfig["database"]);
        $username = trim($privateDatabaseConfig["username"]);
        $password = $privateDatabaseConfig["password"];

        if (
            $host === "" ||
            $dbname === "" ||
            $username === "" ||
            trim($password) === "" ||
            $password === "REPLACE_WITH_INFINITYFREE_PASSWORD"
        ) {
            http_response_code(500);
            die("Database configuration is incomplete.");
        }

        if ($databasePortValue === false) {
            $databasePortValue = $privateDatabaseConfig["port"];
        }
    }
}

if ($databasePortValue === false) {
    $databasePortValue = 3306;
}

if (
    (!is_string($databasePortValue) && !is_int($databasePortValue)) ||
    !preg_match('/^\d+$/D', (string) $databasePortValue) ||
    (int) $databasePortValue < 1 ||
    (int) $databasePortValue > 65535
) {
    http_response_code(500);
    die("Database port configuration is invalid.");
}
$databasePort = (int) $databasePortValue;

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
