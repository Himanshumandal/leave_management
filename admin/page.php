<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";

Autoload::register();

Auth::requireLogin();



if (!Auth::isAdmin()) { 
        $_SESSION['error_page'] = [ 
            'code' => 403, 'title' => 'Access Denied',
            'message' => "You don't have permission to access this page.", 
            'icon' => 'bi-shield-lock'
        ]; 
        
}

require_once '../views/employee/denied.php';