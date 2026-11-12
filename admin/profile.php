<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';

// Get the existing profile
$stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt->fetch();

if (!$profile) {
    die("Profile record not found.");
}

// Process the form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    $full_name = trim($_POST["full_name"] ?? "");
    $tagline = trim($_POST["tagline"] ?? "");
    $introduction = trim($_POST["introduction"] ?? "");
    $profile_image = trim($_POST["profile_image"] ?? "");
    $facebook_url = trim($_POST["facebook_url"] ?? "");
    $youtube_url = trim($_POST["youtube_url"] ?? "");
    $instagram_url = trim($_POST["instagram_url"] ?? "");

    // Check required fields
    if ($full_name === "" || $tagline === "" || $introduction === "") {
        die("Please complete all required fields.");
    }

    // Validate social media URLs
    foreach ([$facebook_url, $youtube_url, $instagram_url] as $url) {
        if ($url !== "" && filter_var($url, FILTER_VALIDATE_URL) === false) {
            die("Please enter valid social media URLs.");
        }

        if ($url !== "" && parse_url($url, PHP_URL_SCHEME) !== "https") {
            die("Social media links must use HTTPS.");
        }
    }

    // Update the database
    $stmt = $pdo->prepare("
        UPDATE profile SET
            full_name = ?,
            tagline = ?,
            introduction = ?,
            profile_image = ?,
            facebook_url = ?,
            youtube_url = ?,
            instagram_url = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $full_name,
        $tagline,
        $introduction,
        $profile_image,
        $facebook_url,
        $youtube_url,
        $instagram_url,
        $profile["id"]
    ]);

    header("Location: profile.php?updated=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
</head>
<body>

    <h1>Edit Profile</h1>
    <?php if (isset($_GET["updated"])): ?>
    <p style="color: green;">Profile updated successfully!</p>
<?php endif; ?>

    <form method="POST">
    <input type="hidden" name="csrf_token"
        value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <label>Full Name</label><br>
        <input type="text" name="full_name"
            value="<?= htmlspecialchars($profile['full_name'], ENT_QUOTES, 'UTF-8') ?>"
            required>
        <br><br>

        <label>Tagline</label><br>
        <input type="text" name="tagline"
            value="<?= htmlspecialchars($profile['tagline'], ENT_QUOTES, 'UTF-8') ?>"
            required>
        <br><br>

        <label>Introduction</label><br>
        <textarea name="introduction" required><?= htmlspecialchars($profile['introduction'], ENT_QUOTES, 'UTF-8') ?></textarea>
        <br><br>

        <label>Profile Image Path</label><br>
        <input type="text" name="profile_image"
            value="<?= htmlspecialchars($profile['profile_image'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Facebook URL</label><br>
        <input type="url" name="facebook_url"
            value="<?= htmlspecialchars($profile['facebook_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>YouTube URL</label><br>
        <input type="url" name="youtube_url"
            value="<?= htmlspecialchars($profile['youtube_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Instagram URL</label><br>
        <input type="url" name="instagram_url"
            value="<?= htmlspecialchars($profile['instagram_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <button type="submit">Save Changes</button>

    </form>

    <br>
    <a href="dashboard.php">Back to Dashboard</a>

</body>
</html>