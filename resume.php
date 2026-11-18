<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/site_settings.php';

$websiteSettings = getWebsiteSettings($pdo);

$educationRecords = [];
$experienceRecords = [];
$achievementRecords = [];
$educationLoadError = false;
$experienceLoadError = false;
$achievementLoadError = false;

try {
    $stmt = $pdo->query(
        "SELECT school, degree, start_year, end_year, description
         FROM education
         ORDER BY display_order ASC, id ASC"
    );
    $educationRecords = $stmt->fetchAll();
} catch (Throwable $error) {
    $educationLoadError = true;
}

try {
    $stmt = $pdo->query(
        "SELECT job_title, organization, start_date, end_date, description
         FROM experience
         ORDER BY display_order ASC, id ASC"
    );
    $experienceRecords = $stmt->fetchAll();
} catch (Throwable $error) {
    $experienceLoadError = true;
}

try {
    $stmt = $pdo->query(
        "SELECT title, organization, achievement_date, description
         FROM achievements
         ORDER BY display_order ASC, id ASC"
    );
    $achievementRecords = $stmt->fetchAll();
} catch (Throwable $error) {
    $achievementLoadError = true;
}

$escapeValue = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

$formatTimelineDate = static function ($value): string {
    $date = trim((string) ($value ?? ''));
    if ($date === '') {
        return '';
    }

    return preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date)
        ? substr($date, 0, 4)
        : $date;
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($websiteSettings['website_title'] . ' | Resume', ENT_QUOTES, 'UTF-8') ?></title>
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
      <li><a href="projects.php">Projects</a></li>
      <li><a href="resume.php">Resume</a></li>
      <li><a href="contact.php">Contact</a></li>
    </ul>
    <button class="theme-toggle" aria-label="Toggle dark mode">&#127769; Dark</button>
    <button class="hamburger" aria-label="Open menu" aria-expanded="false">&#9776;</button>
  </div>
</header>

<section class="section">
  <div class="wrap">
    <h1 class="section-title">Resume / Experience</h1>
    <p class="section-sub">Education, organizations, and professional seminars.</p>

    <section aria-labelledby="education-heading">
      <h2 id="education-heading">Education</h2>
      <?php if ($educationLoadError): ?>
        <p>Unable to load education records right now.</p>
      <?php elseif ($educationRecords === []): ?>
        <p>No education records are available yet.</p>
      <?php else: ?>
        <ol class="timeline">
          <?php foreach ($educationRecords as $record): ?>
            <?php
            $startYear = trim((string) ($record['start_year'] ?? ''));
            $endYear = trim((string) ($record['end_year'] ?? ''));
            $period = $startYear;
            if ($endYear !== '') {
                $period = $period !== '' ? $period . ' — ' . $endYear : $endYear;
            } elseif ($startYear !== '') {
                $period .= ' — Present';
            }
            $description = trim((string) ($record['description'] ?? ''));
            ?>
            <li>
              <?php if ($period !== ''): ?><span class="when"><?= $escapeValue($period) ?></span><?php endif; ?>
              <h3><?= $escapeValue($record['school'] ?? '') ?></h3>
              <p><?= $escapeValue($record['degree'] ?? '') ?></p>
              <?php if ($description !== ''): ?><p><?= $escapeValue($description) ?></p><?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>
    </section>

    <section aria-labelledby="experience-heading">
      <h2 id="experience-heading">Experience</h2>
      <?php if ($experienceLoadError): ?>
        <p>Unable to load experience records right now.</p>
      <?php elseif ($experienceRecords === []): ?>
        <p>No experience records are available yet.</p>
      <?php else: ?>
        <ol class="timeline">
          <?php foreach ($experienceRecords as $record): ?>
            <?php
            $startDate = $formatTimelineDate($record['start_date'] ?? null);
            $endDate = $formatTimelineDate($record['end_date'] ?? null);
            $period = $startDate;
            if ($endDate !== '') {
                $period = $period !== '' ? $period . ' — ' . $endDate : $endDate;
            } elseif ($startDate !== '') {
                $period .= ' — Present';
            }
            $organization = trim((string) ($record['organization'] ?? ''));
            $description = trim((string) ($record['description'] ?? ''));
            ?>
            <li>
              <?php if ($period !== ''): ?><span class="when"><?= $escapeValue($period) ?></span><?php endif; ?>
              <h3><?= $escapeValue($record['job_title'] ?? '') ?></h3>
              <?php if ($organization !== ''): ?><p><?= $escapeValue($organization) ?></p><?php endif; ?>
              <?php if ($description !== ''): ?><p><?= $escapeValue($description) ?></p><?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>
    </section>

    <section aria-labelledby="achievements-heading">
      <h2 id="achievements-heading">Achievements</h2>
      <?php if ($achievementLoadError): ?>
        <p>Unable to load achievement records right now.</p>
      <?php elseif ($achievementRecords === []): ?>
        <p>No achievements are available yet.</p>
      <?php else: ?>
        <ol class="timeline">
          <?php foreach ($achievementRecords as $record): ?>
            <?php
            $achievementDate = $formatTimelineDate($record['achievement_date'] ?? null);
            $organization = trim((string) ($record['organization'] ?? ''));
            $description = trim((string) ($record['description'] ?? ''));
            ?>
            <li>
              <?php if ($achievementDate !== ''): ?><span class="when"><?= $escapeValue($achievementDate) ?></span><?php endif; ?>
              <h3><?= $escapeValue($record['title'] ?? '') ?></h3>
              <?php if ($organization !== ''): ?><p><?= $escapeValue($organization) ?></p><?php endif; ?>
              <?php if ($description !== ''): ?><p><?= $escapeValue($description) ?></p><?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>
    </section>
  </div>
</section>

<footer>
  <p>Thanks for stopping by.</p>
  <p>Leonilo Pabol's Portfolio &bull; <span id="year"></span></p>
</footer>

<button id="scroll-top-btn" aria-label="Scroll to top">&uarr;</button>
<script>document.getElementById('year').textContent = new Date().getFullYear();</script>
</body>
</html>
