<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['logged_in'])) {

    if ($_SESSION['role'] === 'admin') {

        require_once '../dashboard.php';

    } elseif ($_SESSION['role'] === 'employee') {

        require_once '../employee/dashboard.php';

    }

    exit;
}


// User is not logged in
require_once '../views/auth/login.php';

exit;