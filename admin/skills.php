<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';

$stmt = $pdo->query(
    "SELECT skill_name, category, description, skill_level
     FROM skills
     ORDER BY display_order ASC"
);
$skills = $stmt->fetchAll();

$idStmt = $pdo->query(
    "SELECT id, skill_name, description, skill_level
     FROM skills
     ORDER BY id ASC"
);
$skillIds = [];

foreach ($idStmt->fetchAll() as $skillRecord) {
    $skillKey = serialize([
        $skillRecord['skill_name'],
        $skillRecord['description'],
        $skillRecord['skill_level']
    ]);
    $skillIds[$skillKey][] = (int) $skillRecord['id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills</title>
</head>
<body>

    <h1>Manage Skills</h1>

    <p>
        <a href="add_skill.php">+ Add Skill</a>
    </p>

    <a href="dashboard.php">Back to Dashboard</a>

    <h2>My Skills</h2>

    <?php if (count($skills) > 0): ?>

        <?php foreach ($skills as $skill): ?>
            <?php
                $skillKey = serialize([
                    $skill['skill_name'],
                    $skill['description'],
                    $skill['skill_level']
                ]);
                $skillId = array_shift($skillIds[$skillKey]);
            ?>
            <h3>
                <?= htmlspecialchars($skill['skill_name'], ENT_QUOTES, 'UTF-8') ?>
                <small>(<?= htmlspecialchars($skill['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</small>
            </h3>

            <p>
                <?= htmlspecialchars($skill['description'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                Skill level:
                <?= htmlspecialchars($skill['skill_level'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                <a href="edit_skill.php?id=<?= (int) $skillId ?>">Edit</a>
            </p>

            <form method="POST" action="delete_skill.php"
                  onsubmit="return confirm('Are you sure you want to delete this skill?');">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= (int) $skillId ?>">
                <button type="submit">Delete</button>
            </form>

            <hr>
        <?php endforeach; ?>

    <?php else: ?>

        <p>No skills have been added yet.</p>

    <?php endif; ?>

</body>
</html>
