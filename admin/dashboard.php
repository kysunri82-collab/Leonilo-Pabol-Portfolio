
<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$stats = [
    "projects" => null,
    "skills" => null,
    "education" => null,
    "experience" => null,
    "achievements" => null,
    "messages" => null,
    "unread_messages" => null
];
$statsError = false;

try {
    $stmt = $pdo->prepare(
        "SELECT
            (SELECT COUNT(*) FROM projects) AS project_count,
            (SELECT COUNT(*) FROM skills) AS skill_count,
            (SELECT COUNT(*) FROM education) AS education_count,
            (SELECT COUNT(*) FROM experience) AS experience_count,
            (SELECT COUNT(*) FROM achievements) AS achievement_count,
            (SELECT COUNT(*) FROM contact_messages) AS message_count,
            (SELECT COUNT(*) FROM contact_messages WHERE is_read = 0) AS unread_message_count"
    );
    $stmt->execute();
    $counts = $stmt->fetch();

    if (!$counts) {
        $statsError = true;
    } else {
        $stats = [
            "projects" => (int) $counts["project_count"],
            "skills" => (int) $counts["skill_count"],
            "education" => (int) $counts["education_count"],
            "experience" => (int) $counts["experience_count"],
            "achievements" => (int) $counts["achievement_count"],
            "messages" => (int) $counts["message_count"],
            "unread_messages" => (int) $counts["unread_message_count"]
        ];
    }
} catch (Throwable $error) {
    $statsError = true;
}

$statLabels = [
    "projects" => "Projects",
    "skills" => "Skills",
    "education" => "Education Records",
    "experience" => "Experience Records",
    "achievements" => "Achievements",
    "messages" => "Contact Messages",
    "unread_messages" => "Unread Messages"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>

    <h1>Admin Dashboard</h1>

    <h2>
        Welcome, <?= htmlspecialchars($_SESSION["admin_username"], ENT_QUOTES, 'UTF-8') ?>!
    </h2>

    <p>You are successfully logged in.</p>

    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin: 24px 0;
        }

        .dashboard-stat {
            padding: 16px;
            border: 1px solid #d7e1ea;
            border-radius: 8px;
            background: #f7f9fb;
        }

        .dashboard-stat strong {
            display: block;
            font-size: 28px;
            line-height: 1.2;
        }

        .dashboard-stat span {
            display: block;
            margin-top: 6px;
        }

        @media (max-width: 480px) {
            .dashboard-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .dashboard-stat {
                padding: 12px;
            }
        }
    </style>

    <h2>Portfolio Statistics</h2>

    <?php if ($statsError): ?>
        <p role="status">Statistics are temporarily unavailable.</p>
    <?php endif; ?>

    <section class="dashboard-stats" aria-label="Portfolio statistics">
        <?php foreach ($statLabels as $key => $label): ?>
            <div class="dashboard-stat">
                <strong><?= htmlspecialchars($statsError ? '—' : (string) $stats[$key], ENT_QUOTES, 'UTF-8') ?></strong>
                <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endforeach; ?>
    </section>

    <p>
    <a href="profile.php">Manage My Profile</a>
</p>
<p>
    <a href="settings.php">Website Settings</a>
</p>
<p>
    <a href="projects.php">Manage My Projects</a>
</p>
<p>
    <a href="skills.php">Manage My Skills</a>
</p>
<p>
    <a href="education.php">Manage Education</a>
</p>
<p>
    <a href="experience.php">Manage Experience</a>
</p>
<p>
    <a href="achievements.php">Manage Achievements</a>
</p>
<p>
    <a href="messages.php">Manage Messages</a>
</p>

    <a href="logout.php">Logout</a>

</body>
</html>
