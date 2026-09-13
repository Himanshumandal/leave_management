<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }
    require_once __DIR__ . "/../core/Autoload.php";
    Autoload::register();


Auth::requireLogin();


if (!Auth::isEmployee()) { 

        header("Location: page.php"); 
        exit; 
    }

require_once '../views/employee/dashboard.php';