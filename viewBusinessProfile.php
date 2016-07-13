<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
require_once(dirname(__FILE__) . "/includes/class.product.php");

if (!isset($_GET['docid'])) {
    redirect_to("index.php");
} else {
    $docid = $_GET['docid'];
    $doctor = Doctor::find_by_docid($docid);
}

$empid = $_SESSION['employee'];

require_once("layouts/TMheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo ucfirst($doctor->name); ?></h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row" style="overflow-x: auto">
    <div class="col-lg-12 col-md-12"  >
        <?php echo BusiProfile::viewProfile($_GET['docid']); ?>
    </div>
</div>
<?php require_once("layouts/TMfooter.php"); ?>