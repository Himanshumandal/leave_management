<?php

require_once __DIR__ . "/../core/Autoload.php";

Autoload::register();

$secret = env('CRON_SECRET');

$providedKey = $_GET['key'] ?? '';

if (!$secret || !hash_equals($secret, $providedKey)) {
    http_response_code(403);
    exit('Forbidden');
}

require_once __DIR__ . "/reset_ai_chat.php";

echo "AI chat limits reset successfully.";