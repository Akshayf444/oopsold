<?php

session_start();
if (!isset($_SESSION['BM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");

$pageTitle = "View All Profiles";

if (isset($_GET['docid']) || isset($_GET['page'])) {
    
} else {
    redirect_to("BMindex.php");
}
require_once("BMheader.php");
require_once("../viewAllProfileTemplate.php");
require_once("BMfooter.php");
?>