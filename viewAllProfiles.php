<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee']; 
if (isset($_GET['docid']) || isset($_GET['page'])) {
    $pageTitle = "View Profiles";
    $empName = Employee::find_by_empid($empid);    
} else {
    redirect_to('index.php');
}

require_once("layouts/TMheader.php");
require_once("viewAllProfileTemplate.php");
require_once("layouts/TMfooter.php"); ?>