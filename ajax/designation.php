<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";
require_once __DIR__ . '/../vendor/autoload.php';


Autoload::register();

try {

    Auth::requireLogin();
    Auth::requireAdmin();

    

    // Get database connection
    $db = Database::getInstance();

    // Create AuthController
    $desController = new DesignationController($db);

    // Handle login
      // GET → Read employees
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $action = $_GET['action'] ?? 'index';


        if ($action === 'index') {

            $desController->index();

        }


        if ($action === 'show') {

            $desController->show();

        }


        jsonResponse([
            'success' => false,
            'message' => 'Invalid action.'
        ], 400);
    }


    // POST → Create employee + user
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

         $action = $_POST['action'] ?? 'store';


        if ($action === 'store') {

            $desController->store();

        }


        if ($action === 'update') {

            $desController->update();

        }

        if ($action === 'delete') {
            $desController->destroy();
        }


        jsonResponse([
            'success' => false,
            'message' => 'Invalid action.'
        ], 400);

    }else{
        http_response_code(405);

        header('Content-Type: application/json');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        exit;
    }

} catch (PDOException $e) {

    http_response_code(500);

    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'message' => 'Database error'.$e->getMessage()
    ]);

} catch (Exception $e) {

    http_response_code(500);

    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function jsonResponse(
    array $data,
    int $statusCode = 200
): void {

    http_response_code($statusCode);

    header('Content-Type: application/json');

    echo json_encode($data);

    exit;
}