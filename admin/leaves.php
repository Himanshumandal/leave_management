<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";

Autoload::register();

Auth::requireLogin();
Auth::requireAdmin();
// Auth::requireAdmin();
require_once '../views/admin/leaves.php';

?>