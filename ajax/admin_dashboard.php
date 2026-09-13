
<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";
require_once __DIR__ . '/../vendor/autoload.php';

Autoload::register();


try {

    // =====================================================
    // AUTHENTICATION
    // =====================================================

    Auth::requireLogin();
    Auth::requireAdmin();


    // =====================================================
    // DATABASE
    // =====================================================

    $db = Database::getInstance();


    // =====================================================
    // CONTROLLER
    // =====================================================

    $admindashController =
        new AdminDashController($db);


    // =====================================================
    // GET REQUESTS
    // =====================================================

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $action =
            $_GET['action'] ?? 'index';


        // -------------------------------------------------
        // DASHBOARD STATISTICS
        // -------------------------------------------------

        if ($action === 'dashboard') {

            $admindashController->index();

        }


        // -------------------------------------------------
        // ACTIVE EMPLOYEES
        // -------------------------------------------------

        elseif ($action === 'employees') {

            $admindashController->employees();

        }


        // -------------------------------------------------
        // DEPARTMENTS
        // -------------------------------------------------

        elseif ($action === 'departments') {

            $admindashController->departments();

        }


        // -------------------------------------------------
        // ATTENDANCE OVERVIEW
        // -------------------------------------------------

        elseif ($action === 'attendance_overview') {

            $admindashController->attendanceOverview();

        }


        // -------------------------------------------------
        // TODAY'S ATTENDANCE
        // -------------------------------------------------

        elseif ($action === 'today_attendance') {

            $admindashController->todayAttendance();

        }


        // -------------------------------------------------
        // DEPARTMENT-WISE EMPLOYEES
        // -------------------------------------------------

        elseif ($action === 'department_employees') {

            $admindashController->departmentEmployees();

        }


        // -------------------------------------------------
        // INVALID ACTION
        // -------------------------------------------------

        else {

            jsonResponse(
                [
                    'success' => false,
                    'message' => 'Invalid action.'
                ],
                400
            );

        }

    }


    // =====================================================
    // POST REQUESTS
    // =====================================================

    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

        jsonResponse(
            [
                'success' => false,
                'message' => 'Invalid action.'
            ],
            400
        );

    }


    // =====================================================
    // METHOD NOT ALLOWED
    // =====================================================

    else {

        jsonResponse(
            [
                'success' => false,
                'message' => 'Method not allowed.'
            ],
            405
        );

    }


} catch (PDOException $e) {

    http_response_code(500);

    header(
        'Content-Type: application/json'
    );

    echo json_encode(
        [
            'success' => false,
            'message' => 'Database error.'.$e->getMessage(),
            // Do not expose $e->getMessage()
            // in production
        ]
    );

    exit;


} catch (Exception $e) {

    http_response_code(500);

    header(
        'Content-Type: application/json'
    );

    echo json_encode(
        [
            'success' => false,
            'message' => $e->getMessage()
        ]
    );

    exit;

}


/**
 * =========================================================
 * JSON RESPONSE
 * =========================================================
 */

function jsonResponse(
    array $data,
    int $statusCode = 200
): void {

    http_response_code(
        $statusCode
    );

    header(
        'Content-Type: application/json'
    );

    echo json_encode(
        $data
    );

    exit;
}
