<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$pageTitle = "View Activity Details";
require_once(dirname(__FILE__) . "/includes/class.activity_master.php");

$activity_count = 0;
$output = '';

function activityList($id = "") {
    $Activities = ActivityMaster::find_all();
    $output .= '<option value ="0" >Select Activity</option>';
    foreach ($Activities as $Activity) {
        if ($Activity->id == $id) {
            $output .='<option value ="' . $Activity->id . '" selected >' . $Activity->activity . '</option>';
        } else {
            $output .='<option value ="' . $Activity->id . '" >' . $Activity->activity . '</option>';
        }
    }
    return $output;
}

if (isset($_POST['month']) && isset($_POST['activity_type']) && $_POST['activity_type'] != 0) {
    $Activitys = Activity::find_by_month($_SESSION['employee'], $_POST['activity_type'], $_POST['month']);
    $activity_count = Activity::count_by_month($_SESSION['employee'], $_POST['activity_type'], $_POST['month']);
} else {
    $Activitys = Activity::find_by_empid($_SESSION['employee']);
    $activity_count = Activity::count_by_empid($_SESSION['employee']);
}
require_once("layouts/TMheader.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">View Activity Details</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row ">
    <form action="" method="post">
        <div class="col-lg-4 col-xs-6">
            <select name="activity_type" class="form-control " required id="activity">
                <?php
                if (isset($_POST['activity_type'])) {
                    echo activityList($_POST['activity_type']);
                } else {
                    echo activityList();
                }
                ?>

            </select>
        </div>      <!-- /.col-lg-12 -->

        <div class="col-lg-4 col-xs-6">
            <select class="form-control" id="month" onchange="this.form.submit()" name="month">
                <option value="">Select Month</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $month = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
                    if (isset($_POST['month']) && $_POST['month'] == $m) {
                        ?>
                        <option value="<?php echo $m ?>" selected ><?php echo $month ?> </option>';
                    <?php } else { ?>
                        <option value="<?php echo $m ?>"  ><?php echo $month ?> </option>';
                        <?php
                    }
                }
                ?>
            </select>
        </div> 
        <div class="col-lg-4 col-xs-12">
            <?php
            if (isset($activity_count)) {
                echo '<label>Activity Count : </label> ' . $activity_count;
            }
            ?>
        </div>
    </form>
</div>
<div class="row row-margin-top">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 " id="no-more-tables">
        <table class="table table-bordered table-hover " id="items" >
            <thead>
                <tr>
                    <th>Activity Type</th>
                    <th>Activity Date</th>
                    <th>Doctor Name</th>
                    <th>MSL Code</th>
                    <th>Expenses</th>
                    <th>Total Business</th>
                </tr>
            </thead>
            <?php
            $page_count = 1;
            foreach ($Activitys as $Activity) {
                ?>
                <tr>
                    <td data-title="Activity Type"><?php
                        $ActName = ActivityMaster::find_by_id($Activity->activity_type);
                        echo isset($ActName->activity) ? $ActName->activity : $Activity->activity_type;
                        ?>
                    </td>
                    <td data-title="Activity Date"><?php echo date('d-m-Y', strtotime($Activity->activity_date)); ?></td>
                    <td data-title="Doctor Name"><?php
                        $doctor_name = Doctor::find_by_docid($Activity->doc_id);
                        echo $doctor_name->name;
                        ?>
                    </td>
                    <td data-title="MSL Code"><?php
                        $BasicProfile = BasicProfile::find_by_docid($Activity->doc_id);
                        echo (isset($BasicProfile->msl_code)) ? $BasicProfile->msl_code : "-"
                        ?>
                    </td>
                    <td data-title="Expanses"><?php echo $Activity->expances; ?></td>
                    <td data-title="Total Business">
                        <?php
                        echo $Activity->total;
                        ?>
                    </td>
                    <td><a href="viewActivityDetails.php?act_id=<?php echo $Activity->act_id; ?>&page=<?php echo $page_count; ?>"  class="btn btn-success btn-xs ">View Details</a></td>
                </tr>
                <?php
                $page_count++;
            }
            ?>
        </table>
    </div>
</div>
<?php require_once("layouts/TMfooter.php"); ?>