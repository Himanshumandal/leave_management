<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";

Autoload::register();

Auth::requireLogin();
Auth::requireAdmin();

$employeeId = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($employeeId <= 0) {

    header('Location: employees.php');

    exit;
}

require_once __DIR__.  '/../views/admin/employees_details.php';