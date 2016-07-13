<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location:logout.php");
}
require_once("../includes/initialize.php");

if (isset($_GET['docid']) || isset($_GET['page'])) {
    
}  else {
    redirect_to("Dashboard.php");
}

$pageTitle = "View All Profiles";
require_once("adminheader.php");
require_once("../viewAllProfileTemplate.php");
require_once("adminfooter.php");
?>