<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

try {
    require_once __DIR__ . '/../includes/db.php';
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to save achievement records right now.");
}

$title = "";
$organization = "";
$achievementDate = "";
$description = "";
$displayOrderInput = "0";
$formError = "";
$monthNumbers = [
    'january' => 1, 'jan' => 1,
    'february' => 2, 'feb' => 2,
    'march' => 3, 'mar' => 3,
    'april' => 4, 'apr' => 4,
    'may' => 5,
    'june' => 6, 'jun' => 6,
    'july' => 7, 'jul' => 7,
    'august' => 8, 'aug' => 8,
    'september' => 9, 'sep' => 9,
    'october' => 10, 'oct' => 10,
    'november' => 11, 'nov' => 11,
    'december' => 12, 'dec' => 12
];
$isValidAchievementDate = static function (string $date) use ($monthNumbers): bool {
    if ($date === '') {
        return true;
    }

    if (preg_match('/^(?!0000)\d{4}$/D', $date)) {
        return true;
    }

    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D', $date, $matches)) {
        return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }

    if (preg_match('/^([A-Za-z]+)\s+(\d{4})$/D', $date, $matches)) {
        return isset($monthNumbers[strtolower($matches[1])]) && (int) $matches[2] > 0;
    }

    return false;
};

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    $postedValues = [
        $_POST["title"] ?? "",
        $_POST["organization"] ?? "",
        $_POST["achievement_date"] ?? "",
        $_POST["description"] ?? "",
        $_POST["display_order"] ?? ""
    ];

    foreach ($postedValues as $value) {
        if (!is_string($value)) {
            $formError = "Please enter valid text for each field.";
            break;
        }
    }

    if ($formError === "") {
        $title = trim($postedValues[0]);
        $organization = trim($postedValues[1]);
        $achievementDate = trim($postedValues[2]);
        $description = trim($postedValues[3]);
        $displayOrderInput = trim($postedValues[4]);

        if ($title === "" || $displayOrderInput === "") {
            $formError = "Title and display order are required.";
        } elseif (
            mb_strlen($title) > 200 ||
            mb_strlen($organization) > 200 ||
            mb_strlen($achievementDate) > 50 ||
            mb_strlen($description) > 10000
        ) {
            $formError = "Please keep the submitted values within the allowed lengths.";
        } elseif (!$isValidAchievementDate($achievementDate)) {
            $formError = "Enter a year (YYYY), month and year (for example, July 2025), or a valid date (YYYY-MM-DD).";
        } elseif (!ctype_digit($displayOrderInput)) {
            $formError = "Display order must be a non-negative whole number.";
        } else {
            $displayOrder = filter_var($displayOrderInput, FILTER_VALIDATE_INT, [
                "options" => ["min_range" => 0]
            ]);

            if ($displayOrder === false) {
                $formError = "Display order must be a non-negative whole number.";
            }
        }
    }

    if ($formError === "") {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO achievements (title, organization, achievement_date, description, display_order)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $title,
                $organization,
                $achievementDate !== "" ? $achievementDate : null,
                $description,
                $displayOrder
            ]);
        } catch (Throwable $error) {
            $formError = "Unable to save this achievement right now.";
        }

        if ($formError === "") {
            header("Location: achievements.php?added=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Achievement</title>
</head>
<body>

    <h1>Add Achievement</h1>

    <?php if ($formError !== ""): ?>
        <p><?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <label>Title</label><br>
        <input type="text" name="title" maxlength="200"
            value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" required>
        <br><br>

        <label>Organization</label><br>
        <input type="text" name="organization" maxlength="200"
            value="<?= htmlspecialchars($organization, ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Achievement Date or Period</label><br>
        <input type="text" name="achievement_date" maxlength="50"
            placeholder="2024, July 2025, or 2025-07-15"
            value="<?= htmlspecialchars($achievementDate, ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Description</label><br>
        <textarea name="description" maxlength="10000"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>
        <br><br>

        <label>Display Order</label><br>
        <input type="number" name="display_order" min="0" step="1"
            value="<?= htmlspecialchars($displayOrderInput, ENT_QUOTES, 'UTF-8') ?>" required>
        <br><br>

        <button type="submit">Add Achievement</button>
    </form>

    <br>
    <a href="achievements.php">Back to Achievements</a>

</body>
</html>
