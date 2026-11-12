<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';

$stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects</title>
    <?php if (isset($_GET["added"])): ?>
    <p style="color: green;">Project added successfully!</p>
<?php endif; ?>
</head>
<body>

    <h1>Manage Projects</h1>
    <?php if (isset($_GET["updated"])): ?>
    <p style="color: green;">Project updated successfully!</p>
<?php endif; ?>
    <p>
    <a href="add_project.php">+ Add New Project</a>
</p>

    <a href="dashboard.php">Back to Dashboard</a>

    <h2>My Projects</h2>

    <?php if (count($projects) > 0): ?>

        <?php foreach ($projects as $project): ?>
            <h3>
                <?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>
            </h3>

            <p>
                <?= htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                Technologies:
                <?= htmlspecialchars($project['technologies'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>
            <p>
    <a href="edit_project.php?id=<?= (int) $project['id'] ?>">Edit Project</a>
</p>
<form method="POST" action="delete_project.php"
      onsubmit="return confirm('Are you sure you want to delete this project?');">

    <input type="hidden" name="csrf_token"
        value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

    <input type="hidden" name="id"
        value="<?= (int) $project['id'] ?>">

    <button type="submit">Delete Project</button>

</form>

            <hr>
        <?php endforeach; ?>

    <?php else: ?>

        <p>No projects have been added yet.</p>

    <?php endif; ?>

</body>
</html>