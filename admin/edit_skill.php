<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';

$categories = [
    "Web Development",
    "Programming",
    "Design",
    "Development Tools",
    "Professional Skills",
    "Other"
];

// Get skill ID from URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid skill ID.");
}

// Retrieve skill details
$stmt = $pdo->prepare(
    "SELECT skill_name, category, description, skill_level, display_order
     FROM skills
     WHERE id = ?"
);
$stmt->execute([$id]);
$skill = $stmt->fetch();

if (!$skill) {
    die("Skill not found.");
}

// Process skill update
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    $skillName = trim($_POST["skill_name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $skillLevel = trim($_POST["skill_level"] ?? "");
    $displayOrderInput = trim($_POST["display_order"] ?? "");

    if ($skillName === "" || $category === "" || $description === "" || $skillLevel === "" || $displayOrderInput === "") {
        die("All fields are required.");
    }

    if (!in_array($category, $categories, true)) {
        die("Please select a valid category.");
    }

    if (mb_strlen($skillName) > 100 || mb_strlen($category) > 100 || mb_strlen($description) > 1000 || mb_strlen($skillLevel) > 50) {
        die("Please keep the skill name, category, description, and skill level within the allowed lengths.");
    }

    if (!ctype_digit($displayOrderInput)) {
        die("Display order must be a non-negative whole number.");
    }

    $displayOrder = filter_var($displayOrderInput, FILTER_VALIDATE_INT, [
        "options" => ["min_range" => 0]
    ]);

    if ($displayOrder === false) {
        die("Display order must be a non-negative whole number.");
    }

    $stmt = $pdo->prepare(
        "UPDATE skills SET
            skill_name = ?,
            category = ?,
            description = ?,
            skill_level = ?,
            display_order = ?
         WHERE id = ?"
    );

    $stmt->execute([
        $skillName,
        $category,
        $description,
        $skillLevel,
        $displayOrder,
        $id
    ]);

    header("Location: skills.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Skill</title>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">

    <h1>Edit Skill</h1>

    <form method="POST">

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <label>Skill Name</label><br>
        <input type="text" name="skill_name" maxlength="100"
            value="<?= htmlspecialchars($skill['skill_name'], ENT_QUOTES, 'UTF-8') ?>"
            required>
        <br><br>

        <label>Category</label><br>
        <select name="category" required>
            <?php foreach ($categories as $categoryOption): ?>
                <option value="<?= htmlspecialchars($categoryOption, ENT_QUOTES, 'UTF-8') ?>"
                    <?= ($skill['category'] ?? '') === $categoryOption ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categoryOption, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Description</label><br>
        <textarea name="description" maxlength="1000" required><?= htmlspecialchars($skill['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
        <br><br>

        <label>Skill Level</label><br>
        <input type="text" name="skill_level" maxlength="50"
            value="<?= htmlspecialchars($skill['skill_level'], ENT_QUOTES, 'UTF-8') ?>"
            required>
        <br><br>

        <label>Display Order</label><br>
        <input type="number" name="display_order" min="0" step="1"
            value="<?= (int) $skill['display_order'] ?>"
            required>
        <br><br>

        <button type="submit">Update Skill</button>

    </form>

    <br>
    <a href="skills.php">Back to Skills</a>

</main>
</body>
</html>
