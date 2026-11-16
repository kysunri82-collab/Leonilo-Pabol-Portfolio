<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/site_settings.php';

$settings = getWebsiteSettings($pdo);
$formError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    $postedValues = [
        $_POST["website_title"] ?? "",
        $_POST["logo_path"] ?? "",
        $_POST["appearance_color"] ?? ""
    ];

    foreach ($postedValues as $value) {
        if (!is_string($value)) {
            $formError = "Please enter valid text in each field.";
            break;
        }
    }

    if ($formError === "") {
        $settings["website_title"] = trim($postedValues[0]);
        $settings["logo_path"] = trim($postedValues[1]);
        $settings["appearance_color"] = trim($postedValues[2]);

        if ($settings["website_title"] === "") {
            $formError = "Website title is required.";
        } elseif (mb_strlen($settings["website_title"]) > 150) {
            $formError = "Website title must be 150 characters or fewer.";
        } elseif (!validateWebsiteLogoPath($settings["logo_path"])) {
            $formError = "Use a local image path or a valid HTTPS image URL for the logo.";
        } elseif (!preg_match('/^#[0-9A-Fa-f]{6}$/D', $settings["appearance_color"])) {
            $formError = "Appearance color must be a six-digit hexadecimal color.";
        }
    }

    if ($formError === "") {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO settings (id, website_title, logo_path, appearance_color)
                 VALUES (1, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    website_title = VALUES(website_title),
                    logo_path = VALUES(logo_path),
                    appearance_color = VALUES(appearance_color)"
            );
            $stmt->execute([
                $settings["website_title"],
                $settings["logo_path"] !== "" ? $settings["logo_path"] : null,
                $settings["appearance_color"]
            ]);
        } catch (Throwable $error) {
            $formError = "Unable to save website settings right now.";
        }

        if ($formError === "") {
            header("Location: settings.php?updated=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Settings</title>
</head>
<body>

    <h1>Website Settings</h1>

    <?php if (isset($_GET["updated"])): ?>
        <p style="color: green;">Website settings updated successfully.</p>
    <?php elseif ($formError !== ""): ?>
        <p><?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <label>Website Title</label><br>
        <input type="text" name="website_title" maxlength="150"
            value="<?= htmlspecialchars($settings["website_title"], ENT_QUOTES, 'UTF-8') ?>"
            required>
        <br><br>

        <label>Logo Path or HTTPS URL</label><br>
        <input type="text" name="logo_path" maxlength="255"
            value="<?= htmlspecialchars($settings["logo_path"], ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Appearance Color</label><br>
        <input type="color" name="appearance_color"
            value="<?= htmlspecialchars($settings["appearance_color"], ENT_QUOTES, 'UTF-8') ?>"
            required>
        <br><br>

        <button type="submit">Save Settings</button>
    </form>

    <br>
    <a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
