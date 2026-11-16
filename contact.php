<?php
require_once __DIR__ . '/includes/security.php';
startSecureSession();
require_once __DIR__ . '/includes/site_settings.php';

try {
    require_once __DIR__ . '/includes/db.php';
} catch (Throwable $error) {
    http_response_code(500);
    die("Unable to process your message right now. Please try again later.");
}

$websiteSettings = getWebsiteSettings($pdo);

$contactProfile = [];
try {
    $profileStmt = $pdo->query(
        "SELECT email, phone_number, location, facebook_url, youtube_url, instagram_url
         FROM profile
         LIMIT 1"
    );
    $contactProfile = $profileStmt->fetch() ?: [];
} catch (Throwable $error) {
    // The contact form remains available if profile details cannot be loaded.
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
            $now = time();
            $recentSubmissions = $_SESSION["contact_submission_times"] ?? [];

            if (!is_array($recentSubmissions)) {
                $recentSubmissions = [];
            }

            $recentSubmissions = array_values(array_filter(
                $recentSubmissions,
                static fn($submittedAt) => is_int($submittedAt) && $submittedAt > $now - 3600
            ));
            $_SESSION["contact_submission_times"] = $recentSubmissions;

            $lastSubmission = $recentSubmissions === [] ? 0 : max($recentSubmissions);

            if (count($recentSubmissions) >= 5) {
                $formError = "You have reached the message limit. Please try again later.";
            } elseif ($lastSubmission > 0 && $now - $lastSubmission < 15) {
                $formError = "Please wait at least 15 seconds before sending another message.";
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
                    $recentSubmissions[] = $now;
                    $_SESSION["contact_submission_times"] = $recentSubmissions;
                    header("Location: contact.php?sent=1");
                    exit;
                }
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
<title><?= htmlspecialchars($websiteSettings['website_title'] . ' | Contact', ENT_QUOTES, 'UTF-8') ?></title>
<link rel="stylesheet" href="css/style.css">
<style>:root { --accent: <?= htmlspecialchars($websiteSettings['appearance_color'], ENT_QUOTES, 'UTF-8') ?>; }</style>
</head>
<body>

<header>
  <a href="index.php" class="brand"><?php if ($websiteSettings['logo_path'] !== ''): ?><img src="<?= htmlspecialchars($websiteSettings['logo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($websiteSettings['website_title'], ENT_QUOTES, 'UTF-8') ?>" style="max-height:52px;max-width:220px;width:auto;object-fit:contain;"><?php else: ?><?= htmlspecialchars(strtoupper($websiteSettings['website_title']), ENT_QUOTES, 'UTF-8') ?><?php endif; ?></a>
  <div class="navbar">
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
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
      <p class="form-status show success" role="status">Thanks for your message. I'll be in touch soon.</p>
    <?php elseif ($formError !== ""): ?>
      <p class="form-status show error" role="alert"><?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <div class="contact-grid">
      <div class="contact-info">
        <ul>
          <?php if (!empty($contactProfile['email'])): ?>
            <li><span class="label">Email</span> <a href="mailto:<?= htmlspecialchars($contactProfile['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($contactProfile['email'], ENT_QUOTES, 'UTF-8') ?></a></li>
          <?php endif; ?>
          <?php if (!empty($contactProfile['phone_number'])): ?>
            <?php $phoneHref = preg_replace('/[^0-9+]/', '', $contactProfile['phone_number']); ?>
            <li><span class="label">Phone</span> <a href="tel:<?= htmlspecialchars($phoneHref, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($contactProfile['phone_number'], ENT_QUOTES, 'UTF-8') ?></a></li>
          <?php endif; ?>
          <?php if (!empty($contactProfile['location'])): ?>
            <li><span class="label">Location</span> <?= htmlspecialchars($contactProfile['location'], ENT_QUOTES, 'UTF-8') ?></li>
          <?php endif; ?>
          <?php foreach ([
              'Facebook' => $contactProfile['facebook_url'] ?? '',
              'Instagram' => $contactProfile['instagram_url'] ?? '',
              'YouTube' => $contactProfile['youtube_url'] ?? ''
          ] as $socialLabel => $socialUrl): ?>
            <?php if (isSafeHttpUrl((string) $socialUrl)): ?>
              <li><span class="label"><?= htmlspecialchars($socialLabel, ENT_QUOTES, 'UTF-8') ?></span> <a href="<?= htmlspecialchars($socialUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= htmlspecialchars($socialUrl, ENT_QUOTES, 'UTF-8') ?></a></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      </div>

      <form class="contact-form" method="POST" action="contact.php">
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
