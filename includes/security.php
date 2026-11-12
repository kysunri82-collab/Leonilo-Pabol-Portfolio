<?php

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