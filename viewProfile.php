<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

require_once(dirname(__FILE__) . "/includes/initialize.php");


$pageTitle = "View Basic Profile";
$empid = $_SESSION['employee'];

$employee = Employee::find_by_empid($empid);


$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;

$per_page = 1;

$total_count = BasicProfile::count_all($empid);
$pagination = new Pagination($page, $per_page, $total_count);

$sql = "SELECT db.* FROM doc_basic_profile db INNER JOIN doctors d ON d.docid = db.docid  WHERE d.is_delete =0 AND db.empid = '$empid' ";
$sql .= "LIMIT {$per_page} ";
$sql .= "OFFSET {$pagination->offset()}";

$BasicProfile = QueryWrapper::executeQuery($sql);
$finalBasicProfie = !empty($BasicProfile) ? array_shift($BasicProfile) : FALSE;

$doctorName = Doctor::find_by_docid($finalBasicProfie->docid);

$address1 = array($finalBasicProfie->plot1, $finalBasicProfie->street1, $finalBasicProfie->area1, $finalBasicProfie->city1, $finalBasicProfie->state1, $finalBasicProfie->pincode1);
$address2 = array($finalBasicProfie->plot2, $finalBasicProfie->street2, $finalBasicProfie->area2, $finalBasicProfie->city2, $finalBasicProfie->state2, $finalBasicProfie->pincode2);
require_once("layouts/TMheader.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $doctorName->name . " "; ?><small>Basic Profile</small></h1>
    </div>      
</div>

<div class ="row">

    <div class="col-lg-12" style="clear: both;">
        <?php
        if ($pagination->total_pages() > 1) {

            if ($pagination->has_previous_page()) {
                echo "<div class='col-lg-1'> <a class ='btn btn-default' href=\"viewProfile.php?page=";
                echo $pagination->previous_page();
                echo "\">&laquo; Previous</a> </div>";
            }
            echo '<div class="col-lg-10" style = "text-align:center">';
            for ($i = 1; $i <= $pagination->total_pages(); $i++) {
                if ($i == $page) {
                    echo " <span class=\"selected\"  style='color:red;padding:1px;font-weight:bold;border:1px solid aqua;radius:1px;'>{$i}</span> ";
                } else {
                    echo " <a href=\"viewProfile.php?page={$i}\">{$i}</a> ";
                }
            }
            echo '</div>';

            if ($pagination->has_next_page()) {
                echo "<div class ='col-lg-1 pull-right'> <a class ='btn btn-default' href=\"viewProfile.php?page=";
                echo $pagination->next_page();
                echo "\">Next &raquo;</a></div> ";
            }
        }
        ?>
    </div>
</div>
<style>
    table td {overflow-wrap: break-word;}
</style>
<div class ="row row-margin-top ">
    <div class="col-lg-12 col-xs-12">
        <div style="margin-top: 1em" >
            <?php echo BasicProfile::view_basic_profile($finalBasicProfie); ?>
        </div>
    </div>
</div>
<hr>
<div class="row" style="padding:1em">
    <a href="editBasicProfile.php?docid=<?php echo $finalBasicProfie->docid; ?>&page=<?php echo $page ?>" > <input type="button" value="Edit Profile" title="Edit Profile" style="display:<?php
        if ($employee->lock_basic == 1) {
            echo "none";
        }
        ?>" class="btn btn-primary" /></a><br/>

</div>

<?php
require_once("layouts/TMfooter.php");
