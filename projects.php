<?php
require_once __DIR__ . '/includes/db.php';

$stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leonilo Pabol | Projects</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
 <a href="index.php" class="brand">LEONILO PABOL</a>
  <div class="navbar">
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="skills.php">Skills</a></li>
      <li><a href="projects.php">Projects</a></li>
      <li><a href="resume.php">Resume</a></li>
      <li><a href="contact.php">Contact</a></li>
    </ul>
    <button class="theme-toggle" aria-label="Toggle dark mode">🌙 Dark</button>
    <button class="hamburger" aria-label="Open menu" aria-expanded="false">☰</button>
  </div>
</header>

<section class="section">
  <div class="wrap">
    <h1 class="section-title">Projects</h1>
    <p class="section-sub">Selected work and sample concepts. Browse by type; no project photos needed.</p>

    <div class="filter-row">
      <button class="filter-btn active" data-filter="all">All</button>
      <button class="filter-btn" data-filter="web">Web</button>
      <button class="filter-btn" data-filter="school">School</button>
      <button class="filter-btn" data-filter="personal">Personal</button>
      <button class="filter-btn" data-filter="sample">Samples</button>
    </div>

   <div class="projects-grid">

    <?php if (count($projects) > 0): ?>

        <?php foreach ($projects as $project): ?>

            <div class="project-card"
     data-category="<?= htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8') ?>"
                 data-title="<?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>"
                 data-full-desc="<?= htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="project-kind">
    <?= htmlspecialchars(ucfirst($project['category']), ENT_QUOTES, 'UTF-8') ?> Project
</div>

                <div class="project-body">

                    <h3>
                        <?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <div class="tag-row">
                        <?php
                        $technologies = explode(',', $project['technologies'] ?? '');
                        foreach ($technologies as $technology):
                            if (trim($technology) !== ""):
                        ?>
                            <span class="tag">
                                <?= htmlspecialchars(trim($technology), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>

                    <?php if (!empty($project['project_url'])): ?>
                        <a href="<?= htmlspecialchars($project['project_url'], ENT_QUOTES, 'UTF-8') ?>"
                           target="_blank" rel="noopener">View Project ↗</a>
                    <?php endif; ?>

                </div>
            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <p>No projects available yet.</p>

    <?php endif; ?>

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
