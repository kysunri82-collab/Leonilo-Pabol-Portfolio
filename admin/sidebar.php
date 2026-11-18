<?php
$_adminUiCurrentPage = basename($_SERVER['PHP_SELF'] ?? '');
$_adminUiNavigation = [
    ["label" => "Dashboard", "href" => "dashboard.php", "pages" => ["dashboard.php"], "icon" => "<rect x=\"3\" y=\"3\" width=\"7\" height=\"7\" rx=\"1\"/><rect x=\"14\" y=\"3\" width=\"7\" height=\"7\" rx=\"1\"/><rect x=\"14\" y=\"14\" width=\"7\" height=\"7\" rx=\"1\"/><rect x=\"3\" y=\"14\" width=\"7\" height=\"7\" rx=\"1\"/>"],
    ["label" => "Profile", "href" => "profile.php", "pages" => ["profile.php"], "icon" => "<circle cx=\"12\" cy=\"8\" r=\"4\"/><path d=\"M5 21a7 7 0 0 1 14 0\"/>"],
    ["label" => "Website Settings", "href" => "settings.php", "pages" => ["settings.php"], "icon" => "<circle cx=\"12\" cy=\"12\" r=\"3\"/><path d=\"M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.8 3.1-.2-.1a1.7 1.7 0 0 0-1.8.1l-.1.1h-3.6l-.1-.2a1.7 1.7 0 0 0-1.5-1l-.2.1-3.1-1.8.1-.2a1.7 1.7 0 0 0-.1-1.8l-.1-.1v-3.6l.2-.1a1.7 1.7 0 0 0 1-1.5l-.1-.2 1.8-3.1.2.1a1.7 1.7 0 0 0 1.8-.1l.1-.1h3.6l.1.2a1.7 1.7 0 0 0 1.5 1l.2-.1 3.1 1.8-.1.2a1.7 1.7 0 0 0 .1 1.8l.1.1V15z\"/>"],
    ["label" => "Projects", "href" => "projects.php", "pages" => ["projects.php", "add_project.php", "edit_project.php"], "icon" => "<rect x=\"3\" y=\"4\" width=\"18\" height=\"16\" rx=\"2\"/><path d=\"M8 4V2m8 2V2M3 10h18\"/>"],
    ["label" => "Skills", "href" => "skills.php", "pages" => ["skills.php", "add_skill.php", "edit_skill.php"], "icon" => "<path d=\"m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3z\"/><path d=\"m19 14 .9 2.1L22 17l-2.1.9L19 20l-.9-2.1L16 17l2.1-.9L19 14z\"/>"],
    ["label" => "Education", "href" => "education.php", "pages" => ["education.php", "add_education.php", "edit_education.php"], "icon" => "<path d=\"m2 9 10-5 10 5-10 5L2 9z\"/><path d=\"M6 11v5c3.5 3 8.5 3 12 0v-5M22 9v6\"/>"],
    ["label" => "Experience", "href" => "experience.php", "pages" => ["experience.php", "add_experience.php", "edit_experience.php"], "icon" => "<rect x=\"3\" y=\"7\" width=\"18\" height=\"14\" rx=\"2\"/><path d=\"M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18m-11 0v2h4v-2\"/>"],
    ["label" => "Achievements", "href" => "achievements.php", "pages" => ["achievements.php", "add_achievement.php", "edit_achievement.php"], "icon" => "<path d=\"m12 3 2.4 5 5.6.8-4 3.9.9 5.5-4.9-2.6-4.9 2.6.9-5.5-4-3.9 5.6-.8L12 3z\"/>"],
    ["label" => "Messages", "href" => "messages.php", "pages" => ["messages.php", "view_message.php"], "icon" => "<rect x=\"3\" y=\"5\" width=\"18\" height=\"14\" rx=\"2\"/><path d=\"m4 7 8 6 8-6\"/>"],
];
?>
<aside class="admin-sidebar" aria-label="Admin navigation">
    <a class="admin-brand" href="dashboard.php">
        <span class="admin-brand-mark" aria-hidden="true">LP</span>
        <span class="admin-brand-copy"><strong>Portfolio Admin</strong><small>Management workspace</small></span>
    </a>
    <p class="admin-nav-label">Workspace</p>
    <nav class="admin-nav">
        <?php foreach ($_adminUiNavigation as $_adminUiItem): ?>
            <?php $_adminUiActive = in_array($_adminUiCurrentPage, $_adminUiItem["pages"], true); ?>
            <a href="<?= htmlspecialchars($_adminUiItem["href"], ENT_QUOTES, 'UTF-8') ?>" class="<?= $_adminUiActive ? 'active' : '' ?>"<?= $_adminUiActive ? ' aria-current="page"' : '' ?>>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $_adminUiItem["icon"] ?></svg>
                <span><?= htmlspecialchars($_adminUiItem["label"], ENT_QUOTES, 'UTF-8') ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-footer">
        <button type="button" class="theme-toggle" aria-label="Toggle dark mode">🌙 Dark</button>
        <a href="../index.php">
            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3h7v7m0-7L10 14"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
            View portfolio
        </a>
    </div>
</aside>
