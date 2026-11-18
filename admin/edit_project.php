<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/site_settings.php';
require_once __DIR__ . '/../includes/upload.php';

// Get project ID from URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid project ID.");
}

// Retrieve project details
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    die("Project not found.");
}

// Process project update
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category = $_POST["category"] ?? "school";
    $technologies = trim($_POST["technologies"] ?? "");

    if (!is_string($_POST["project_image"] ?? "")) {
        die("Please enter a valid project image path.");
    }

    $submittedImagePath = trim($_POST["project_image"] ?? "");
    $existingImagePath = $project['project_image'] ?? "";
    $project_image = $submittedImagePath !== ""
        ? $submittedImagePath
        : $existingImagePath;
    $project_url = trim($_POST["project_url"] ?? "");

    if ($title === "" || $description === "") {
        die("Project title and description are required.");
    }

    if ($project_url !== "") {
        if (!isSafeHttpUrl($project_url)) {
            die("Please enter a valid HTTP or HTTPS project URL.");
        }
    }

    $uploadedImage = null;
    try {
        $uploadedImage = storeOptionalImageUpload($_FILES["project_image_upload"] ?? null);
    } catch (InvalidArgumentException $error) {
        die(htmlspecialchars($error->getMessage(), ENT_QUOTES, 'UTF-8'));
    } catch (Throwable $error) {
        die("Unable to process the uploaded image right now.");
    }

    if ($uploadedImage !== null) {
        $project_image = $uploadedImage;
    } elseif (
        $project_image !== "" &&
        $project_image !== $existingImagePath &&
        !validateWebsiteLogoPath($project_image)
    ) {
        die("Please enter a valid project image path or HTTPS URL.");
    }

    try {
        $stmt = $pdo->prepare("
    UPDATE projects SET
        title = ?,
        description = ?,
        category = ?,
        technologies = ?,
        project_image = ?,
        project_url = ?
    WHERE id = ?
");

        $stmt->execute([
            $title,
            $description,
            $category,
            $technologies,
            $project_image,
            $project_url,
            $id
        ]);
    } catch (Throwable $error) {
        if ($uploadedImage !== null) {
            removeUploadedImage($uploadedImage);
        }
        die("Unable to update the project right now.");
    }
    header("Location: projects.php?updated=1");
    exit;
}

if (!$project) {
    die("Project not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">

    <h1>Edit Project</h1>

    <form method="POST" enctype="multipart/form-data">

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <label>Project Title</label><br>
        <input type="text" name="title"
            value="<?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>"
            required>
        <br><br>

        <label>Description</label><br>
        <textarea name="description" required><?= htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
        <br><br>
        <label>Project Category</label><br>
<select name="category" required>
    <option value="web" <?= $project['category'] === 'web' ? 'selected' : '' ?>>Web</option>
    <option value="school" <?= $project['category'] === 'school' ? 'selected' : '' ?>>School</option>
    <option value="personal" <?= $project['category'] === 'personal' ? 'selected' : '' ?>>Personal</option>
    <option value="sample" <?= $project['category'] === 'sample' ? 'selected' : '' ?>>Sample</option>
</select>
<br><br>

        <label>Technologies Used</label><br>
        <input type="text" name="technologies"
            value="<?= htmlspecialchars($project['technologies'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Project Image Path</label><br>
        <input type="text" name="project_image"
            value="<?= htmlspecialchars($project['project_image'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Upload a replacement image (JPG, JPEG, PNG, or WEBP; maximum 5 MB, 6000 x 6000 pixels, and 20 megapixels)</label><br>
        <input type="file" name="project_image_upload" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        <br><br>

        <label>Project URL</label><br>
        <input type="url" name="project_url"
            value="<?= htmlspecialchars($project['project_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <button type="submit">Update Project</button>

    </form>

    <br>
    <a href="projects.php">Back to Projects</a>

</main>
</body>
</html>
