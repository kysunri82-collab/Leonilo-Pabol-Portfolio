<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

try {
    require_once __DIR__ . '/../includes/db.php';
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to load achievement records right now.");
}

$achievementRecords = [];
$loadError = false;

try {
    $stmt = $pdo->query(
        "SELECT id, title, organization, achievement_date, description
         FROM achievements
         ORDER BY display_order ASC"
    );
    $achievementRecords = $stmt->fetchAll();
} catch (Throwable $error) {
    $loadError = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Achievements</title>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">

    <h1>Manage Achievements</h1>

    <a href="dashboard.php">Back to Dashboard</a>

    <h2>My Achievements</h2>

    <p>
        <a href="add_achievement.php">+ Add Achievement</a>
    </p>

    <?php if (isset($_GET['added'])): ?>
        <p>Achievement added successfully.</p>
    <?php elseif (isset($_GET['updated'])): ?>
        <p>Achievement updated successfully.</p>
    <?php elseif (isset($_GET['deleted'])): ?>
        <p>Achievement deleted successfully.</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p>Unable to complete that achievement change. Please try again.</p>
    <?php endif; ?>

    <?php if ($loadError): ?>

        <p>Unable to load achievement records right now.</p>

    <?php elseif (count($achievementRecords) > 0): ?>

        <?php foreach ($achievementRecords as $record): ?>
            <article class="admin-record">
            <h3>
                <?= htmlspecialchars($record['title'], ENT_QUOTES, 'UTF-8') ?>
            </h3>

            <p>
                Organization:
                <?= htmlspecialchars($record['organization'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                Achievement Date:
                <?= htmlspecialchars($record['achievement_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                <?= htmlspecialchars($record['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                <a href="edit_achievement.php?id=<?= (int) $record['id'] ?>">Edit</a>
            </p>

            <form method="POST" action="delete_achievement.php"
                  onsubmit="return confirm('Are you sure you want to delete this achievement?');">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">
                <button type="submit">Delete</button>
            </form>
            </article>
        <?php endforeach; ?>

    <?php else: ?>

        <p>No achievements have been added yet.</p>

    <?php endif; ?>

</main>
</body>
</html>
