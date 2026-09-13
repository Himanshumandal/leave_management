<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";
require_once __DIR__ . '/../vendor/autoload.php';


Autoload::register();

try {

    /*
        |--------------------------------------------------------------------------
        | AI Service Authentication Key
        |--------------------------------------------------------------------------
        */

        $aiServiceKey = env('AI_SERVICE_KEY');

        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        $isAiService = false;

        $requestAiKey = $_SERVER['HTTP_X_AI_SERVICE_KEY'] ?? '';

        

        if (
            !empty($requestAiKey) &&
            !empty($aiServiceKey) &&
            hash_equals($aiServiceKey, $requestAiKey)
        ) {
       
            $isAiService = true;
        }

        /*
        |--------------------------------------------------------------------------
        | Normal User Authentication
        |--------------------------------------------------------------------------
        */

        if (!$isAiService) {
            Auth::requireLogin();
        }

   

    // Get database connection
    $db = Database::getInstance();

    // Create AuthController
    $attendencController = new AttendanceController($db);

    // Handle login
      // GET → Read employees
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {


        $action = $_GET['action'] ?? 'index';


        if ($action === 'index') {
             Auth::requireAdmin();
            $attendencController->index();

        }

        elseif($action=== 'history'){


            $attendencController->history();
        }
       elseif($action=== 'summary'){
         Auth::requireAdmin();
            $attendencController->summary();
        }
        elseif($action === 'today_attendance'){
            $attendencController->todayAttendence();
        }elseif($action == 'get_individual_attendance'){
            if (!$isAiService) {

                        http_response_code(403);

                        echo json_encode([
                            'success' => false,
                            'message' =>
                                'Unauthorized AI service request.'
                        ]);

                        exit;
                    }

            $attendencController->get_indi_attendance_for_AI();
        }


        // if ($action === 'show') {

        //     $empController->show();

        // }


        jsonResponse([
            'success' => false,
            'message' => 'Invalid action.'
        ], 400);
    }


    // POST → Create employee + user
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        Auth::requireAdmin();
         $action = $_POST['action'] ?? 'save';


        if ($action === 'save') {

            $attendencController->save();

        }


        if ($action === 'update') {

            // $attendencController->update();

        }

        if ($action === 'delete') {
            // $attendencController->destroy();
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