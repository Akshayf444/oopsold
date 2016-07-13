<?php
session_start();
if (!isset($_SESSION['SM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");

if (isset($_GET['docid']) || isset($_GET['page'])) {
    
}  else {
    redirect_to("SMindex.php");
}

$pageTitle = "View All Profiles";
require_once("SMheader.php");
require_once("../viewAllProfileTemplate.php");
require_once("SMfooter.php");
?>