<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

require_once(dirname(__FILE__) . "/includes/initialize.php");

$employee = Employee::find_by_empid($_SESSION['employee']);
$empid = $_SESSION['employee'];

$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;

$per_page = 1;

$total_count = Services::count_all($empid);
$pagination = new Pagination($page, $per_page, $total_count);

$sql = "SELECT s.* FROM services s INNER JOIN doctors d ON d.docid = s.docid  WHERE d.is_delete = 0 AND s.empid = '$empid' ";
$sql .= "LIMIT {$per_page} ";
$sql .= "OFFSET {$pagination->offset()}";
$services = Services::find_by_sql($sql);
$service = !empty($services) ? array_shift($services) : FALSE;
$doctorName = Doctor::find_by_docid($service->docid);

$pageTitle = "View Service Profile";

require_once("layouts/TMheader.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $doctorName->name . " "; ?><small>Service Profile</small></h1>
    </div>      
</div>
<div class ="row">

    <div class="col-lg-12" style="clear: both;">
        <?php
        if ($pagination->total_pages() > 1) {

            if ($pagination->has_previous_page()) {
                echo "<div class='col-lg-1'> <a class ='btn btn-default' href=\"viewServiceDetails.php?page=";
                echo $pagination->previous_page();
                echo "\">&laquo; Previous</a></div> ";
            }

            echo '<div class="col-lg-10" style = "text-align:center">';
            for ($i = 1; $i <= $pagination->total_pages(); $i++) {
                if ($i == $page) {
                    echo " <span class=\"selected\"  style='color:red;padding:1px;font-weight:bold;border:1px solid aqua;radius:1px;'>{$i}</span> ";
                } else {
                    echo " <a href=\"viewServiceDetails.php?page={$i}\">{$i}</a> ";
                }
            }
            echo '</div>';

            if ($pagination->has_next_page()) {
                echo "<div class ='col-lg-1 pull-right'> <a class ='btn btn-default' href=\"viewServiceDetails.php?page=";
                echo $pagination->next_page();
                echo "\">Next &raquo;</a> </div>";
            }
        }
        ?>
    </div>
</div>

<div class ="row row-margin-top col-lg-12">

    <table cellspacing="0" class="table table-bordered">

        <tr>
            <td>Services Provided To Doctor:</td>
            <td>
                <?php echo $service->aushadh; ?>
            </td>
        </tr>

        <tr>
            <td>Activities With Doctors</td>
            <td>
                <?php echo $service->factors; ?>
            </td>
        </tr>
        <tr>
            <th colspan="2">Services By Other Competing Companies</th>
        </tr>	
        <tr>
            <td>High Value Gifts  </td>	
            <td><?php echo $service->AOIC; ?></td>
        </tr>
        <tr>
            <td>Special Rate  </td>
            <td><?php echo $service->DOC; ?></td>
        </tr>
        <tr>
            <td>Bulk Sampling  </td>
            <td><?php echo $service->ESCRS; ?></td>
        </tr>
        <tr>
            <td>Post-op pouches / cards </td>
            <td><?php echo $service->WGC; ?></td>
        </tr>
        <tr>
            <td>Journals/Books/Online Subscription</td>
            <td><?php echo $service->WOC; ?></td>
        </tr>
        <tr>
            <td>Conferences</td>
            <td><?php echo $service->Other; ?></td>
        </tr>
        </tr>
    </table>
</div>
<hr>	
<div class="row" style="padding:1em">
    <a href="editService.php?docid=<?php echo $service->docid; ?>"> <input type="button" value="Edit Service" title="Edit Service" style="display:<?php
        if ($employee->lock_service == 1) {
            echo "none";
        }
        ?>" class="btn btn-primary" /></a><br/>
</div>
<?php
require_once("layouts/TMfooter.php");
