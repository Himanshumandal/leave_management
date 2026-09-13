<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";

Autoload::register();

Auth::requireAdmin();

require_once '../views/admin/employees.php';