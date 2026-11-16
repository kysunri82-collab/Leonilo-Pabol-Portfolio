<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/security.php';

try {
    require_once __DIR__ . '/includes/db.php';
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to process your message right now. Please try again later.");
}

$name = "";
$email = "";
$subject = "";
$message = "";
$formError = "";
$messageSent = isset($_GET['sent']) && $_GET['sent'] === "1";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";

    if (!validateCsrfToken($token)) {
        die("Invalid security token. Please refresh the page.");
    }

    $postedValues = [
        $_POST["name"] ?? "",
        $_POST["email"] ?? "",
        $_POST["subject"] ?? "",
        $_POST["message"] ?? "",
        $_POST["website"] ?? ""
    ];

    foreach ($postedValues as $value) {
        if (!is_string($value)) {
            $formError = "Please enter valid text in all fields.";
            break;
        }
    }

    if ($formError === "") {
        $name = trim($postedValues[0]);
        $email = trim($postedValues[1]);
        $subject = trim($postedValues[2]);
        $message = trim($postedValues[3]);
        $honeypot = trim($postedValues[4]);

        if ($name === "" || $email === "" || $subject === "" || $message === "") {
            $formError = "Please complete all fields.";
        } elseif (
            mb_strlen($name) > 100 ||
            mb_strlen($email) > 254 ||
            mb_strlen($subject) > 200 ||
            mb_strlen($message) > 10000
        ) {
            $formError = "Please keep each field within its allowed length.";
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $formError = "Please enter a valid email address.";
        } elseif ($honeypot !== "") {
            // Treat honeypot submissions as successful without storing them.
            header("Location: contact.php?sent=1");
            exit;
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO contact_messages (name, email, subject, message)
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([$name, $email, $subject, $message]);
            } catch (Throwable $error) {
                $formError = "Unable to send your message right now. Please try again later.";
            }

            if ($formError === "") {
                header("Location: contact.php?sent=1");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leonilo Pabol | Contact</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
  <a href="index.html" class="brand">LEONILO PABOL</a>
  <div class="navbar">
    <ul class="nav-links">
      <li><a href="index.html">Home</a></li>
      <li><a href="about.html">About</a></li>
      <li><a href="skills.html">Skills</a></li>
      <li><a href="projects.html">Projects</a></li>
      <li><a href="resume.html">Resume</a></li>
      <li><a href="contact.php">Contact</a></li>
    </ul>
    <button class="theme-toggle" aria-label="Toggle dark mode">ðŸŒ™ Dark</button>
    <button class="hamburger" aria-label="Open menu" aria-expanded="false">â˜°</button>
  </div>
</header>

<section class="section">
  <div class="wrap">
    <h1 class="section-title">Contact</h1>
    <p class="section-sub">Have a project or opportunity in mind? Reach out below.</p>

    <?php if ($messageSent): ?>
      <p class="form-status" role="status">Thanks for your message. I'll be in touch soon.</p>
    <?php elseif ($formError !== ""): ?>
      <p class="form-status" role="alert"><?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <div class="contact-grid">
      <div class="contact-info">
        <ul>
          <li><span class="label">Email</span> <a href="mailto:kysunri82@gmail.com">kysunri82@gmail.com</a></li>
          <li><span class="label">Facebook</span> <a href="https://www.facebook.com/leiyhoui" target="_blank" rel="noopener">facebook.com/leiyhoui</a></li>
          <li><span class="label">Instagram</span> <a href="https://www.instagram.com/leomazingri/" target="_blank" rel="noopener">instagram.com/leomazingri</a></li>
          <li><span class="label">YouTube</span> <a href="https://www.youtube.com/@talmidkysun9687" target="_blank" rel="noopener">youtube.com/@talmidkysun9687</a></li>
        </ul>
      </div>

      <form method="POST" action="contact.php">
        <input type="hidden" name="csrf_token"
          value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <div aria-hidden="true" style="position:absolute;left:-10000px;">
          <label for="website">Leave this field empty</label>
          <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" maxlength="100"
            placeholder="Juan Dela Cruz" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" maxlength="254"
            placeholder="you@example.com" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-group">
          <label for="subject">Subject</label>
          <input type="text" id="subject" name="subject" maxlength="200"
            value="<?= htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" name="message" rows="5" maxlength="10000" required><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Send Message</button>
      </form>
    </div>
  </div>
</section>

<footer>
  <p>Thanks for stopping by.</p>
  <p>Leonilo Pabol's Portfolio â€¢ <span id="year"></span></p>
  <a href="#top">BACK TO TOP â†‘</a>
</footer>

<button id="scroll-top-btn" aria-label="Scroll to top">â†‘</button>
<script src="js/script.js"></script>
<script>document.getElementById('year').textContent = new Date().getFullYear();</script>
</body>
</html>
