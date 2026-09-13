<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";
require_once __DIR__ . '/../vendor/autoload.php';

Autoload::register();

try {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Auth::requireLogin();

    if (
    !Auth::verifyCsrfToken(
            $_POST['csrf_token'] ?? null
        )
    ) {
        jsonResponse([
            'success' => false,
            'message' => 'Invalid request.'
        ], 403);
    }


    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */

    $db = Database::getInstance();


    /*
    |--------------------------------------------------------------------------
    | Chat Controller
    |--------------------------------------------------------------------------
    */

    $chatController = new ChatController($db);


    /*
    |--------------------------------------------------------------------------
    | Only POST is allowed
    |--------------------------------------------------------------------------
    */

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        jsonResponse([
            'success' => false,
            'message' => 'Method not allowed.'
        ], 405);
    }


    /*
    |--------------------------------------------------------------------------
    | Action
    |--------------------------------------------------------------------------
    */

    $action = $_POST['action'] ?? 'store';


    /*
    |--------------------------------------------------------------------------
    | Store Chat Message
    |--------------------------------------------------------------------------
    */

    if ($action === 'store') {

        $chatController->store();
    }


    /*
    |--------------------------------------------------------------------------
    | Invalid Action
    |--------------------------------------------------------------------------
    */

    jsonResponse([
        'success' => false,
        'message' => 'Invalid action.'
    ], 400);


} catch (PDOException $e) {

    error_log(
        "CHAT DATABASE ERROR: " .
        $e->getMessage()
    );

    jsonResponse([
        'success' => false,
        'message' => 'Database error.'
    ], 500);


} catch (Throwable $e) {

      error_log(
        "CHAT ERROR: " .
        $e->getMessage() .
        " | FILE: " .
        $e->getFile() .
        " | LINE: " .
        $e->getLine()
    );

    jsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 500);
}


/*
|--------------------------------------------------------------------------
| JSON Response Helper
|--------------------------------------------------------------------------
*/

function jsonResponse(
    array $data,
    int $statusCode = 200
): void {

    http_response_code($statusCode);

    header('Content-Type: application/json');

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}