<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

try {
    require_once __DIR__ . '/../includes/db.php';
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to load messages right now.");
}

$messages = [];
$loadError = false;

try {
    $stmt = $pdo->query(
        "SELECT id, name, email, subject, created_at, is_read
         FROM contact_messages
         ORDER BY created_at DESC, id DESC"
    );
    $messages = $stmt->fetchAll();
} catch (Throwable $error) {
    $loadError = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages</title>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">

    <h1>Manage Messages</h1>

    <a href="dashboard.php">Back to Dashboard</a>

    <h2>Contact Inbox</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <p>Message deleted successfully.</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p>Unable to complete that message action. Please try again.</p>
    <?php endif; ?>

    <?php if ($loadError): ?>

        <p>Unable to load messages right now.</p>

    <?php elseif (count($messages) > 0): ?>

        <?php foreach ($messages as $message): ?>
            <article class="admin-record">
            <h3>
                <?= htmlspecialchars($message['subject'], ENT_QUOTES, 'UTF-8') ?>
            </h3>

            <p>
                From:
                <?= htmlspecialchars($message['name'], ENT_QUOTES, 'UTF-8') ?>
                (<?= htmlspecialchars($message['email'], ENT_QUOTES, 'UTF-8') ?>)
            </p>

            <p>
                Date:
                <?= htmlspecialchars($message['created_at'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                Status: <?= (int) $message['is_read'] === 1 ? 'Read' : 'Unread' ?>
            </p>

            <p>
                <a href="view_message.php?id=<?= (int) $message['id'] ?>">View Message</a>
            </p>

            <form method="POST" action="delete_message.php"
                  onsubmit="return confirm('Are you sure you want to delete this message?');">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                <button type="submit">Delete</button>
            </form>
            </article>
        <?php endforeach; ?>

    <?php else: ?>

        <p>No messages have been received yet.</p>

    <?php endif; ?>

</main>
</body>
</html>
