<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid message ID.");
}

try {
    require_once __DIR__ . '/../includes/db.php';
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to load this message right now.");
}

$actionError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    $readStatus = $_POST["is_read"] ?? "";

    if (!is_string($readStatus) || !in_array($readStatus, ["0", "1"], true)) {
        $actionError = "Invalid message status.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = ? WHERE id = ?");
            $stmt->execute([(int) $readStatus, $id]);
        } catch (Throwable $error) {
            $actionError = "Unable to update this message right now.";
        }

        if ($actionError === "") {
            header("Location: view_message.php?id=" . (int) $id . "&updated=1");
            exit;
        }
    }
}

try {
    $stmt = $pdo->prepare(
        "SELECT name, email, subject, message, created_at, is_read
         FROM contact_messages
         WHERE id = ?"
    );
    $stmt->execute([$id]);
    $message = $stmt->fetch();
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to load this message right now.");
}

if (!$message) {
    http_response_code(404);
    die("Message not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message</title>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">

    <h1>View Message</h1>

    <a href="messages.php">Back to Messages</a>

    <?php if (isset($_GET['updated'])): ?>
        <p>Message status updated.</p>
    <?php endif; ?>

    <?php if ($actionError !== ""): ?>
        <p><?= htmlspecialchars($actionError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <h2><?= htmlspecialchars($message['subject'], ENT_QUOTES, 'UTF-8') ?></h2>

    <p>
        From:
        <?= htmlspecialchars($message['name'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        Email:
        <?= htmlspecialchars($message['email'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        Date:
        <?= htmlspecialchars($message['created_at'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        Status: <?= (int) $message['is_read'] === 1 ? 'Read' : 'Unread' ?>
    </p>

    <p><?= nl2br(htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8')) ?></p>

    <form method="POST" action="view_message.php?id=<?= (int) $id ?>">
        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="is_read" value="<?= (int) $message['is_read'] === 1 ? '0' : '1' ?>">
        <button type="submit">
            <?= (int) $message['is_read'] === 1 ? 'Mark as Unread' : 'Mark as Read' ?>
        </button>
    </form>

</main>
</body>
</html>
