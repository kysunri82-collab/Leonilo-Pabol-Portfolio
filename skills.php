<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/site_settings.php';

$websiteSettings = getWebsiteSettings($pdo);

$stmt = $pdo->query("SELECT skill_name, description, skill_level FROM skills ORDER BY display_order ASC");
$skills = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($websiteSettings['website_title'] . ' | Skills', ENT_QUOTES, 'UTF-8') ?></title>
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
    <button class="theme-toggle" aria-label="Toggle dark mode">🌙 Dark</button>
    <button class="hamburger" aria-label="Open menu" aria-expanded="false">☰</button>
  </div>
</header>

<section class="section">
  <div class="wrap">
    <h1 class="section-title">Skills</h1>
    <p class="section-sub">A snapshot of the skills I'm developing and using in my work.</p>

    <div class="skills-grid">

      <?php foreach ($skills as $skill): ?>
        <div class="skill-card">
          <h3><?= htmlspecialchars($skill['skill_name'], ENT_QUOTES, 'UTF-8') ?></h3>
          <p><?= htmlspecialchars($skill['description'], ENT_QUOTES, 'UTF-8') ?></p>
          <p class="skill-level"><?= htmlspecialchars($skill['skill_level'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<footer>
  <p>Thanks for stopping by.</p>
  <p>Leonilo Pabol's Portfolio • <span id="year"></span></p>
  <a href="#top">BACK TO TOP ↑</a>
</footer>

<button id="scroll-top-btn" aria-label="Scroll to top">↑</button>
<script src="js/script.js"></script>
<script>document.getElementById('year').textContent = new Date().getFullYear();</script>
</body>
</html>
