<?php

function validateWebsiteLogoPath(string $path): bool {
    if ($path === "") {
        return true;
    }

    if (mb_strlen($path) > 255) {
        return false;
    }

    if (filter_var($path, FILTER_VALIDATE_URL) !== false) {
        return parse_url($path, PHP_URL_SCHEME) === "https";
    }

    if (!preg_match('#^/?[A-Za-z0-9._/-]+$#D', $path)) {
        return false;
    }

    return !preg_match('#(^|/)\.\.?(/|$)#', $path);
}

function getWebsiteSettings(PDO $pdo): array {
    $defaults = [
        "website_title" => "Leonilo Pabol",
        "logo_path" => "",
        "appearance_color" => "#e8c61f"
    ];

    try {
        $stmt = $pdo->prepare(
            "SELECT website_title, logo_path, appearance_color
             FROM settings
             WHERE id = ?"
        );
        $stmt->execute([1]);
        $settings = $stmt->fetch();
    } catch (Throwable $error) {
        return $defaults;
    }

    if (!$settings) {
        return $defaults;
    }

    $websiteTitle = trim((string) ($settings["website_title"] ?? ""));
    $logoPath = trim((string) ($settings["logo_path"] ?? ""));
    $appearanceColor = (string) ($settings["appearance_color"] ?? "");

    return [
        "website_title" => $websiteTitle !== "" ? $websiteTitle : $defaults["website_title"],
        "logo_path" => validateWebsiteLogoPath($logoPath) ? $logoPath : "",
        "appearance_color" => preg_match('/^#[0-9A-Fa-f]{6}$/D', $appearanceColor)
            ? $appearanceColor
            : $defaults["appearance_color"]
    ];
}
