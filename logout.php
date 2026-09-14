<?php

session_start();

require_once "./core/Autoload.php";

Autoload::register();

Auth::logout();

header('Location: /auth/login.php');

exit;