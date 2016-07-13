<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

require_once(dirname(__FILE__) . "/includes/initialize.php");

$empid = $_SESSION['employee'];
$employee = Employee::find_by_empid($_SESSION['employee']);
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 1;

$pageTitle = "View Doctor Profile";
require_once("layouts/TMheader.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Doctor Profile</h1>
    </div>      
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="col-lg-3">
        </div> 
        <div class="col-lg-9">
            
        </div> 
    </div>      
</div>
<?php
require_once("layouts/TMfooter.php");


