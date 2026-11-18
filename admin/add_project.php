<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/site_settings.php';
require_once __DIR__ . '/../includes/upload.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate CSRF token
    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    // Get form values
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category = $_POST["category"] ?? "school";
    $technologies = trim($_POST["technologies"] ?? "");

    if (!is_string($_POST["project_image"] ?? "")) {
        die("Please enter a valid project image path.");
    }

    $project_image = trim($_POST["project_image"] ?? "");
    $project_url = trim($_POST["project_url"] ?? "");

    // Check required fields
    if ($title === "" || $description === "") {
        die("Project title and description are required.");
    }

    // Validate project URL
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
    } elseif ($project_image !== "" && !validateWebsiteLogoPath($project_image)) {
        die("Please enter a valid project image path or HTTPS URL.");
    }

    // Insert project into database
    try {
        $stmt = $pdo->prepare("
    INSERT INTO projects (
        title,
        description,
        category,
        technologies,
        project_image,
        project_url
    ) VALUES (?, ?, ?, ?, ?, ?)
");

        $stmt->execute([
            $title,
            $description,
            $category,
            $technologies,
            $project_image,
            $project_url
        ]);
    } catch (Throwable $error) {
        if ($uploadedImage !== null) {
            removeUploadedImage($uploadedImage);
        }
        die("Unable to save the project right now.");
    }

    header("Location: projects.php?added=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project</title>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">

    <h1>Add New Project</h1>

    <form method="POST" enctype="multipart/form-data">

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <label>Project Title</label><br>
        <input type="text" name="title" required>
        <br><br>
        <label>Project Category</label><br>
<select name="category" required>
    <option value="web">Web</option>
    <option value="school" selected>School</option>
    <option value="personal">Personal</option>
    <option value="sample">Sample</option>
</select>
<br><br>

        <label>Description</label><br>
        <textarea name="description" required></textarea>
        <br><br>

        <label>Technologies Used</label><br>
        <input type="text" name="technologies">
        <br><br>

        <label>Project Image Path</label><br>
        <input type="text" name="project_image">
        <br><br>

        <label>Upload Project Image (JPG, JPEG, PNG, or WEBP; maximum 5 MB, 6000 x 6000 pixels, and 20 megapixels)</label><br>
        <input type="file" name="project_image_upload" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        <br><br>

        <label>Project URL</label><br>
        <input type="url" name="project_url">
        <br><br>

        <button type="submit">Add Project</button>

    </form>

    <br>
    <a href="projects.php">Back to Projects</a>

</main>
</body>
</html>
