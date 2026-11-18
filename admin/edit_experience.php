<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid experience ID.");
}

try {
    require_once __DIR__ . '/../includes/db.php';
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to load this experience record right now.");
}

try {
    $stmt = $pdo->prepare(
        "SELECT job_title, organization, start_date, end_date, description, display_order
         FROM experience
         WHERE id = ?"
    );
    $stmt->execute([$id]);
    $experience = $stmt->fetch();
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to load this experience record right now.");
}

if (!$experience) {
    http_response_code(404);
    die("Experience record not found.");
}

$jobTitle = $experience['job_title'];
$organization = $experience['organization'];
$startDate = $experience['start_date'] ?? "";
$endDate = $experience['end_date'] ?? "";
$description = $experience['description'] ?? "";
$displayOrderInput = (string) $experience['display_order'];
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
$getPeriodBounds = static function (string $period) use ($monthNumbers): ?array {
    if (preg_match('/^(?!0000)(\d{4})$/D', $period, $matches)) {
        return [$matches[1] . '0101', $matches[1] . '1231'];
    }

    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D', $period, $matches)) {
        $year = (int) $matches[1];
        $month = (int) $matches[2];
        $day = (int) $matches[3];
        if (!checkdate($month, $day, $year)) {
            return null;
        }

        $date = sprintf('%04d%02d%02d', $year, $month, $day);
        return [$date, $date];
    }

    if (preg_match('/^([A-Za-z]+)\s+(\d{4})$/D', $period, $matches)) {
        $month = $monthNumbers[strtolower($matches[1])] ?? null;
        $year = (int) $matches[2];
        if ($month === null || $year < 1) {
            return null;
        }

        $lastDay = 31;
        while ($lastDay > 28 && !checkdate($month, $lastDay, $year)) {
            $lastDay--;
        }

        return [
            sprintf('%04d%02d01', $year, $month),
            sprintf('%04d%02d%02d', $year, $month, $lastDay)
        ];
    }

    return null;
};
$isValidPeriod = static function (string $period) use ($getPeriodBounds): bool {
    return $period === '' || $getPeriodBounds($period) !== null;
};

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    $postedValues = [
        $_POST["job_title"] ?? "",
        $_POST["organization"] ?? "",
        $_POST["start_date"] ?? "",
        $_POST["end_date"] ?? "",
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
        $jobTitle = trim($postedValues[0]);
        $organization = trim($postedValues[1]);
        $startDate = trim($postedValues[2]);
        $endDate = trim($postedValues[3]);
        $description = trim($postedValues[4]);
        $displayOrderInput = trim($postedValues[5]);

        if ($jobTitle === "" || $organization === "" || $displayOrderInput === "") {
            $formError = "Job title, organization, and display order are required.";
        } elseif (
            mb_strlen($jobTitle) > 200 ||
            mb_strlen($organization) > 200 ||
            mb_strlen($description) > 10000
        ) {
            $formError = "Please keep the submitted values within the allowed lengths.";
        } elseif (mb_strlen($startDate) > 50 || mb_strlen($endDate) > 50) {
            $formError = "Each period must be 50 characters or fewer.";
        } elseif (!$isValidPeriod($startDate) || !$isValidPeriod($endDate)) {
            $formError = "Use a year (YYYY), month and year (for example, July 2025), or a valid date (YYYY-MM-DD).";
        } elseif (
            $startDate !== "" &&
            $endDate !== "" &&
            $getPeriodBounds($startDate)[0] > $getPeriodBounds($endDate)[1]
        ) {
            $formError = "End period cannot be earlier than start period.";
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
                "UPDATE experience SET
                    job_title = ?,
                    organization = ?,
                    start_date = ?,
                    end_date = ?,
                    description = ?,
                    display_order = ?
                 WHERE id = ?"
            );
            $stmt->execute([
                $jobTitle,
                $organization,
                $startDate !== "" ? $startDate : null,
                $endDate !== "" ? $endDate : null,
                $description,
                $displayOrder,
                $id
            ]);
        } catch (Throwable $error) {
            $formError = "Unable to update this experience record right now.";
        }

        if ($formError === "") {
            header("Location: experience.php?updated=1");
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
    <title>Edit Experience</title>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">

    <h1>Edit Experience</h1>

    <?php if ($formError !== ""): ?>
        <p><?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST" action="edit_experience.php?id=<?= (int) $id ?>">
        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <label>Job Title</label><br>
        <input type="text" name="job_title" maxlength="200"
            value="<?= htmlspecialchars($jobTitle, ENT_QUOTES, 'UTF-8') ?>" required>
        <br><br>

        <label>Organization</label><br>
        <input type="text" name="organization" maxlength="200"
            value="<?= htmlspecialchars($organization, ENT_QUOTES, 'UTF-8') ?>" required>
        <br><br>

        <label>Start Period / Date</label><br>
        <input type="text" name="start_date" maxlength="50"
            placeholder="2024, July 2025, or 2024-07-15"
            value="<?= htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>End Period / Date</label><br>
        <input type="text" name="end_date" maxlength="50"
            placeholder="2026, September 2025, or 2025-09-30"
            value="<?= htmlspecialchars($endDate, ENT_QUOTES, 'UTF-8') ?>">
        <small>For a range such as 2024&ndash;2026, enter 2024 as the start and 2026 as the end. Month/year and full dates are also supported.</small>
        <br><br>

        <label>Description</label><br>
        <textarea name="description" maxlength="10000"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>
        <br><br>

        <label>Display Order</label><br>
        <input type="number" name="display_order" min="0" step="1"
            value="<?= htmlspecialchars($displayOrderInput, ENT_QUOTES, 'UTF-8') ?>" required>
        <br><br>

        <button type="submit">Update Experience</button>
    </form>

    <br>
    <a href="experience.php">Back to Experience</a>

</main>
</body>
</html>
