<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate CSRF token
    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    // Get form values
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $technologies = trim($_POST["technologies"] ?? "");
    $project_image = trim($_POST["project_image"] ?? "");
    $project_url = trim($_POST["project_url"] ?? "");

    // Check required fields
    if ($title === "" || $description === "") {
        die("Project title and description are required.");
    }

    // Validate project URL
    if ($project_url !== "") {
        if (
            filter_var($project_url, FILTER_VALIDATE_URL) === false ||
            parse_url($project_url, PHP_URL_SCHEME) !== "https"
        ) {
            die("Please enter a valid HTTPS project URL.");
        }
    }

    // Insert project into database
    $stmt = $pdo->prepare("
        INSERT INTO projects (
            title,
            description,
            technologies,
            project_image,
            project_url
        ) VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $title,
        $description,
        $technologies,
        $project_image,
        $project_url
    ]);

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
</head>
<body>

    <h1>Add New Project</h1>

    <form method="POST">

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <label>Project Title</label><br>
        <input type="text" name="title" required>
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

        <label>Project URL</label><br>
        <input type="url" name="project_url">
        <br><br>

        <button type="submit">Add Project</button>

    </form>

    <br>
    <a href="projects.php">Back to Projects</a>

</body>
</html>