<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";

Autoload::register();

try {

    // Get database connection
    $db = Database::getInstance();

    // Create AuthController
    $authController = new AuthController($db);

    // Handle login
    $authController->login();

} catch (PDOException $e) {

    http_response_code(500);

    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);

} catch (Exception $e) {

    http_response_code(500);

    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}