<?php

require_once __DIR__ . "/../core/Autoload.php";

Autoload::register();

try {

    $db = Database::getInstance();

    $sql = "
        UPDATE employees
        SET max_ai_chat = 5
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute();

    error_log(
        "AI CHAT LIMIT RESET SUCCESS | " .
        "EMPLOYEES RESET: " .
        $stmt->rowCount() .
        " | TIME: " .
        date('Y-m-d H:i:s')
    );

} catch (Throwable $e) {

    error_log(
        "AI CHAT LIMIT RESET ERROR | " .
        $e->getMessage()
    );

    exit(1);
}