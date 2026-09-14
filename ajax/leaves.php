<?php

    session_start();

    require_once __DIR__ . '/../core/Autoload.php';
    require_once __DIR__ . '/../vendor/autoload.php';

    Autoload::register();

    header('Content-Type: application/json');

    try {
        error_log(
            "LEAVE API START: " .
            date('H:i:s') .
            " PID=" . getmypid()
        );
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

        /*
        |--------------------------------------------------------------------------
        | Database
        |--------------------------------------------------------------------------
        */

        $db = Database::getInstance();

        /*
        |--------------------------------------------------------------------------
        | Controller
        |--------------------------------------------------------------------------
        */

        $leaveController = new LeaveController($db);

        /*
        |--------------------------------------------------------------------------
        | Request Information
        |--------------------------------------------------------------------------
        */

        $method = $_SERVER['REQUEST_METHOD'];

        $action = $_GET['action']
            ?? $_POST['action']
            ?? '';

        /*
        |--------------------------------------------------------------------------
        | POST Requests
        |--------------------------------------------------------------------------
        */

        if ($method === 'POST') {

            switch ($action) {

                /*
                | Apply for leave
                */
                case 'store':

                    $leaveController->store();

                    break;


                /*
                | Update leave status
                */
                case 'update_status':

                    Auth::requireAdmin();

                    $leaveController->updateStatus();

                    break;


                /*
                | Invalid action
                */
                default:

                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid action.'
                    ]);

                    break;
            }

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | GET Requests
        |--------------------------------------------------------------------------
        */

        if ($method === 'GET') {

            switch ($action) {

                /*
                | Employee's own leave list
                */
                case 'my_leaves':

                    $leaveController->myLeaves();

                    break;


                /*
                | Employee leave details / balance
                */
                case 'get_leave':

                    $leaveController->getLeaves();

                    break;


                /*
                | Admin leave management
                */
                case 'admin_leaves':

                    Auth::requireAdmin();

                    $leaveController->adminLeaves();

                    break;

                case 'ai_get_leave':

                    /*
                    | Only trusted Python AI service
                    | can access this endpoint.
                    */

                    if (!$isAiService) {

                        http_response_code(403);

                        echo json_encode([
                            'success' => false,
                            'message' =>
                                'Unauthorized AI service request.'
                        ]);

                        exit;
                    }


                    $leaveController->getLeavesForAI();
                
                    break;


                /*
                | Invalid action
                */
                default:

                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid action.'
                    ]);

                    break;
            }

                error_log(
                        "LEAVE API END: " .
                        date('H:i:s') .
                        " PID=" . getmypid()
                    );

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | Unsupported HTTP Method
        |--------------------------------------------------------------------------
        */

        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed.'
        ]);

    } catch (PDOException $e) {

        /*
        |--------------------------------------------------------------------------
        | Database Error
        |--------------------------------------------------------------------------
        */

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Database error.'
        ]);

    } catch (Exception $e) {

        /*
        |--------------------------------------------------------------------------
        | General Error
        |--------------------------------------------------------------------------
        */

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }