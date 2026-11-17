<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid education ID.");
}

try {
    require_once __DIR__ . '/../includes/db.php';
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to load this education record right now.");
}

try {
    $stmt = $pdo->prepare(
        "SELECT school, degree, start_year, end_year, description, display_order
         FROM education
         WHERE id = ?"
    );
    $stmt->execute([$id]);
    $education = $stmt->fetch();
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to load this education record right now.");
}

if (!$education) {
    http_response_code(404);
    die("Education record not found.");
}

$school = $education['school'];
$degree = $education['degree'];
$startYear = $education['start_year'] ?? "";
$endYear = $education['end_year'] ?? "";
$description = $education['description'] ?? "";
$displayOrderInput = (string) $education['display_order'];
$formError = "";
$isValidYear = static function (string $year): bool {
    return $year === "" || preg_match('/^(?!0000)\d{4}$/D', $year) === 1;
};

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    $postedValues = [
        $_POST["school"] ?? "",
        $_POST["degree"] ?? "",
        $_POST["start_year"] ?? "",
        $_POST["end_year"] ?? "",
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
        $school = trim($postedValues[0]);
        $degree = trim($postedValues[1]);
        $startYear = trim($postedValues[2]);
        $endYear = trim($postedValues[3]);
        $description = trim($postedValues[4]);
        $displayOrderInput = trim($postedValues[5]);

        if ($school === "" || $degree === "" || $displayOrderInput === "") {
            $formError = "School, degree or program, and display order are required.";
        } elseif (
            mb_strlen($school) > 200 ||
            mb_strlen($degree) > 200 ||
            mb_strlen($startYear) > 20 ||
            mb_strlen($endYear) > 20 ||
            mb_strlen($description) > 10000
        ) {
            $formError = "Please keep the submitted values within the allowed lengths.";
        } elseif (!$isValidYear($startYear) || !$isValidYear($endYear)) {
            $formError = "Start year and end year must be valid four-digit years.";
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
                "UPDATE education SET
                    school = ?,
                    degree = ?,
                    start_year = ?,
                    end_year = ?,
                    description = ?,
                    display_order = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $school,
                $degree,
                $startYear !== "" ? $startYear : null,
                $endYear !== "" ? $endYear : null,
                $description !== "" ? $description : null,
                $displayOrder,
                $id
            ]);
        } catch (Throwable $error) {
            $formError = "Unable to update this education record right now.";
        }

        if ($formError === "") {
            header("Location: education.php?updated=1");
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
    <title>Edit Education</title>
</head>
<body>

    <h1>Edit Education</h1>

    <?php if ($formError !== ""): ?>
        <p><?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST" action="edit_education.php?id=<?= (int) $id ?>">

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <label>School</label><br>
        <input type="text" name="school" maxlength="200"
            value="<?= htmlspecialchars($school, ENT_QUOTES, 'UTF-8') ?>"
            required>
        <br><br>

        <label>Degree or Program</label><br>
        <input type="text" name="degree" maxlength="200"
            value="<?= htmlspecialchars($degree, ENT_QUOTES, 'UTF-8') ?>"
            required>
        <br><br>

        <label>Start Year</label><br>
        <input type="text" name="start_year" maxlength="20"
            value="<?= htmlspecialchars($startYear, ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>End Year</label><br>
        <input type="text" name="end_year" maxlength="20"
            value="<?= htmlspecialchars($endYear, ENT_QUOTES, 'UTF-8') ?>">
        <br><br>

        <label>Description</label><br>
        <textarea name="description" maxlength="10000"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>
        <br><br>

        <label>Display Order</label><br>
        <input type="number" name="display_order" min="0" step="1"
            value="<?= htmlspecialchars($displayOrderInput, ENT_QUOTES, 'UTF-8') ?>"
            required>
        <br><br>

        <button type="submit">Update Education</button>

    </form>

    <br>
    <a href="education.php">Back to Education</a>

</body>
</html>
