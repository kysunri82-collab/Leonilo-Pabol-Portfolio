<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/site_settings.php';

$websiteSettings = getWebsiteSettings($pdo);

$stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($websiteSettings['website_title'] . ' | Home', ENT_QUOTES, 'UTF-8') ?></title>
<script src="js/script.js"></script>
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
      <li><a href="skills.php">Skills</a></li>
      <li><a href="projects.php">Projects</a></li>      <li><a href="resume.php">Resume</a></li>
      <li><a href="contact.php">Contact</a></li>
    </ul>
    <button class="theme-toggle" aria-label="Toggle dark mode">🌙 Dark</button>
    <button class="hamburger" aria-label="Open menu" aria-expanded="false">☰</button>
  </div>
</header>

<section class="intro">
  <div class="profilepic">
    <img src="<?= htmlspecialchars($profile['profile_image'], ENT_QUOTES, 'UTF-8') ?>" 
     alt="Photo of Leonilo S. Pabol"
         onerror="this.outerHTML='<div class=&quot;placeholder-photo&quot;>Add your photo at<br>images/leonilo.jpg</div>'">
  </div>

  <div class="text-column">
    <p class="eyebrow">Hello, I'm</p>
<h1 class="big-name">
    <?= htmlspecialchars($profile['full_name'], ENT_QUOTES, 'UTF-8') ?>
</h1>
    <h2 class="big-tagline">
    <?= htmlspecialchars($profile['tagline'], ENT_QUOTES, 'UTF-8') ?>
</h2>
    <p class="lead">
    <?= htmlspecialchars($profile['introduction'], ENT_QUOTES, 'UTF-8') ?>
</p>

    <div class="cta-row">
<a href="projects.php" class="btn btn-primary">View My Projects</a>         <a href="contact.php" class="btn btn-outline">Get In Touch</a>
    </div>

    <div class="link-list" style="margin-top:20px;">
<?php if (isSafeHttpUrl((string) ($profile['facebook_url'] ?? ''))): ?>
      <div class="link-row"><span>Facebook</span><a href="<?= htmlspecialchars($profile['facebook_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Visit ↗</a></div>
<?php endif; ?>
<?php if (isSafeHttpUrl((string) ($profile['youtube_url'] ?? ''))): ?>
      <div class="link-row"><span>YouTube</span><a href="<?= htmlspecialchars($profile['youtube_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Watch ↗</a></div>
<?php endif; ?>
<?php if (isSafeHttpUrl((string) ($profile['instagram_url'] ?? ''))): ?>
      <div class="link-row"><span>Instagram</span><a href="<?= htmlspecialchars($profile['instagram_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Follow ↗</a></div>
<?php endif; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <h2 class="section-title">A Quick Snapshot</h2>
    <p class="section-sub">A little bit about what I focus on and where I'm headed.</p>
    <div class="skills-grid">
      <div class="skill-card">
        <h3>🎓 Student</h3>
        <p>Studying Information Technology, learning to build real, working software.</p>
      </div>
      <div class="skill-card">
        <h3>💻 Developer</h3>
        <p>Comfortable with HTML, CSS, and JavaScript for building responsive websites.</p>
      </div>
      <div class="skill-card">
        <h3>🎵 Creative</h3>
        <p>Music, reading, and the outdoors keep me balanced outside of schoolwork.</p>
      </div>
    </div>
  </div>
</section>

<footer>
  <p>Thanks for stopping by.</p>
  <p>Leonilo Pabol's Portfolio • <span id="year"></span></p>
</footer>

<button id="scroll-top-btn" aria-label="Scroll to top">↑</button>
<script>document.getElementById('year').textContent = new Date().getFullYear();</script>
</body>
</html>
