<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';

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
    $technologies = trim($_POST["technologies"] ?? "");
    $project_image = trim($_POST["project_image"] ?? "");
    $project_url = trim($_POST["project_url"] ?? "");

    if ($title === "" || $description === "") {
        die("Project title and description are required.");
    }

    if ($project_url !== "") {
        if (
            filter_var($project_url, FILTER_VALIDATE_URL) === false ||
            parse_url($project_url, PHP_URL_SCHEME) !== "https"
        ) {
            die("Please enter a valid HTTPS project URL.");
        }
    }

    $stmt = $pdo->prepare("
        UPDATE projects SET
            title = ?,
            description = ?,
            technologies = ?,
            project_image = ?,
            project_url = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $title,
        $description,
        $technologies,
        $project_image,
        $project_url,
        $id
    ]);

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
</head>
<body>

    <h1>Edit Project</h1>

    <form method="POST">

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

        <label>Technologies Used</label><br>
        <input type="text" name="technologies"
            value="<?= htmlspecialchars($project['technologies'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Project Image Path</label><br>
        <input type="text" name="project_image"
            value="<?= htmlspecialchars($project['project_image'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Project URL</label><br>
        <input type="url" name="project_url"
            value="<?= htmlspecialchars($project['project_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <button type="submit">Update Project</button>

    </form>

    <br>
    <a href="projects.php">Back to Projects</a>

</body>
</html>