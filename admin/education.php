<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

try {
    require_once __DIR__ . '/../includes/db.php';
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to load education records right now.");
}

$educationRecords = [];
$loadError = false;

try {
    $stmt = $pdo->query(
        "SELECT id, school, degree, start_year, end_year, description
         FROM education
         ORDER BY display_order ASC"
    );
    $educationRecords = $stmt->fetchAll();
} catch (Throwable $error) {
    $loadError = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Education</title>
</head>
<body>

    <h1>Manage Education</h1>

    <a href="dashboard.php">Back to Dashboard</a>

    <h2>My Education</h2>

    <p>
        <a href="add_education.php">+ Add Education</a>
    </p>

    <?php if (isset($_GET['added'])): ?>
        <p>Education record added successfully.</p>
    <?php elseif (isset($_GET['updated'])): ?>
        <p>Education record updated successfully.</p>
    <?php elseif (isset($_GET['deleted'])): ?>
        <p>Education record deleted successfully.</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p>Unable to complete that education change. Please try again.</p>
    <?php endif; ?>

    <?php if ($loadError): ?>

        <p>Unable to load education records right now.</p>

    <?php elseif (count($educationRecords) > 0): ?>

        <?php foreach ($educationRecords as $record): ?>
            <h3>
                <?= htmlspecialchars($record['school'], ENT_QUOTES, 'UTF-8') ?>
            </h3>

            <p>
                Degree:
                <?= htmlspecialchars($record['degree'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                Start Year:
                <?= htmlspecialchars($record['start_year'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                End Year:
                <?= htmlspecialchars($record['end_year'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                <?= htmlspecialchars($record['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                <a href="edit_education.php?id=<?= (int) $record['id'] ?>">Edit</a>
            </p>

            <form method="POST" action="delete_education.php"
                  onsubmit="return confirm('Are you sure you want to delete this education record?');">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">
                <button type="submit">Delete</button>
            </form>

            <hr>
        <?php endforeach; ?>

    <?php else: ?>

        <p>No education records have been added yet.</p>

    <?php endif; ?>

</body>
</html>
