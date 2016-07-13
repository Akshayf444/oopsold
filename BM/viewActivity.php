<?php
session_start();
if (!isset($_SESSION['BM'])) {
    header("Location:login.php");
}
require_once( "../includes/initialize.php");
$pageTitle = "View Activity Details";
require_once( "../includes/class.activity_master.php");

$activity_count = 0;
$output = '';
$bm_empid = $_SESSION['BM'];

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
    $sql = "SELECT act.* FROM `activity_details` act 
            INNER JOIN `employees` e ON e.`empid`= act.empid 
            INNER JOIN bm ON bm.`bm_empid` = e.`bm_empid` WHERE bm.`bm_empid` = '$bm_empid' AND act.activity_type ='{$_POST['activity_type']}' AND month(act.activity_date) = '{$_POST['month']}' ";
    $Activitys = QueryWrapper::executeQuery($sql);
    $sql = "SELECT count(*) FROM `activity_details` act 
            INNER JOIN `employees` e ON e.`empid`= act.empid 
            INNER JOIN bm ON bm.`bm_empid` = e.`bm_empid` WHERE bm.`bm_empid` = '$bm_empid' AND act.activity_type ='{$_POST['activity_type']}' AND month(act.activity_date) = '{$_POST['month']}' ";
    $activity_count = Activity::returnCount($sql);
} else {
    $sql = "SELECT act.* FROM `activity_details` act 
            INNER JOIN `employees` e ON e.`empid`= act.empid 
            INNER JOIN bm ON bm.`bm_empid` = e.`bm_empid` WHERE bm.`bm_empid` = '$bm_empid' ";
    $Activitys = QueryWrapper::executeQuery($sql);
    $sql = "SELECT count(*) FROM `activity_details` act 
            INNER JOIN `employees` e ON e.`empid`= act.empid 
            INNER JOIN bm ON bm.`bm_empid` = e.`bm_empid` WHERE bm.`bm_empid` = '$bm_empid' ";
    $activity_count = Activity::returnCount($sql);
}
require_once("BMheader.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">View Activity Details</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row ">
    <form action="" method="post">
        <div class="col-lg-4">
            <select name="activity_type" class="form-control "  required id="activity">
                <?php
                if (isset($_POST['activity_type'])) {
                    echo activityList($_POST['activity_type']);
                } else {
                    echo activityList();
                }
                ?>

            </select>
        </div>      <!-- /.col-lg-12 -->

        <div class="col-lg-4">
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
        <div class="col-lg-4">
            <?php
            if (isset($activity_count)) {
                echo '<label>Activity Count : </label> ' . $activity_count;
            }
            ?>
        </div>
    </form>
</div>
<div class="row row-margin-top">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive">
        <table class="table table-bordered table-hover " id="items" >
            <tr>
                <th>Activity Type</th>
                <th>Activity Date</th>
                <th>Doctor Name</th>
                <th>MSL Code</th>
                <th>Expenses</th>
                <th>Total Business</th>
            </tr>
            <?php
            $page_count = 1;
            if (!empty($Activitys)) {
                foreach ($Activitys as $Activity) {
                    ?>
                    <tr>
                        <td><?php
                            $ActName = ActivityMaster::find_by_id($Activity->activity_type);
                            echo isset($ActName->activity) ? $ActName->activity : $Activity->activity_type;
                            ?>
                        </td>
                        <td><?php echo date('d-m-Y', strtotime($Activity->activity_date)); ?></td>
                        <td><?php
                            $doctor_name = Doctor::find_by_docid($Activity->doc_id);
                            echo $doctor_name->name;
                            ?>
                        </td>
                        <td><?php
                            $BasicProfile = BasicProfile::find_by_docid($Activity->doc_id);
                            echo (isset($BasicProfile->msl_code)) ? $BasicProfile->msl_code : "-"
                            ?>
                        </td>
                        <td><?php echo $Activity->expances; ?></td>
                        <td>
                            <?php
                            echo $Activity->total;
                            ?>
                        </td>
                        <td><a id="<?php echo $Activity->act_id; ?>" href="#"  class="btn btn-success btn-xs " >View Details</a></td>                    </tr>
                    <?php
                    $page_count++;
                }
            } else {
                echo 'Details Not Found';
            }
            ?>
        </table>
    </div>
</div>
<div id="modalpopup"></div>
<script>
    $('.btn').click(function () {
        var id = $(this).attr('id');
        $.ajax({
            type: 'POST',
            data: {act_id: id},
            url: 'viewActivityDetails.php',
            success: function (data) {
                $("#modalpopup").html(data);
            }
        });
    });

</script>
<?php require_once("BMfooter.php"); ?>