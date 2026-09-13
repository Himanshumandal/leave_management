<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";

Autoload::register();

Auth::requireLogin();
Auth::requireAdmin();
require_once '../views/admin/attendence.php';
?>