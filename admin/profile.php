<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/site_settings.php';
require_once __DIR__ . '/../includes/upload.php';

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

    foreach ([
        "full_name",
        "tagline",
        "introduction",
        "profile_image",
        "facebook_url",
        "youtube_url",
        "instagram_url",
        "email",
        "phone_number",
        "location"
    ] as $field) {
        if (!is_string($_POST[$field] ?? "")) {
            die("Please enter valid text in all profile fields.");
        }
    }

    $full_name = trim($_POST["full_name"] ?? "");
    $tagline = trim($_POST["tagline"] ?? "");
    $introduction = trim($_POST["introduction"] ?? "");
    $submittedProfileImage = trim($_POST["profile_image"] ?? "");
    $existingProfileImage = $profile["profile_image"] ?? "";
    $profile_image = $submittedProfileImage !== ""
        ? $submittedProfileImage
        : $existingProfileImage;
    $facebook_url = trim($_POST["facebook_url"] ?? "");
    $youtube_url = trim($_POST["youtube_url"] ?? "");
    $instagram_url = trim($_POST["instagram_url"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone_number = trim($_POST["phone_number"] ?? "");
    $location = trim($_POST["location"] ?? "");

    // Check required fields
    if ($full_name === "" || $tagline === "" || $introduction === "") {
        die("Please complete all required fields.");
    }

    if (
        mb_strlen($full_name) > 100 ||
        mb_strlen($tagline) > 150 ||
        mb_strlen($introduction) > 10000 ||
        mb_strlen($profile_image) > 255 ||
        mb_strlen($facebook_url) > 255 ||
        mb_strlen($youtube_url) > 255 ||
        mb_strlen($instagram_url) > 255 ||
        mb_strlen($email) > 254 ||
        mb_strlen($phone_number) > 50 ||
        mb_strlen($location) > 150
    ) {
        die("Please keep the profile fields within their allowed lengths.");
    }

    if ($email !== "" && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        die("Please enter a valid email address.");
    }

    if ($phone_number !== "" && !preg_match('/^[0-9+().\-\s]+$/', $phone_number)) {
        die("Please enter a valid phone number.");
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

    $uploadedImage = null;
    try {
        $uploadedImage = storeOptionalImageUpload($_FILES["profile_image_upload"] ?? null);
    } catch (InvalidArgumentException $error) {
        die(htmlspecialchars($error->getMessage(), ENT_QUOTES, 'UTF-8'));
    } catch (Throwable $error) {
        die("Unable to process the uploaded image right now.");
    }

    if ($uploadedImage !== null) {
        $profile_image = $uploadedImage;
    } elseif (
        $profile_image !== "" &&
        $profile_image !== $existingProfileImage &&
        !validateWebsiteLogoPath($profile_image)
    ) {
        die("Please enter a valid profile image path or HTTPS URL.");
    }

    // Update the database
    try {
        $stmt = $pdo->prepare("
        UPDATE profile SET
            full_name = ?,
            tagline = ?,
            introduction = ?,
            profile_image = ?,
            facebook_url = ?,
            youtube_url = ?,
            instagram_url = ?,
            email = ?,
            phone_number = ?,
            location = ?
        WHERE id = ?
        ");

        $stmt->execute([
            $full_name,
            $tagline,
            $introduction,
            $profile_image !== "" ? $profile_image : null,
            $facebook_url,
            $youtube_url,
            $instagram_url,
            $email !== "" ? $email : null,
            $phone_number !== "" ? $phone_number : null,
            $location !== "" ? $location : null,
            $profile["id"]
        ]);
    } catch (Throwable $error) {
        if ($uploadedImage !== null) {
            removeUploadedImage($uploadedImage);
        }
        die("Unable to update the profile right now.");
    }

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

    <form method="POST" enctype="multipart/form-data">
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

        <label>Upload a replacement profile image (JPG, JPEG, PNG, or WEBP; maximum 5 MB)</label><br>
        <input type="file" name="profile_image_upload" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        <br><br>

        <label>Email</label><br>
        <input type="email" name="email" maxlength="254"
            value="<?= htmlspecialchars($profile['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Phone Number</label><br>
        <input type="tel" name="phone_number" maxlength="50"
            value="<?= htmlspecialchars($profile['phone_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Location</label><br>
        <input type="text" name="location" maxlength="150"
            value="<?= htmlspecialchars($profile['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
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
