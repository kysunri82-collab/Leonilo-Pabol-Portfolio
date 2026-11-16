<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/site_settings.php';

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, max-age=0");

echo json_encode(
    getWebsiteSettings($pdo),
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
?>
