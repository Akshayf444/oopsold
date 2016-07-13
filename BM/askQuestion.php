<?php
session_start();
if (!isset($_SESSION['BM'])) {
    header("Location : logout.php");
}
require_once("../includes/initialize.php");
require_once("BMheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Ask Question</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row" >
    <div class="col-lg-11 answerlist" >
        <?php require_once("../askQuestionTemplate.php"); ?>
    </div>      <!-- /.col-lg-12 -->
</div>
<?php
require_once("BMfooter.php");
