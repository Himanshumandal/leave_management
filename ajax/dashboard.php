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


    // =====================================================
    // DATABASE
    // =====================================================

    $db = Database::getInstance();


    // =====================================================
    // CONTROLLER
    // =====================================================

    $employeeDashController =
        new EmployeeDashController($db);


    // =====================================================
    // GET REQUEST
    // =====================================================

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

        jsonResponse([
            'success' => false,
            'message' => 'Method not allowed.'
        ], 405);
    }


    // =====================================================
    // ACTION
    // =====================================================

    $action =
        $_GET['action'] ?? 'dashboard';


    // =====================================================
    // DASHBOARD
    // =====================================================

    if ($action === 'dashboard') {

        $employeeDashController->index();

    }


    // =====================================================
    // ATTENDANCE TREND
    // =====================================================

    elseif ($action === 'attendance_trend') {

        $employeeDashController
            ->attendanceTrend();

    }


    // =====================================================
    // ATTENDANCE STATUS
    // =====================================================

    elseif ($action === 'attendance_status') {

        $employeeDashController
            ->attendanceStatus();

    }


    // =====================================================
    // WORKING HOURS
    // =====================================================

    elseif ($action === 'working_hours') {

        $employeeDashController
            ->workingHours();

    }


    // =====================================================
    // INVALID ACTION
    // =====================================================

    else {

        jsonResponse([
            'success' => false,
            'message' => 'Invalid action.'
        ], 400);

    }


} catch (PDOException $e) {

    jsonResponse([
        'success' => false,
        'message' => 'Database error.'
    ], 500);


} catch (Exception $e) {

    jsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 500);

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

    http_response_code($statusCode);

    header(
        'Content-Type: application/json'
    );

    echo json_encode($data);

    exit;
}
