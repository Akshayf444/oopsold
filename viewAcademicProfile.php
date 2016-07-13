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
$total_count = AcaProfile::count_all($empid);

$pagination = new Pagination($page, $per_page, $total_count);

$sql = "SELECT dp.* FROM doc_academic_profile dp INNER JOIN doctors d ON d.docid = dp.docid  WHERE d.is_delete = 0 AND dp.empid = '$empid' ";
$sql .= "LIMIT {$per_page} ";
$sql .= "OFFSET {$pagination->offset()}";

$academicProfiles = AcaProfile::find_by_sql($sql);
$academicProfile = !empty($academicProfiles) ? array_shift($academicProfiles) : FALSE;
$doctorName = Doctor::find_by_docid($academicProfile->docid);

$pageTitle = "View Academic Profile";
require_once("layouts/TMheader.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $doctorName->name . " "; ?><small>Academic Profile</small></h1>
    </div>      
</div>
<div class ="row">

    <div class="col-lg-12" style="clear: both;">
        <?php
        if ($pagination->total_pages() > 1) {

            if ($pagination->has_previous_page()) {
                echo "<div class='col-lg-1'> <a class ='btn btn-default' href=\"viewAcademicProfile.php?page=";
                echo $pagination->previous_page();
                echo "\">&laquo; Previous</a></div> ";
            }

            echo '<div class="col-lg-10" style = "text-align:center">';
            for ($i = 1; $i <= $pagination->total_pages(); $i++) {
                if ($i == $page) {
                    echo " <span class=\"selected\"  style='color:red;padding:1px;font-weight:bold;border:1px solid aqua;radius:1px;'>{$i}</span> ";
                } else {
                    echo " <a href=\"viewAcademicProfile.php?page={$i}\">{$i}</a> ";
                }
            }
            echo '</div>';

            if ($pagination->has_next_page()) {
                echo "<div class ='col-lg-1 pull-right'> <a class ='btn btn-default' href=\"viewAcademicProfile.php?page=";
                echo $pagination->next_page();
                echo "\">Next &raquo;</a> </div>";
            }
        }
        ?>
    </div>
</div>
<style>
    .dl-horizontal dt {
        width: 240px;
    }
    .dl-horizontal dd {
        margin-left: 250px;
    }
</style>
<div class ="row row-margin-top col-lg-12">
    <table cellspacing="0" class="table table-bordered">

        <tr>
            <td>Preferred Academic Media:</td>
            <td>
                <?php echo $academicProfile->media; ?>
            </td>
        </tr>
        <tr>
            <td>Scientific Journal:</td>
            <td>
                <?php echo $academicProfile->journal; ?>
            </td>
        </tr>

        <tr>
            <td>Online Subscriptions:</td>
            <td>
                <?php echo $academicProfile->subscription; ?>
            </td>
        </tr>
        <tr>
            <td>Interest in Patient Education Materials</td>	
            <td> <?php echo $academicProfile->materials; ?></td>
        </tr>
        <tr>
            <td>Activities</td>	
            <td><?php echo $academicProfile->activities; ?></td>
        </tr>
        </td>
        </tr>

        <tr>
            <th>Professional Association</th>
            <td>
        <tr>
            <td>Local:</td>
            <td> <?php echo $academicProfile->local; ?></td>
        </tr>
        <tr>
            <td>International :</td>
            <td><?php echo $academicProfile->intern; ?></td>

        </tr>

        </tr>
    </table>
</div>
<hr>
<div class="row" style="padding:1em">
    <a href="editAcademicProfile.php?docid=<?php echo $academicProfile->docid; ?>" > <input type="button" value="Edit Profile" title="Edit Profile" style="display:<?php
        if ($employee->lock_academic == 1) {
            echo "none";
        }
        ?>" class="btn btn-primary" /></a><br/>
</div>
<?php
require_once("layouts/TMfooter.php");
