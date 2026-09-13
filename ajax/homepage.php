<?php

require_once "../core/Autoload.php";

Autoload::register();

header('Content-Type: application/json');

try {

    $action =
        $_GET['action'] ?? '';

    if ($action === 'status') {

        echo json_encode([

            'success' => true,

            'message' =>
                'Employee Management System is running.',

            'data' => [

                'status' => 'online'

            ]

        ]);

        exit;
    }


    echo json_encode([

        'success' => false,

        'message' =>
            'Invalid action.'

    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([

        'success' => false,

        'message' =>
            'Unable to process request.'

    ]);

}