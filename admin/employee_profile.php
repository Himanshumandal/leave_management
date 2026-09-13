<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";

Autoload::register();

Auth::requireLogin();
// Auth::requireAdmin();


$employeeId=0;

$encryptedId =
    trim(
        $_GET['id'] ?? ''
    );



$employeeId =
    UrlEncryptor::decrypt(
        $encryptedId
    );

if($encryptedId!=''){
    $employeeId =
    UrlEncryptor::decrypt(
        $encryptedId
    );

}else if(Auth::userId()>0){
    $employeeId=Auth::userId()-1;
}


if ($employeeId <= 0) {

    header('Location: employees.php');

    exit;
}

require_once __DIR__.  '/../views/admin/employee_profile.php';