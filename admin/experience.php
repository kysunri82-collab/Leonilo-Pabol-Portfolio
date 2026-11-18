<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

try {
    require_once __DIR__ . '/../includes/db.php';
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to load experience records right now.");
}

$experienceRecords = [];
$loadError = false;

try {
    $stmt = $pdo->query(
        "SELECT id, job_title, organization, start_date, end_date, description
         FROM experience
         ORDER BY display_order ASC"
    );
    $experienceRecords = $stmt->fetchAll();
} catch (Throwable $error) {
    $loadError = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Experience</title>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">

    <h1>Manage Experience</h1>

    <a href="dashboard.php">Back to Dashboard</a>

    <h2>My Experience</h2>

    <p>
        <a href="add_experience.php">+ Add Experience</a>
    </p>

    <?php if (isset($_GET['added'])): ?>
        <p>Experience record added successfully.</p>
    <?php elseif (isset($_GET['updated'])): ?>
        <p>Experience record updated successfully.</p>
    <?php elseif (isset($_GET['deleted'])): ?>
        <p>Experience record deleted successfully.</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p>Unable to complete that experience change. Please try again.</p>
    <?php endif; ?>

    <?php if ($loadError): ?>

        <p>Unable to load experience records right now.</p>

    <?php elseif (count($experienceRecords) > 0): ?>

        <?php foreach ($experienceRecords as $record): ?>
            <article class="admin-record">
            <h3>
                <?= htmlspecialchars($record['job_title'], ENT_QUOTES, 'UTF-8') ?>
            </h3>

            <p>
                Organization:
                <?= htmlspecialchars($record['organization'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                Start Date:
                <?= htmlspecialchars($record['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                End Date:
                <?= htmlspecialchars($record['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                <?= htmlspecialchars($record['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                <a href="edit_experience.php?id=<?= (int) $record['id'] ?>">Edit</a>
            </p>

            <form method="POST" action="delete_experience.php"
                  onsubmit="return confirm('Are you sure you want to delete this experience record?');">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">
                <button type="submit">Delete</button>
            </form>
            </article>
        <?php endforeach; ?>

    <?php else: ?>

        <p>No experience records have been added yet.</p>

    <?php endif; ?>

</main>
</body>
</html>
