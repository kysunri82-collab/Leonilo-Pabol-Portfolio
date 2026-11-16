<?php

function startSecureSession(): void {
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    ini_set("session.use_strict_mode", "1");
    ini_set("session.use_only_cookies", "1");
    ini_set("session.use_trans_sid", "0");

    $httpsActive = (
        isset($_SERVER["HTTPS"]) &&
        $_SERVER["HTTPS"] !== "" &&
        strtolower((string) $_SERVER["HTTPS"]) !== "off"
    ) || (int) ($_SERVER["SERVER_PORT"] ?? 0) === 443;

    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/",
        "secure" => $httpsActive,
        "httponly" => true,
        "samesite" => "Lax"
    ]);

    session_start();
}

function destroySecureSession(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), "", [
            "expires" => time() - 42000,
            "path" => $params["path"] ?: "/",
            "domain" => $params["domain"],
            "secure" => $params["secure"],
            "httponly" => $params["httponly"],
            "samesite" => $params["samesite"] ?? "Lax"
        ]);
    }

    session_destroy();
}

// Generate a CSRF token
function generateCsrfToken() {
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}

// Validate a submitted CSRF token
function validateCsrfToken($token) {
    if (
        !isset($_SESSION["csrf_token"]) ||
        !is_string($token) ||
        $token === ""
    ) {
        return false;
    }

    return hash_equals($_SESSION["csrf_token"], $token);
}

// Generate a consistent hash for login attempt tracking
function getLoginIdentifierHash(string $identifier): string {
    return hash("sha256", strtolower(trim($identifier)));
}

// Check whether an administrator is temporarily locked out
function isLoginLocked(PDO $pdo, string $identifier): bool {

    $identifierHash = getLoginIdentifierHash($identifier);

    $sql = "SELECT
                CASE
                    WHEN locked_until IS NOT NULL
                    AND locked_until > NOW()
                    THEN 1 ELSE 0
                END AS is_locked,
                CASE
                    WHEN last_attempt IS NOT NULL
                    AND last_attempt <= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                    THEN 1 ELSE 0
                END AS window_expired,
                locked_until
            FROM login_attempts
            WHERE identifier_hash = :identifier_hash
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":identifier_hash" => $identifierHash
    ]);

    $record = $stmt->fetch();

    if (!$record) {
        return false;
    }

    if ((int)$record["is_locked"] === 1) {
        return true;
    }

    // Reset attempts after the time window or lockout expires
    if (
        (int)$record["window_expired"] === 1 ||
        $record["locked_until"] !== null
    ) {
        $reset = $pdo->prepare(
            "UPDATE login_attempts
             SET attempts = 0,
                 last_attempt = NULL,
                 locked_until = NULL
             WHERE identifier_hash = :identifier_hash"
        );

        $reset->execute([
            ":identifier_hash" => $identifierHash
        ]);
    }

    return false;
}

// Record a failed login attempt
function recordFailedLogin(PDO $pdo, string $identifier): void {

    $identifierHash = getLoginIdentifierHash($identifier);

    $sql = "INSERT INTO login_attempts
                (identifier_hash, attempts, last_attempt, locked_until)
            VALUES
                (:identifier_hash, 1, NOW(), NULL)
            ON DUPLICATE KEY UPDATE
                locked_until = CASE
                    WHEN last_attempt IS NULL
                    OR last_attempt <= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                    THEN NULL
                    WHEN attempts >= 4
                    THEN DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                    ELSE locked_until
                END,
                attempts = CASE
                    WHEN last_attempt IS NULL
                    OR last_attempt <= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                    THEN 1
                    ELSE attempts + 1
                END,
                last_attempt = NOW()";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":identifier_hash" => $identifierHash
    ]);
}

// Clear failed login attempts after successful authentication
function clearLoginAttempts(PDO $pdo, string $identifier): void {

    $identifierHash = getLoginIdentifierHash($identifier);

    $stmt = $pdo->prepare(
        "DELETE FROM login_attempts
         WHERE identifier_hash = :identifier_hash"
    );

    $stmt->execute([
        ":identifier_hash" => $identifierHash
    ]);
}
