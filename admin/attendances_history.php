<?php

session_start();

require_once __DIR__ . "/../core/Autoload.php";

Autoload::register();

Auth::requireLogin();
// Auth::requireAdmin();
$empId='';

if (isset($_GET['id'])) {
    $empId = trim($_GET['id']);
    }

if(empty($empId)){
 Auth::requireAdmin();

}
    
    
    
    
    
$role=Auth::role();
require_once '../views/admin/attendence_history.php';


?>