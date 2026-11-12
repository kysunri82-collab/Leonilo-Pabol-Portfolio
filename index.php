<?php
require_once __DIR__ . '/includes/db.php';

$stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leonilo Pabol | Home</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
  <a href="index.php" class="brand">LEONILO PABOL</a>
  <div class="navbar">
    <ul class="nav-links">
      <li><a href="index.html">Home</a></li>
      <li><a href="about.html">About</a></li>
      <li><a href="skills.html">Skills</a></li>
      <li><a href="projects.php">Projects</a></li>      <li><a href="resume.html">Resume</a></li>
      <li><a href="contact.html">Contact</a></li>
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
<a href="projects.php" class="btn btn-primary">View My Projects</a>         <a href="contact.html" class="btn btn-outline">Get In Touch</a>
    </div>

    <div class="link-list" style="margin-top:20px;">
      <div class="link-row"><span>Facebook</span><a href="<?= htmlspecialchars($profile['facebook_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Visit ↗</a></div>
      <div class="link-row"><span>YouTube</span><a href="<?= htmlspecialchars($profile['youtube_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Watch ↗</a></div>
      <div class="link-row"><span>Instagram</span><a href="<?= htmlspecialchars($profile['instagram_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Follow ↗</a></div>
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
  <a href="#top">BACK TO TOP ↑</a>
</footer>

<button id="scroll-top-btn" aria-label="Scroll to top">↑</button>
<script src="js/script.js"></script>
<script>document.getElementById('year').textContent = new Date().getFullYear();</script>
</body>
</html>
