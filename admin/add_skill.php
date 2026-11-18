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
$category = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate CSRF token
    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    // Get and type-check form values before trimming them.
    $postedValues = [
        "skill_name" => $_POST["skill_name"] ?? "",
        "category" => $_POST["category"] ?? "",
        "description" => $_POST["description"] ?? "",
        "skill_level" => $_POST["skill_level"] ?? "",
        "display_order" => $_POST["display_order"] ?? ""
    ];

    foreach ($postedValues as $value) {
        if (!is_string($value)) {
            die("Please submit valid text for all fields.");
        }
    }

    $skillName = trim($postedValues["skill_name"]);
    $category = trim($postedValues["category"]);
    $description = trim($postedValues["description"]);
    $skillLevel = trim($postedValues["skill_level"]);
    $displayOrderInput = trim($postedValues["display_order"]);

    // Check required fields
    if ($skillName === "" || $category === "" || $description === "" || $skillLevel === "" || $displayOrderInput === "") {
        die("All fields are required.");
    }

    if (!in_array($category, $categories, true)) {
        die("Please select a valid category.");
    }

    // Validate field lengths and display order
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

    // Insert skill into database
    $stmt = $pdo->prepare(
        "INSERT INTO skills (skill_name, category, description, skill_level, display_order)
         VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $skillName,
        $category,
        $description,
        $skillLevel,
        $displayOrder
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
    <title>Add Skill</title>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">

    <h1>Add New Skill</h1>

    <form method="POST">

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <label>Skill Name</label><br>
        <input type="text" name="skill_name" maxlength="100" required>
        <br><br>

        <label>Category</label><br>
        <select name="category" required>
            <option value="" disabled <?= $category === "" ? 'selected' : '' ?>>Select a category</option>
            <?php foreach ($categories as $categoryOption): ?>
                <option value="<?= htmlspecialchars($categoryOption, ENT_QUOTES, 'UTF-8') ?>"
                    <?= $category === $categoryOption ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categoryOption, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Description</label><br>
        <textarea name="description" maxlength="1000" required></textarea>
        <br><br>

        <label>Skill Level</label><br>
        <input type="text" name="skill_level" maxlength="50" required>
        <br><br>

        <label>Display Order</label><br>
        <input type="number" name="display_order" min="0" step="1" required>
        <br><br>

        <button type="submit">Add Skill</button>

    </form>

    <br>
    <a href="skills.php">Back to Skills</a>

</main>
</body>
</html>
