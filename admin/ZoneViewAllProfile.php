<?php

require_once("../includes/initialize.php");

if (isset($_GET['docid']) || isset($_GET['page'])) {
    
} else {
    redirect_to("Dashboard.php");
}

$pageTitle = "View All Profiles";
require_once("zoneheader.php");
require_once("../viewAllProfileTemplate.php");
require_once("zonefooter.php");
?>